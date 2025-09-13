<?php

namespace App\Model\Download\Superadmin\Finance\Report;

use App\Model\Union;
use Illuminate\Http\Request;

class SummaryDownload
{
  public function download()
  {
     $rows = Union::with('upazila.district')
                  ->with('financeDatas')
                  ->orderBy('distid')
                  ->orderBy('upid')
                  ->get();

    \Excel::create('Report-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'District',
          'Upazila',
          'Union',
          'Total Income (Tk.)',
          'Total Expenses (Tk.)',
          'Balance (Tk.)',
          'Last Transaction Date'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
            $income = $row->financeDatas()->where('trans_type', 'in')->sum('amount');
            $exp = $row->financeDatas()->where('trans_type', 'ex')->sum('amount');
            $date = count($row->financeDatas) ? $row->financeDatas()->orderBy('date', 'DESC')->first()->date : "";

            if($date != ""){
              $date = date('d-m-Y', strtotime($date));
            }else{
              $date = "";
            }

          $sheet->row($rowIndex, [
            $row->upazila->district->distname ,
            $row->upazila->upname ,
            $row->unname ,
            $income ,
            $exp ,
            ($income-$exp) ,
            $date
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}