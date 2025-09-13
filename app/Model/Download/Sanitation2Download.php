<?php

namespace App\Model\Download;

use Illuminate\Http\Request;

class Sanitation2Download
{
  public function download($datas)
  {
    $rows = $datas;
    \Excel::create('Institutional Latrine Report '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'id',
          'Region',
          'Project',
          'District',
          'Upazila',
          'Union',
          'CDF_No',
          'Laatrine No',
          'Cons Type',
          'Village',
          'Main Type',
          'Sub Type',
          'Name',
          'Male Chamber',
          'Female Chamber',
          'Overhead Tank',
          'Motor Pump',
          'Water Source',
          'Sock Well',
          'Seotic Tank',
          'Tapout Side',
          'Longitude',
          'Latitude',
          'Male Ben.',
          'Fem Ben',
          'Child Ben',
          'Disb Bene',
          'Caretaker Name',
          'Caretaker Phone',
          'Ch Comittee',
          'Ch Com Tel',
          'App Date',
          'Approve ID',
          'App Status',
          'Imp. Status',
          'Created By',
          'Updated By',
          'Created At',
          'Updated At'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $region   = !empty($row->region) ? $row->region->region_name : "";
          $project  = !empty($row->project) ? $row->project->project : "";
          $district = !empty($row->district) ? $row->district->distname : "";
          $upazila  = !empty($row->upazila) ? $row->upazila->upname : "";
          $union    = !empty($row->union) ? $row->union->unname: "";

          $sheet->row($rowIndex, [
            $row->id,
            $region,
            $project,
            $district,
            $upazila,
            $union,
            $row->cdfno,
            $row->latrineno,
            $row->cons_type,
            $row->village,
            $row->maintype,
            $row->subtype,
            $row->name,
            $row->malechamber,
            $row->femalechamber,
            $row->overheadtank,
            $row->motorpump,
            $row->watersource,
            $row->sockwell,
            $row->seotictank,
            $row->tapoutside,
            $row->longitude,
            $row->latitude,
            $row->male_ben,
            $row->fem_ben,
            $row->child_ben,
            $row->disb_bene,
            $row->caretakername,
            $row->caretakerphone,
            $row->ch_comittee,
            $row->ch_com_tel,
            $row->app_date,
            $row->approve_id,
            $row->app_status,
            $row->imp_status,
            $row->created_by,
            $row->updated_by,
            $row->created_at,
            $row->updated_at
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');;
  }
}
