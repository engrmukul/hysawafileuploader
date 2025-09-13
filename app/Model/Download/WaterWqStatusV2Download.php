<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class WaterWqStatusV2Download
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

        \Excel::create(date('d-m-Y').' Infrastructure Water Quality Status', function($excel) use($rows) {
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
                        'District',

                        'Do you remember any/last WQ Test?',
                        'When?',
                        'Who?',
                        'Any report available?',
                        'High arsenic',
                        'High salinity',
                        'High iron',
                        'Regular agency test?',
                        'Which agency?',
                        'Frequency?',
                        'Annual flooding',
                        'storm inundation',
                        'decline in water table',
                        'Tidal flooding',
                        'Community reliance during climate events?',

                        'Onboarding Time',
                        'Actively Managed',
                    )
                );

                $rowIndex = 2;
                foreach($rows as $row)
                {

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

                    if($row->is_active == "1" || $row->is_active == "3"){
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
                        ucfirst(strtolower($row->unname)),
                        ucfirst(strtolower($row->upname)),
                        ucfirst(strtolower($row->distname)),

                        $row->is_past_wq,
                        $row->wq_when,
                        $row->wq_who,
                        $row->is_rep,
                        $row->is_ars,
                        $row->is_cl,
                        $row->is_fe,
                        $row->is_agency,
                        $row->agency_nm,
                        $row->agency_freq,
                        $row->vul_ann_flood,
                        $row->vul_storm,
                        $row->vul_dec_water,
                        $row->vul_tid_flood,
                        $row->comm_reliance,

                        $row->onboard_date,
                        $is_manged,
                    ]);
                    $rowIndex++;
                }
            });
        })->download('csv');
    }
}
