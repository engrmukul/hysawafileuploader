<?php

namespace App\Model\Download;

use App\HandwashSites;
use App\Model\Search\Request\HandwashSitesSearchRequest;
use Illuminate\Http\Request;

class HandwashSitesDownload
{
  private $waters;
  private $request;

  public function __construct($waters)
  {
    $this->waters = $waters;
  }

  public function download()
  {
    $rows = $this->waters;

    if(!count($rows))
    {
      return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    \Excel::create('Hand_Wash_Sites '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');

        $sheet->row(1, array(
          'Site ID',
          'Region',
          'Project',
          'District',
          'Upazila',
          'Union',
          'Word No',
          'Address Details',
          'Apprv. status',
          'Apprv. date',
          'Imp. status',
          'Compl. Year',
          'Caretaker Name',
          'Caretaker Phone',
          'Lat',
          'Lon',
          'MaleBeneficiary',
          'Female Beneficiary',
          'created_by',
          'updated_by',
          'created_at',
          'updated_at'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
            if(auth()->user()->region_id == '24' or auth()->user()->region_id == '2'){
                $project_name = "HYSAWA German Project";
            } else {
                $project_name = $row->project;
            }
          $sheet->row($rowIndex, [
            $row->id,
            $row->region_name,
            $project_name,
            $row->distname,
            $row->upname,
            $row->unname,
            $row->ward_no,
            $row->address,
            $row->app_status,
            $row->app_date,
            $row->imp_status,
            $row->completion_year,
            $row->caretaker_name,
            $row->caretaker_phone,
            $row->lat,
            $row->lon,
            $row->beneficiary_male,
            $row->beneficiary_female,
            $row->created_by,
            $row->updated_by,
            $row->created_at,
            $row->updated_at
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
