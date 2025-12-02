<?php

namespace App\Model\Download\Superadmin\Finance\Report;

use App\Fhead;
use App\Model\FinanceData;
use App\Model\Head;
use Illuminate\Http\Request;

class ConsolidatedDownload
{
  public function download(Request $request)
  {
    \Excel::create('Finance-Report-Consolidated-'.time(), function($excel) use($request) {
      $excel->sheet('Sheetname', function($sheet) use($request) {

        $sheet->setOrientation('landscape');
        $rowIndex = 1;

        $sheet->row($rowIndex++, array(
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

        $data = \DB::select(\DB::raw("
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
              DATE < '".$request->input('starting_date')."'
          ) AS t"));
        $data2 = \DB::select(\DB::raw("
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
              DATE < '".$request->input('ending_date')."'
          ) AS t"));

        if(count($data)){
           $data= $data[0];
        }
        if(count($data2)){
           $data2= $data2[0];
        }

        $d = Fhead::with('subhead.subItem')->get()->toArray();

        $sheet->row($rowIndex++, array('', '', 'Opening Balance', $data->total));
        $sheet->row($rowIndex++, array('', '', 'Cash in Hand', $data->opening_cash));
        $sheet->row($rowIndex++, array('', '', 'Cash at Bank', $data->opening_bank));

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

          $sheet->row($rowIndex++, array($list["headname"]));

          if (count($list["subhead"]) > 0)
          {
            foreach($list["subhead"] as $sublist)
            {
              $sheet->row($rowIndex++, array('', $sublist["sname"]));

              if (count($sublist["sub_item"]) > 0)
              {
                foreach($sublist["sub_item"] as $sub_item)
                {
                  $data11 = \DB::table('item_budget')
                      ->select(\DB::raw('SUM(budget) as budget'))
                      ->where('itemid', $sub_item["id"])
                      ->first();
                  $budget+=$cbudget=$data11->budget;

                  $data12 = \DB::table('fdata')
                    ->select(\DB::raw('SUM(amount) as cIncome'))
                    ->where('item',$sub_item["id"])
                    ->where('trans_type','in')
                    ->whereBetween('date', array($request->starting_date, $request->ending_date))
                    ->first();
                  $currentIncome+=$ccurrentIncome=$data12->cIncome;

                  $data13 = \DB::table('fdata')
                    ->select(\DB::raw('SUM(amount) as cExpenditure'))
                    ->where('item',$sub_item["id"])
                    ->where('trans_type','ex')
                    ->whereBetween('date', array($request->starting_date, $request->ending_date))
                    ->first();
                  $currentExpenditure+=$ccurrentExpenditure = $data13->cExpenditure;

                  $data14 = \DB::table('fdata')
                    ->select(\DB::raw('SUM(amount) as cIncome'))
                    ->where('item',$sub_item["id"])
                    ->where('trans_type','in')
                    ->whereDate('date','<=',$request->ending_date)
                    ->first();
                  $comulativeIncome+=$ccomulativeIncome=$data14->cIncome;

                  $data15 = \DB::table('fdata')
                    ->select(\DB::raw('SUM(amount) as cExpenditure'))
                    ->where('item',$sub_item["id"])
                    ->whereDate('date','<=',$request->ending_date)
                    ->where('trans_type','ex')
                    ->first();
                  $comulativeExpenditure+=$ccomulativeExpenditure=$data15->cExpenditure;

                  $comulativeBudget += $ccomulativeBudget = ($data11->budget - $data15->cExpenditure) ;

                  $data16 = \DB::table('demand')
                    ->select('amount')
                    ->where('item', $sub_item["id"])
                    ->first();
                  $demand += @$cdemand= $data16->amount;

                  $sheet->row($rowIndex++, array(
                    '',
                    '',
                    $sub_item["itemname"],
                    $cbudget,
                    $ccurrentIncome,
                    $ccurrentExpenditure,
                    $ccomulativeIncome,
                    $ccomulativeExpenditure,
                    $ccomulativeBudget,
                    $cdemand
                  ));
                }
              }
            }

            $sheet->row($rowIndex++, array(
              '',
              '',
              'Subtotal',
//              '',
              $budget,
              $currentIncome,
              $currentExpenditure,
              $comulativeIncome,
              $comulativeExpenditure,
              $comulativeBudget,
              $demand
            ));

            $gbudget+=$budget;
            $gcurrentIncome+=$currentIncome;
            $gcurrentExpenditure+=$currentExpenditure;
            $gcomulativeIncome+=$comulativeIncome;
            $gcomulativeExpenditure+=$comulativeExpenditure;
            $gcomulativeBudget+=$comulativeBudget;
            $gdemand+=$demand+=$cdemand;
          }
        }

        $sheet->row($rowIndex++, array(
          '',
          '',
          'Grand Total:',
//          '',
          $gbudget,
          $gcurrentIncome,
          $gcurrentExpenditure,
          $gcomulativeIncome,
          $gcomulativeExpenditure,
          $gcomulativeBudget,
          $gdemand
        ));

        $sheet->row($rowIndex++, array('', '', 'Closing Balance', $data2->total ));
        $sheet->row($rowIndex++, array('', '', 'Cash in Hand', $data2->opening_cash ));
        $sheet->row($rowIndex++, array('', '', 'Cash at Bank', $data2->opening_bank ));

      });
    })->download('csv');
  }
}