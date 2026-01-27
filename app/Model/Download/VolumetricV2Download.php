<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class VolumetricV2Download
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

    \Excel::create(date('d-m-Y').' SafePani Volumetric Uses', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'Waterpoint ID',
          'Waterpoint Type',
          'School Name',
          'Union',
          'Upazila',
          'Flowmeter No',
          'Reading',
          'Water Consumption (m3)',
          'Reading Date',
          'Comments',
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

            $upname = ucfirst(strtolower(Upazila::find($row->infrastructure->school->upid)->upname));
            $unname = ucfirst(strtolower(Union::find($row->infrastructure->school->unid)->unname));

            if($row->infrastructure->is_active == "1"){
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
            $row['flowmeter_no'],
            $row['reading'],
            $row['consumption'],
            $row['reading_date'],
            $row['comments'],
            $is_manged
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
