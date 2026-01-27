<?php

namespace App\Model\Download\TrainingAgency\Training;

use Illuminate\Http\Request;

class SummaryDownload
{
  public function download($type)
  {
    return "";

    $rows = "";

    \Excel::create('Training-Summary-'.time(), function($excel) use($rows) {
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