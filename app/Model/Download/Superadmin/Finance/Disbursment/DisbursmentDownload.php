<?php

namespace App\Model\Download\Superadmin\Finance\Disbursment;

use Illuminate\Http\Request;

class DisbursmentDownload
{
  public function download()
  {
     $rows = "";

    \Excel::create('Finance-Disbursment-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(

          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->head ,

          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}