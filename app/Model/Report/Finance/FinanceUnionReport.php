<?php

namespace App\Model\Report\Finance;

use App\Fhead;
use App\Model\Bank;
use App\Model\District;
use App\Model\FinanceData;
use App\Model\ItemBudget;
use App\Model\Region;
use App\Model\SubHead;
use App\Model\Union;
use App\Model\Upazila;
use App\ReportData;
use DB;
use Illuminate\Http\Request;

class FinanceUnionReport
{
  private $d;
  private $data;
  private $data2;

  private $union_id;
  private $upazila_id;
  private $district_id;

  private $starting_date;
  private $ending_date;

  private $districtName;
  private $upazilaName;
  private $unionName;

  public function __construct(Request $request)
  {
    $this->union_id = $request->union_id;
    $this->upazila_id = $request->upazila_id;
    $this->district_id = $request->district_id;

    $union = Union::with('upazila.district')->where('id', $request->input('union_id'))->first();

    $this->unionName = $union->unname;
    $this->upazilaName = $union->upazila->upname;
    $this->districtName = $union->upazila->district->distname;

    $this->d = Fhead::with('subhead.subItem')->get()->toArray();
    $this->data = \DB::select(\DB::raw("
      SELECT
        bIncome,
        bExpenditure,
        cIncome,
        cExpenditure,
        bIncome-bExpenditure AS opening_bank,
        cIncome-cExpenditure AS opening_cash,
        Income-Expenditure AS total
      FROM(
        SELECT *,
        SUM(IF(trans_type = 'in' AND MODE='bank', amount, 0)) AS 'bIncome',
        SUM(IF(trans_type = 'ex' AND MODE='bank', amount, 0)) AS 'bExpenditure',
        SUM(IF(trans_type = 'in' AND MODE='cash', amount, 0)) AS 'cIncome',
        SUM(IF(trans_type = 'ex' AND MODE='cash', amount, 0)) AS 'cExpenditure',
        SUM(IF(trans_type = 'in', amount, 0)) AS 'Income',
        SUM(IF(trans_type = 'ex', amount, 0)) AS 'Expenditure',
        SUM(amount) AS Total
        FROM
        fdata WHERE unid =".$this->union_id."
        AND
        DATE < '".$this->starting_date."'
      ) AS t"));
    $this->data2 = \DB::select(\DB::raw("
      SELECT
        bIncome,
        bExpenditure,
        cIncome,
        cExpenditure,
        bIncome-bExpenditure AS opening_bank,
        cIncome-cExpenditure AS opening_cash,
        Income-Expenditure AS total
      FROM(
        SELECT *,
        SUM(IF(trans_type = 'in' AND MODE='bank', amount, 0)) AS 'bIncome',
        SUM(IF(trans_type = 'ex' AND MODE='bank', amount, 0)) AS 'bExpenditure',
        SUM(IF(trans_type = 'in' AND MODE='cash', amount, 0)) AS 'cIncome',
        SUM(IF(trans_type = 'ex' AND MODE='cash', amount, 0)) AS 'cExpenditure',
        SUM(IF(trans_type = 'in', amount, 0)) AS 'Income',
        SUM(IF(trans_type = 'ex', amount, 0)) AS 'Expenditure',
        SUM(amount) AS Total
        FROM
          fdata
        WHERE
          unid =".$this->union_id." AND
          DATE < '".$this->ending_date."') AS t"));

      if(count($this->data))
      {
         $this->data = $data[0];
      }
      if(count($this->data2))
      {
         $this->data2= $this->data2[0];
      }
  }

  public function download()
  {
    return \Excel::create('Finance-Report-Union-'.time(), function($excel) use($request) {
      $excel->sheet('Sheetname', function($sheet) use($request) {

        $sheet->setOrientation('landscape');
        $rowIndex = 1;

        $sheet->row($rowIndex++, array(
            '',
            '',
            '',
            'Description',
            'Approved Budget(A)',
            'Current Income(B)',
            'Current Expenses(C)',
            'Cumulative Income(D)',
            'Cumulative Expenses(E)',
            'Budget Balance(F=A-E)',
            'Demand'
          )
        );

        $financeData2 = FinanceData::where('unid', $request->union_id)
        ->where(function($q) use ($request) {
            if($request->starting_date != ""){
              $q->where('date', '<=', $request->starting_date);
            }
        })->get();

        $income_bankob = $financeData2->where('mode', 'bank')->where('trans_type', 'in')->sum('amount');
        $ex_bankob     = $financeData2->where('mode', 'bank')->where('trans_type', 'ex')->sum('amount');
        $bincomeob     = $income_bankob-$ex_bankob;
        $incom_cashob  = $financeData2->where('mode', 'cash')->where('trans_type', 'in')->sum('amount');
        $ex_cashob     = $financeData2->where('mode', 'cash')->where('trans_type', 'ex')->sum('amount');
        $cincomeob     = $incom_cashob-$ex_cashob;
        $open_balance  = $cincomeob + $bincomeob;

        $sheet->row($rowIndex++, array('', '', 'Opening Balance', $open_balance));
        $sheet->row($rowIndex++, array('', '', 'Cash in Hand', $cincomeob));
        $sheet->row($rowIndex++, array('', '', 'Cash at Bank', $bincomeob));

        $budget_gr   = 0;
        $income_gr   = 0;
        $ex_gr       = 0;
        $cumin_gr    = 0;
        $cumex_gr    = 0;
        $balance_gr  = 0;
        $demand_gr   = 0;

        $heads = Head::where('id', '!=', 11)->get();

        foreach($heads as $head)
        {
          $budget_total = 0;
          $income_total = 0;
          $ex_total     = 0;
          $cumin_total  = 0;
          $cumex_total  = 0;
          $balance_total= 0;
          $demand_total = 0;

          $sheet->row($rowIndex++, array($head->headname));

          $subHeads = SubHead::where('headid', $head->id)->get();

          foreach($subHeads as $subHead)
          {
            $sheet->row($rowIndex++, array('', $subHead->sname));

            $items = Item::where('subid', $subHead->id)->get();

            foreach($items as $item)
            {
              $financeDataSet = FinanceData::where('head', $head->id)
                ->where('subhead', $subHead->id)
                ->where('item', $item->id)
                ->where(function($q) use ($request) {
                  if($request->starting_date !="" && $request->ending_date != "")
                  {
                    $q->where('date', '<=', $request->ending_date)
                      ->where('date', '>=', $request->starting_date);
                  }
                })
                ->take(1)
                ->get();

              if(!count($financeDataSet))
              {
                continue;
              }

              foreach($financeDataSet as $fd)
              {
                $itemBudget = ItemBudget::where('itemid', $fd->item)
                    ->where('ubid', $request->union_id)
                    ->first();
                $itemBudgetAmount = $itemBudget ? $itemBudget->budget : 0;
                $demand = Demand::where('item', $fd->item)
                    ->where('unid',  $request->union_id)
                    ->first();

                $demandAmount = $demand ? $demand->amount : 0;
                $budget_total=$budget_total + $itemBudgetAmount;
                $demand_total=$demand_total + $demandAmount;

                $income = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'in')
                    ->where('unid', $request->union_id)
                    ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date)
                        ->where('date', '>=', $request->starting_date);
                    })->get()
                    ->sum('amount');
                $income_total = $income_total + $income;

                $expense = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'ex')
                    ->where('unid', $request->union_id)
                     ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date)
                        ->where('date', '>=', $request->starting_date);
                    })->get()
                    ->sum('amount');
                $ex_total = $ex_total + $expense;

                $cumIncome = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'in')
                    ->where('unid', $request->union_id)
                    ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date);
                    })
                    ->get()
                    ->sum('amount');

                $cumin_total=$cumin_total + $cumIncome;

                $cumExpense = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'ex')
                    ->where('unid', $request->union_id)
                    ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date);
                    })
                    ->get()
                    ->sum('amount');

                $cumex_total = $cumex_total + $cumExpense;
                $budget_balance = $itemBudgetAmount - $cumExpense;
                $balance_total = $balance_total+$budget_balance;

                  $sheet->row($rowIndex++, array(
                    '',
                    '',
                    $item->itemname,
                    $itemBudgetAmount,
                    $income,
                    $expense,
                    $cumIncome,
                    $cumExpense,
                    $budget_balance,
                    $demandAmount
                  ));
              }
            }
          }

          $sheet->row($rowIndex++, array(
            '',
            '',
            'Subtotal',
            '',
            $budget_total,
            $income_total,
            $ex_total,
            $cumin_total,
            $cumex_total,
            $balance_total,
            $demand_total
          ));

          $budget_gr   = $budget_total + $budget_gr;
          $income_gr   = $income_total + $income_gr;
          $ex_gr       = $ex_total + $ex_gr;
          $cumin_gr    = $cumin_total + $cumin_gr;
          $cumex_gr    = $cumex_total + $cumex_gr;
          $balance_gr  = $balance_total + $balance_gr;
          $demand_gr   = $demand_total + $demand_gr;

        }

        $sheet->row($rowIndex++, array(
            '',
            '',
            'Grand Total:',
            '',
            $budget_gr,
            $income_gr,
            $ex_gr,
            $cumin_gr,
            $cumex_gr,
            $balance_gr,
            $demand_gr
        ));

        $financeData = FinanceData::where('unid', $request->union_id)->where(function($q) use ($request){
          if($request->ending_date != ""){
            $financeData = $financeData->where('date', '<=', $request->ending_date);
          }
        })->get();

        $income_bank = $financeData->where('trans_type', 'in')->where('mode', 'bank')->sum('amount');
        $ex_bank     = $financeData->where('trans_type', 'ex')->where('mode', 'bank')->sum('amount');
        $bincome     = $income_bank-$ex_bank;
        $incom_cash  = $financeData->where('trans_type', 'in')->where('mode', 'cash')->sum('amount');
        $ex_cash     = $financeData->where('trans_type', 'ex')->where('mode', 'cash')->sum('amount');
        $cincome     = $incom_cash-$ex_cash;
        $closing     = $cincome + $bincome;
        $income_cash = $incom_cash - $ex_cash;

        $sheet->row($rowIndex++, array('', '', 'Closing Balance', $closing ));
        $sheet->row($rowIndex++, array('', '', 'Cash in Hand', $income_cash ));
        $sheet->row($rowIndex++, array('', '', 'Cash at Bank', $bincome ));

        $bankData = Bank::where('unid', $request->union_id)->orderBy('date', 'DESC')->get();
        $bankDataBalance = 0;
        $bankDataDate = "";

        if(count($bankData))
        {
          $bankData = $bankData->first();
          $bankDataBalance = $bankData->balance;
          $bankDataDate = $bankData->date;
        }

        $sheet->row($rowIndex++, array('', '', 'Bank statement','', $bankDataBalance, $bankDataDate));
      });
    })->download('csv');
  }

  public function getHead($type = 'html')
  {
    if($type == 'print') return $this->getPrintHead();

    return '<div class="col-md-12">
            <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <a href="#">District: <span style="color:purple"> '.$this->districtName.'</span></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="#">Upazila: <span style="color:purple"> '.$this->upazilaName.'</span></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="#">Union: <span style="color:purple"> '.$this->unionName.'</span></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="#">Starting Date: <span style="color:purple">'.$this->starting_date.'</span></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="#">Ending Date: <span style="color:purple">'.$this->ending_date.'</span></a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <span>District Report</span>
                </li>
            </ul>
            </div>
            </div>';

  }

  public function getBody($type = 'html')
  {
    if($type == 'print') return $this->getPrintBody();
    $str = "";
    $str .=
    '<div class="col-md-12">
      <div class="portlet light tasks-widget bordered">
        <div class="portlet-body util-btn-margin-bottom-5">

          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="example0">
              <thead class="flip-content">
                <th>Description</th>
                <th>Approved Budget(A)</th>
                <th>Current Income(B)</th>
                <th>Current Expenses(C)</th>
                <th>Cumulative Income(D)</th>
                <th>Cumulative Expenses(E</th>
                <th>Budget Balance(F=A-E)</th>
                <th>Demand</th>
              </thead>
              <tbody>
                <tr>
                  <td>Opening Balance</td>
                  <td colspan="8" style="text-align:center">'.$data->total.'</td>
                </tr>
                <tr>
                  <td>Cash in Hand</td>
                  <td colspan="8" style="text-align:center">'.$data->opening_cash.'</td>
                </tr>
                <tr>
                  <td>Cash at Bank</td>
                  <td colspan="8" style="text-align:center">'.$data->opening_bank.'</td>
                </tr>';

    $gbudget=0;
    $gcurrentIncome=0;
    $gcurrentExpenditure=0;
    $gcomulativeIncome=0;
    $gcomulativeBudget=0;
    $gcomulativeExpenditure=0;
    $gdemand=0;

    foreach($d as $list)
    {

      $budget=0;
      $currentIncome=0;
      $currentExpenditure=0;
      $comulativeIncome=0;
      $comulativeBudget=0;
      $comulativeExpenditure=0;
      $demand=0;

      $str .=
      '<tr>
        <td colspan="8" style="text-align:center">'. $list["headname"].'</td>
      </tr>';

                    if (count($list["subhead"]) > 0)
                    {
                      foreach($list["subhead"] as $sublist)
                      {

                     $str .= '<tr>
                      <td colspan="8" style="text-align:center">'. $sublist["sname"].'</td>
                    </tr>';

                    if (count($sublist["sub_item"]) > 0)
                      {
                      foreach($sublist["sub_item"] as $sub_item)
                      {

                        $budget+=$cbudget=\App\FinanceData::getBudget(
                          $sub_item["id"],
                          'ubid',
                          $this->union_id);
                        $currentIncome+=$ccurrentIncome=\App\FinanceData::currentIncome(
                          $sub_item["id"],
                          $this->starting_date,
                          $this->ending_date,
                          'unid',
                          $this->union_id);
                        $currentExpenditure+=$ccurrentExpenditure=\App\FinanceData::currentExpenditure(
                          $sub_item["id"],
                          $this->starting_date,
                          $this->ending_date,
                          'unid',
                          $this->union_id);
                        $comulativeIncome+=$ccomulativeIncome=\App\FinanceData::comulativeIncome(
                          $sub_item["id"],
                          $this->ending_date,
                          'unid',
                          $this->union_id);
                        $comulativeExpenditure+=$ccomulativeExpenditure=\App\FinanceData::comulativeExpenditure(
    $sub_item["id"],
    $this->ending_date,
    'unid',
    $this->union_id);
                        $comulativeBudget+= $ccomulativeBudget=\App\FinanceData::comulativeBudget();
                        $demand+=@$cdemand= \App\FinanceData::demand($sub_item["id"],'unid',$this->union_id);

                          $str .=
                          '<tr>
                          <td style="text-align:center">'. $sub_item["itemname"].'</td>
                          <td>'. $cbudget .'</td>
                          <td>'. $ccurrentIncome .'</td>
                          <td>'. $ccurrentExpenditure .'</td>
                          <td>'. $ccomulativeIncome .'</td>
                          <td>'. $ccomulativeExpenditure .'</td>
                          <td>'. $ccomulativeBudget .'</td>
                          <td>'. $cdemand .'</td>
                          </tr>';

                      }
                      }
                      }
                      $str .=
                      '<tr>
                        <td>Sub Total</td>
                        <td>'. $budget .'</td>
                        <td>'. $currentIncome .'</td>
                        <td>'. $currentExpenditure .'</td>
                        <td>'. $comulativeIncome .'</td>
                        <td>'. $comulativeExpenditure .'</td>
                        <td>'. $comulativeBudget .'</td>
                        <td>'. $demand .'</td>
                      </tr>';

                      $gbudget+=$budget;
                      $gcurrentIncome+=$currentIncome;
                      $gcurrentExpenditure+=$currentExpenditure;
                      $gcomulativeIncome+=$comulativeIncome;
                      $gcomulativeExpenditure+=$comulativeExpenditure;
                      $gcomulativeBudget+=$comulativeBudget;
                      $gdemand+=$demand+=$cdemand;
                      }
                      }
                      $str .=
                      '<tr>
                        <td>Grand Total</td>
                        <td>'. $gbudget .'</td>
                        <td>'. $gcurrentIncome .'</td>
                        <td>'. $gcurrentExpenditure .'</td>
                        <td>'. $gcomulativeIncome .'</td>
                        <td>'. $gcomulativeExpenditure .'</td>
                        <td>'. $gcomulativeBudget .'</td>
                        <td>'. $gdemand .'</td>
                      </tr>
                      <tr>
                        <td>Clossing Balance</td>
                        <td colspan="8" style="text-align:center">'. $this->data2->total  .'</td>
                      </tr>
                      <tr>
                        <td>Cash in Hand</td>
                        <td colspan="8" style="text-align:center">'. $this->data2->opening_cash .'</td>
                      </tr>
                      <tr>
                        <td>Cash at Bank</td>
                        <td colspan="8" style="text-align:center">'. $this->data2->opening_bank .'</td>
                      </tr>';



                        $bankStatement = \DB::table('bank')
                                            ->where('unid', $this->union_id)
                                            ->where('date', $this->ending_date)
                                            ->orderBy('date', 'DESC')
                                            ->get();
                        $flag = false;
                        if(count($bankStatement))
                        {
                            $flag = true;
                            $bankStatementAmount = $bankStatement->first()->balance;
                            $str .= '<tr>
                                <td>Bank Statement</td>
                                <td colspan="8" style="text-align:left">'. $bankStatementAmount .'</td>
                            </tr>';
                        }

                  $str .= '</tbody>
                </table>
              </div>
            </div>
          </div>
        </div>';

      return $str;
  }


  private function getPrintHead()
  {
    return
    '<table class="table table-bordered table-hover" style="width:100%">
        <tr>
            <td colspan="8" style="text-align:center">'.$this->districtName.'</td>
            <td colspan="8" style="text-align:center">'.$this->upazilaName.'</td>
            <td colspan="8" style="text-align:center">'.$this->unionName.'</td>
        </tr>
        <tr>
            <td colspan="8" style="text-align:center">'.$this->starting_date.'</td>
            <td colspan="8" style="text-align:center">'.$this->ending_date.'</td>
        </tr>
    </table>';
  }

  private function getPrintBody()
  {
    $str = '';

    $str =
    '<table class="table table-bordered table-hover" id="example0">
      <thead class="flip-content">
          <th>Description</th>
          <th>Approved Budget(A)</th>
          <th>Current Income(B)</th>
          <th>Current Expenses(C)</th>
          <th>Cumulative Income(D)</th>
          <th>Cumulative Expenses(E</th>
          <th>Budget Balance(F=A-E)</th>
          <th>Demand</th>
      </thead>
      <tbody>
        <tr><td>Opening Balance</td><td colspan="8" style="text-align:center">'.$this->data->total.'</td></tr>
        <tr><td>Cash in Hand</td><td colspan="8" style="text-align:center">'.$this->data->opening_cash.'</td></tr>
        <tr><td>Cash at Bank</td><td colspan="8" style="text-align:center">'.$this->data->opening_bank.'</td></tr>';

        $gbudget=0;
        $gcurrentIncome=0;
        $gcurrentExpenditure=0;
        $gcomulativeIncome=0;
        $gcomulativeBudget=0;
        $gcomulativeExpenditure=0;
        $gdemand=0;

        foreach($this->d as $list)
        {
          $budget=0;
          $currentIncome=0;
          $currentExpenditure=0;
          $comulativeIncome=0;
          $comulativeBudget=0;
          $comulativeExpenditure=0;
          $demand=0;

          $str = $str .'<tr><td colspan="8" style="text-align:center">'.$list["headname"].'</td></tr>';

          if (count($list["subhead"]) > 0)
          {

            foreach($list["subhead"] as $sublist)
            {
              $str = $str .'<tr><td colspan="8" style="text-align:center">'.$sublist["sname"].'</td></tr>';

              if (count($sublist["sub_item"]) > 0)
              {
                foreach($sublist["sub_item"] as $sub_item)
                {
                  $budget+=$cbudget=\App\FinanceData::getBudget($sub_item["id"],'ubid',$this->union_id);
                  $currentIncome+=$ccurrentIncome=\App\FinanceData::currentIncome($sub_item["id"],$this->starting_date,$this->ending_date,'unid',$this->union_id);
                  $currentExpenditure+=$ccurrentExpenditure=\App\FinanceData::currentExpenditure($sub_item["id"],$this->starting_date,$this->ending_date,'unid',$this->union_id);
                  $comulativeIncome+=$ccomulativeIncome=\App\FinanceData::comulativeIncome($sub_item["id"],$this->ending_date,'unid',$this->union_id);
                  $comulativeExpenditure+=$ccomulativeExpenditure=\App\FinanceData::comulativeExpenditure($sub_item["id"],$this->ending_date,'unid',$this->union_id);
                  $comulativeBudget+= $ccomulativeBudget=\App\FinanceData::comulativeBudget();
                  $demand+=@$cdemand= \App\FinanceData::demand($sub_item["id"],'unid',$this->union_id);

                  $str = $str .'<tr>
                      <td style="text-align:center">'.$sub_item["itemname"].'</td>
                      <td>'.$cbudget .'</td>
                      <td>'.$ccurrentIncome .'</td>
                      <td>'.$ccurrentExpenditure .'</td>
                      <td>'.$ccomulativeIncome .'</td>
                      <td>'.$ccomulativeExpenditure .'</td>
                      <td>'.$ccomulativeBudget .'</td>
                      <td>'.$cdemand .'</td>
                  </tr>';
                }
              }
            }
            $str = $str .'<tr>
                <td>Sub Total</td>
                <td>'.$budget.'</td>
                <td>'.$currentIncome.'</td>
                <td>'.$currentExpenditure.'</td>
                <td>'.$comulativeIncome.'</td>
                <td>'.$comulativeExpenditure.'</td>
                <td>'.$comulativeBudget.'</td>
                <td>'.$demand.'</td>
            </tr>';

            $gbudget+=$budget;
            $gcurrentIncome+=$currentIncome;
            $gcurrentExpenditure+=$currentExpenditure;
            $gcomulativeIncome+=$comulativeIncome;
            $gcomulativeExpenditure+=$comulativeExpenditure;
            $gcomulativeBudget+=$comulativeBudget;
            $gdemand+=$demand+=$cdemand;

          }
        }
        $str = $str .'<tr>
            <td>Grand Total</td>
            <td>'.$gbudget.'</td>
            <td>'.$gcurrentIncome.'</td>
            <td>'.$gcurrentExpenditure.'</td>
            <td>'.$gcomulativeIncome.'</td>
            <td>'.$gcomulativeExpenditure.'</td>
            <td>'.$gcomulativeBudget.'</td>
            <td>'.$gdemand.'</td>
        </tr>
        <tr><td>Clossing Balance</td><td colspan="8" style="text-align:center">'.$this->data2->total .'</td></tr>
        <tr><td>Cash in Hand</td><td colspan="8" style="text-align:center">'.$this->data2->opening_cash.'</td></tr>
        <tr><td>Cash at Bank</td><td colspan="8" style="text-align:center">'.$this->data2->opening_bank.'</td></tr>';

    $bankStatement = \DB::table('bank')
                        ->where('unid', $this->union_id)
                        ->where('date', $this->ending_date)
                        ->orderBy('date', 'DESC')
                        ->get();

    if(count($bankStatement))
    {
      $bankStatementAmount = $bankStatement->first()->balance;
      $str = $str .'<tr><td>Bank Statement</td><td colspan="8" style="text-align:left">'.$bankStatementAmount.'</td></tr>';
    }

    $str = $str .'</tbody>
    </table>';

    return $str;
  }
}
