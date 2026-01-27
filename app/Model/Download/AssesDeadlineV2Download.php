<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class AssesDeadlineV2Download
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

        \Excel::create(date('d-m-Y').' SafePani Assessment Deadline', function($excel) use($rows) {
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->setOrientation('landscape');


                $sheet->row(1, array(
                        'Waterpoint ID',
                        'Waterpoint Type',
                        'Institution Name',
                        'Institution ID',
                        'Institution Type',
                        'Union',
                        'Upazila',
                        'Last SI Date',
                        'Last SI (Days)',
                        'Next SI Counter',
                        'Last Chem. Date',
                        'Last Chem. (Days)',
                        'Next Chem. Counter',
                        'Last Bact. Date',
                        'Last Bact. (Days)',
                        'Next Bact. Counter',
                        'Onboarding Time',
                        'Actively Managed',
                    )
                );

                $rowIndex = 2;
                foreach($rows as $row)
                {
                    $upname = ucfirst(strtolower(Upazila::find($row->upid)->upname));
                    $unname = ucfirst(strtolower(Union::find($row->unid)->unname));

                    if ($row->tech_type == 'DTW') {
                        $water_type = 'Deep Tubewell';
                    } else if ($row->tech_type == 'STW') {
                        $water_type = 'Shallow Tubewell';
                    } else if ($row->tech_type == 'RWH') {
                        $water_type = 'Rainwater Harvesting System';
                    } else if ($row->tech_type == 'MAR') {
                        $water_type = 'Managed Aquifer Recharge';
                    } else if ($row->tech_type == 'PWS') {
                        $water_type = 'Piped Water';
                    } else if ($row->tech_type == 'PSF') {
                        $water_type = 'Pond Sand Filter';
                    } else if ($row->tech_type == 'SWDU') {
                        $water_type = 'Solar Water Desalination Unit';
                    } else if ($row->tech_type == 'RO') {
                        $water_type = 'Reverse Osmosis';
                    } else {
                        $water_type = 'Unknown';
                    }

                    if($row->is_active == "1"){
                        $is_manged = 'Yes';
                    } else {
                        $is_manged = 'No';
                    }

                    $sheet->row($rowIndex, [
                        $row->water_id,
                        $water_type,
                        $row->sch_name_en,
                        $row->institution_id,
                        $row->sch_type_edu,
                        $upname,
                        $unname,
                        $row->last_si_date,
                        $row->last_si_duration,
                        $row->next_si_days,
                        $row->last_chem_date,
                        $row->last_chem_duration,
                        $row->next_chem_days,
                        $row->last_bact_date,
                        $row->last_bact_duration,
                        $row->next_bact_days,
                        $row->onboard_date,
                        $is_manged,
                    ]);
                    $rowIndex++;
                }
            });
        })->download('csv');
    }
}
