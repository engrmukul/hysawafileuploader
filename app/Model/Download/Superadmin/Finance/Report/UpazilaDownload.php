<?php

namespace App\Model\Download\Superadmin\Finance\Report;

use App\Model\Bank;
use App\Model\Demand;
use App\Model\FinanceData;
use App\Model\Head;
use App\Model\Item;
use App\Model\ItemBudget;
use App\Model\SubHead;
use App\Model\Union;
use Illuminate\Http\Request;

class UpazilaDownload
{
  public function download(Request $request)
  {
    $UNIds = Union::where('upid', $request->upazila_id)->get(['id']);

    \Excel::create('Finance-Report-Upazila-'.time(), function($excel) use($request, $UNIds) {
      $excel->sheet('Sheetname', function($sheet) use($request, $UNIds) {

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

        $financeData2 = FinanceData::where('upid', $request->upazila_id)
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
                    ->where('ubid', $request->upazila_id)
                    ->first();
                $itemBudgetAmount = $itemBudget ? $itemBudget->budget : 0;
                $demand = Demand::where('item', $fd->item)
                    ->whereIn('unid',  $UNIds)
                    ->first();

                $demandAmount = $demand ? $demand->amount : 0;
                $budget_total=$budget_total + $itemBudgetAmount;
                $demand_total=$demand_total + $demandAmount;

                $income = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'in')
                    ->where('upid', $request->upazila_id)
                    ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date)
                        ->where('date', '>=', $request->starting_date);
                    })->get()
                    ->sum('amount');
                $income_total = $income_total + $income;

                $expense = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'ex')
                    ->where('upid', $request->upazila_id)
                     ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date)
                        ->where('date', '>=', $request->starting_date);
                    })->get()
                    ->sum('amount');
                $ex_total = $ex_total + $expense;

                $cumIncome = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'in')
                    ->where('upid', $request->upazila_id)
                    ->where(function($q) use ($request){
                      $q->where('date', '<=', $request->ending_date);
                    })
                    ->get()
                    ->sum('amount');

                $cumin_total=$cumin_total + $cumIncome;

                $cumExpense = FinanceData::where('item', $fd->item)
                    ->where('trans_type', 'ex')
                    ->where('upid', $request->upazila_id)
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

        $financeData = FinanceData::where('upid', $request->union_id)->where(function($q) use ($request){
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


      });
    })->download('csv');
  }
}