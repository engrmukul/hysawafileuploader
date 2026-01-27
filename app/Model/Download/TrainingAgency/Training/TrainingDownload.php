<?php

namespace App\Model\Download\TrainingAgency\Training;

use Illuminate\Http\Request;

class TrainingDownload
{
  public function download()
  {
     $rows = \DB::table('tbl_training')
                        ->leftJoin('trg_title', 'trg_title.id', '=', 'tbl_training.TrgTitle')
                        ->leftJoin('tbl_venue', 'tbl_venue.VenueID', '=', 'tbl_training.TrgVenue')
                        ->leftJoin('trg_agency', 'trg_agency.id', '=', 'tbl_training.Agency')
                        ->where('proj_id', auth()->user()->proj_id)
                        ->get();

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
            $row->agency_name ,
            $row->title ,
            $row->VenueName ,
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