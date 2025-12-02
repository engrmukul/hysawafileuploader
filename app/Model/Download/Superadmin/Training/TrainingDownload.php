<?php

namespace App\Model\Download\Superadmin\Training;

use Illuminate\Http\Request;

class TrainingDownload
{
  public function download()
  {
     $rows = \DB::table('tbl_training')->get();

    \Excel::create('Training-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'Training Agency / Organized by',
          'Training Title',
          'Venue',
          'Date From',

          'Date To',
          'Participant type',
          'Number of participant',
          'Batch no',

          'Status'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->Agency ,
            $row->TrgTitle ,
            $row->TrgVenue ,
            $row->TrgFrom ,

            $row->TrgTo ,
            $row->TrgParticipantsType ,
            $row->TrgParticipantNo ,
            $row->TrgBatchNo ,

            $row->TrgStatus

          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}