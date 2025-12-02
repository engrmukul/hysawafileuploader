<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class WQTestsV2Download
{
  private $rows;
  private $request;

  public function __construct($rows)
  {
    $this->waters = $rows;
  }

  public function download()
  {
    $rows = $this->waters;

    if(!count($rows))
    {
      return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    \Excel::create('SafePani Water Quality Test Results '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'Infrastructure ID',
          'Infrastructure Type',
          'School Name',
          'Union',
          'Upazila',
          'District',
          'Sample ID',
          'Sample Type',
          'Parameter',
          'Value',
          'Quarter',
          'Year',
          'Sampling Date',
          'Test Date',
          'Verify Date',
          'Notify Date',
          'SMS Date',
          'Reporting Status',
          'Action Status',
          'Actively Managed'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
            if ($row->infrastructure->tech_type == 'DTW') {
                $water_type = 'Deep Tubewell';
            } else if ($row->infrastructure->tech_type == 'STW') {
                $water_type = 'Shallow Tubewell';
            } else if ($row->infrastructure->tech_type == 'RWH') {
                $water_type = 'Rainwater Harvesting System';
            } else if ($row->infrastructure->tech_type == 'MAR') {
                $water_type = 'Managed Aquifer Recharge';
            } else if ($row->infrastructure->tech_type == 'PWS') {
                $water_type = 'Piped Water';
            } else if ($row->infrastructure->tech_type == 'PSF') {
                $water_type = 'Pond Sand Filter';
            } else if ($row->infrastructure->tech_type == 'SWDU') {
                $water_type = 'Solar Water Desalination Unit';
            } else if ($row->infrastructure->tech_type == 'RO') {
                $water_type = 'Reverse Osmosis';
            } else {
                $water_type = 'Unknown';
            }


             if ($row['action_is_needed'] == "1") {
                $action = "Action needed: ".$row['risk_level'];
              } else {
                $action = "No action needed";
              }

             $distname = ucfirst(strtolower($row->infrastructure->school->district->distname));
             $upname = ucfirst(strtolower($row->infrastructure->school->upazila->upname));
             $unname = ucfirst(strtolower($row->infrastructure->school->union->unname));

            if($row->infrastructure->is_active == "1" || $row->infrastructure->is_active == "3"){
                $is_manged = 'Yes';
            } else {
                $is_manged = 'No';
            }

            $sheet->row($rowIndex, [
            $row['water_id'],
            $water_type,
            $row->infrastructure->school->sch_name_en,
            $unname,
            $upname,
            $distname,
            $row['sample_id'],
            $row['sample_type'],
            $row['parameter'],
            $row['value'],
            $row['quarter'],
            $row['year'],
            $row['sampling_date'],
            $row['test_date'],
            $row['verify_date'],
            $row['notify_date'],
            $row['sms_date'],
            $action,
            $row['action_status'],
            $is_manged
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
