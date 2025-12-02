<?php

namespace App\Model\Download\Superadmin\Project\Sanitation;

use Illuminate\Http\Request;

class FieldVisitDownload
{
  public function download()
  {
     $rows = \DB::table('tour')->get();

    \Excel::create('Monitoring-Field-Visit-Report-List-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {

        $sheet->setOrientation('landscape');

        $sheet->row(1, array(
          'Name of Visitor',
          'Designation of visitor',
          'Date of Visit From',
          'Date of Visit To',
          'Place of Visit',
          'Pupose/Objective(s)',
          'Findings/Observation(s)',
          'People Person met',
          'Agreed Action Points/Recommendation(s)',
          'Follow-up actions',
          'Supervisor\'s Feedback',
          'Annex',
          'Signature and Date'
        ));

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
              $row->name,
              $row->des,
              $row->fdate,
              $row->tdate,
              $row->place,
              $row->porpose,
              $row->findings,
              $row->peoplemet,
              $row->recomandation,
              $row->followup,
              $row->feedback,
              $row->anext,
              $row->sign,
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}