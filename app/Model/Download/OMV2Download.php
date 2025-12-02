<?php

namespace App\Model\Download;

use App\Model\District;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class OMV2Download
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

    \Excel::create(date("d-m-Y").' SafePani O&M Response Time', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'OM_ID',
          'Waterpoint ID',
          'Problem ID',
          'Waterpoint Type',
          'Institution Name',
          'Institution Type',
          'Union',
          'Upazila',
          'District',
          'Quarter',
          'Year',
          'Month',
          'Maintenance Activity',
          'Maintenance Details',
          'Materials Cost',
          'Labor Cost',
          'Transport Cost',
          'Total Cost',
            'Problem Identification',
            'Notification Time',
            'Maintenance Time',
            'Response Time',
            'Response Time (Digit)',
            'Days (Farction)',
            'Actively Managed',
            'Comments'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
            if($row['notification_time'] < date("2023-09-01 00:00:01")){
                $prob_id = "N/A";
            } else {
                if($row['problem_id'] != null)
                    $prob_id = $row['problem_id'];
                else
                    $prob_id = "?";
            }
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

            $upname = ucfirst(strtolower(Upazila::find($row->infrastructure->school->upid)->upname));
            $unname = ucfirst(strtolower(Union::find($row->infrastructure->school->unid)->unname));
            $distname = ucfirst(strtolower(District::find($row->infrastructure->school->distid)->distnamename));

            if($row->infrastructure->is_active == "1"){
                $is_manged = 'Yes';
            } else {
                $is_manged = 'No';
            }

            $sheet->row($rowIndex, [
            $row['id'],
            $row['water_id'],
            $prob_id,
            $water_type,
            $row->infrastructure->school->sch_name_en,
            $row->infrastructure->school->sch_type_edu,
            $unname,
            $upname,
            $distname,
            $row['quarter'],
            $row['year'],
            $row['month'],
            $row['maintenance_activity'],
            $row['maintenance_details'],
            $row['materials_cost'],
            $row['labor_cost'],
            $row['transport_cost'],
            $row['total_cost'],
            $row['problem_identification'],
            $row['notification_time'],
            $row['maintenance_time'],
            $row['response_time'],
            $row['response_time_digit'],
            $row['days_frac'],
            $is_manged,
            $row['comments'],
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
