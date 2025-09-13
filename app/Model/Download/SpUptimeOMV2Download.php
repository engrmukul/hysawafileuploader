<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class SpUptimeOMV2Download
{
    private $rows;
    private $request;
    private $start_date;
    private $end_date;

    public function __construct($rows, $start_date, $end_date)
    {
        $this->waters = $rows;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function download()
    {
        $rows = $this->waters;
        $start_date = $this->start_date;
        $end_date = $this->end_date;

        if(!count($rows))
        {
            return response()->json(['status' => 'error', 'message' => 'No Data Found']);
        }

        \Excel::create('O&M Uptime Data ('.$start_date.' to '.$end_date.')', function($excel) use($rows, $start_date, $end_date) {
            $excel->sheet('Sheetname', function($sheet) use($rows, $start_date, $end_date) {
                $sheet->setOrientation('landscape');


                $sheet->row(1, array(
                        'wp_id',
                        'start_date',
                        'duration_days',
                        'waterpoint_type'
                    )
                );

                $rowIndex = 2;
                foreach($rows as $row)
                {
                    if ($row['problem_identification'] == '1') {
                        $identification_type = 'Planned service interruption';
                    } else {
                        $identification_type = 'Unplanned service interruption';
                    }

                    $timeStamp = $row['maintenance_time'];
                    $start_date = date( "Y-m-d", strtotime($timeStamp));

                    $sheet->row($rowIndex, [
                        $row['water_id'],
                        $start_date,
                        $row['days_frac'],
                        $identification_type,
                    ]);
                    $rowIndex++;
                }
            });
        })->download('csv');
    }
}
