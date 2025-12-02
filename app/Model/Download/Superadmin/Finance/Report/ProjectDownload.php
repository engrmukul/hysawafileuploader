<?php

namespace App\Model\Download\Superadmin\Finance\Report;

use App\Model\Demand;
use App\Model\FinanceData;
use App\Model\Head;
use App\Model\Item;
use App\Model\ItemBudget;
use App\Model\Project;
use App\Model\SubHead;
use Illuminate\Http\Request;

class ProjectDownload
{
  public function download($request)
  {
    \Excel::create('Report-'.time(), function($excel) use($request) {
      $excel->sheet('Sheetname', function($sheet) use($request) {

        $sheet->setOrientation('landscape');

        $project         = Project::where('id', $request->input('project_id'))->first();
        $demands         = Demand::orderBy('item')->get();
        $itemBudgets     = ItemBudget::where('distid', $request->input('district_id'))->get();
        $financeDataMain = FinanceData::where('proid', $request->input('project_id'))->get();


        $arrayIndex = 1;
        $sheet->row($arrayIndex++, array(
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
          ));



        $financeData2 = FinanceData::where('proid', $request->project_id)
          ->where(function($q) use ($request){
            if($request->starting_date != ""){
              $q->where('date', '<=', $request->starting_date);
            }
          })->get();

        $income_bankob = $financeData2->where('mode', 'bank')->where('trans_type', 'in')->sum('amount');
        $ex_bankob     = $financeData2->where('mode', 'bank')->where('trans_type', 'ex')->sum('amount');
        $bincomeob     = $income_bankob-$ex_bankob;
        $incom_cashob  = $financeData2->where('mode', 'cash')->where('trans_type', 'in')->sum('amount');
        $ex_cashob     = $financeData2->where('mode', 'cash')->where('trans_type', 'ex')->sum('amount');

        $cincomeob     = $incom_cashob - $ex_cashob;
        $open_balance  = $cincomeob + $bincomeob;

        $sheet->row($arrayIndex++, array(''. ''. 'Opening Balance', $open_balance));
        $sheet->row($arrayIndex++, array(''. ''. 'Cash in Hand', $cincomeob));
        $sheet->row($arrayIndex++, array(''. ''. 'Cash at Bank', $bincomeob));

        $budget_gr   = 0;
        $income_gr   = 0;
        $ex_gr       = 0;
        $cumin_gr    = 0;
        $cumex_gr    = 0;
        $balance_gr  = 0;
        $demand_gr   = 0;

        $heads = Head::where('id', '!=', 11)->where('id', '!=', 15)->get();


        foreach($heads as $head)
        {
          $budget_total = 0;
          $income_total = 0;
          $ex_total     = 0;
          $cumin_total  = 0;
          $cumex_total  = 0;
          $balance_total= 0;
          $demand_total = 0;

          $sheet->row($arrayIndex++, array($head->headname));
          $subHeads = SubHead::where('headid', $head->id)->get();

          foreach($subHeads as $subHead)
          {
            $sheet->row($arrayIndex++, array('', $subHead->sname));
            $items = Item::where('subid', $subHead->id)->get();

            foreach($items as $item)
            {
              $financeDataSet = FinanceData::where('head', $head->id)
                ->where('subhead', $subHead->id)
                ->where('item', $item->id)
                ->where(function($q) use ($request){

                  if($request->starting_date !="" && $request->ending_date != "")
                  {
                    $q->where('date', '<=', $request->ending_date)
                      ->where('date', '>=', $request->starting_date);
                  }
                })->take(1)->get();

              if(!count($financeDataSet))
              {
                continue;
              }

              foreach($financeDataSet as $fd)
              {
                $itemBudgetAmount = $itemBudgets->where('itemid', $fd->item)->sum('budget');
                $demandAmount     = $demands ? $demands->where('item', $fd->item)->sum('amount') : 0;

                $budget_total     = $budget_total+ $itemBudgetAmount;
                $demand_total     = $demand_total+$demandAmount;

                $income     = $financeDataMain->where('item', $fd->item)
                                              ->where('trans_type', 'in')
                                              ->where('date', '<=', $request->ending_date)
                                              ->where('date', '>=', $request->starting_date)
                                              ->sum('amount');
                $income_total = $income_total + $income;

                $expense    = $financeDataMain->where('item', $fd->item)
                                              ->where('trans_type', 'ex')
                                              ->where('date', '<=', $request->ending_date)
                                              ->where('date', '>=', $request->starting_date)
                                              ->sum('amount');
                $ex_total = $ex_total + $expense;

                $cumIncome = $financeDataMain->where('item', $fd->item)
                                              ->where('trans_type', 'in')
                                              ->where('date', '<=', $request->ending_date)
                                              ->sum('amount');
                $cumin_total=$cumin_total + $cumIncome;

                $cumExpense = $financeDataMain->where('item', $fd->item)
                                              ->where('trans_type', 'ex')
                                              ->where('date', '<=', $request->ending_date)
                                              ->sum('amount');
                $cumex_total=$cumex_total + $cumExpense;

                $budget_balance = $itemBudgetAmount - $cumExpense;
                $balance_total = $balance_total + $budget_balance;

                $sheet->row($arrayIndex++, array('', '', $item->itemname,
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

          $sheet->row($arrayIndex++, array('', '', 'Subtotal',
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

        $sheet->row($arrayIndex++, array('', '', 'Grand Total:',
                  $budget_gr,
                  $income_gr,
                  $ex_gr,

                  $cumin_gr,
                  $cumex_gr,
                  $balance_gr,
                  $demand_gr
                ));

        $financeData = FinanceData::where('proid', $request->project_id)
          ->where(function($q) use ($request) {
            if($request->ending_date != ""){
              $q->where('date', '<=', $request->ending_date);
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

        $sheet->row($arrayIndex++, array('Closing Balance', $closing));
        $sheet->row($arrayIndex++, array('Cash in Hand', $income_cash));
        $sheet->row($arrayIndex++, array('Cash at Bank', $bincome));

        });
    })->download('csv');
  }
}

