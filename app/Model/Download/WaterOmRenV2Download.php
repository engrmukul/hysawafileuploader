<?php

namespace App\Model\Download;

use App\Model\SPRepairRen;
use App\Model\SPRepairType;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class WaterOmRenV2Download
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

        \Excel::create(date('d-m-Y').' Infrastructure Repair Renovation', function($excel) use($rows) {
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->setOrientation('landscape');

                $repair_types = SPRepairType::all();

                $sheet->row(1, array(
                        'Waterpoint ID',
                        'Waterpoint Type',
                        'Institution Name',
                        'Institution ID',
                        'Institution Type',
                        'Union',
                        'Upazila',
                        'District',

                        $repair_types[0]->rdescription,
                        $repair_types[1]->rdescription,
                        $repair_types[2]->rdescription,
                        $repair_types[3]->rdescription,
                        $repair_types[4]->rdescription,
                        $repair_types[5]->rdescription,
                        $repair_types[6]->rdescription,
                        $repair_types[7]->rdescription,
                        $repair_types[8]->rdescription,
                        $repair_types[9]->rdescription,
                        $repair_types[10]->rdescription,
                        $repair_types[11]->rdescription,
                        $repair_types[12]->rdescription,
                        $repair_types[13]->rdescription,
                        $repair_types[14]->rdescription,
                        $repair_types[15]->rdescription,
                        $repair_types[16]->rdescription,
                        $repair_types[17]->rdescription,
                        $repair_types[18]->rdescription,
                        $repair_types[19]->rdescription,
                        $repair_types[20]->rdescription,
                        $repair_types[21]->rdescription,
                        $repair_types[22]->rdescription,
                        $repair_types[23]->rdescription,
                        $repair_types[24]->rdescription,
                        $repair_types[25]->rdescription,
                        $repair_types[26]->rdescription,
                        $repair_types[27]->rdescription,
                        $repair_types[28]->rdescription,
                        $repair_types[29]->rdescription,
                        $repair_types[30]->rdescription,
                        $repair_types[31]->rdescription,
                        $repair_types[32]->rdescription,
                        $repair_types[33]->rdescription,
                        $repair_types[34]->rdescription,
                        $repair_types[35]->rdescription,
                        'Estimated Cost',

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

                    //Cost calculation
                    $total_cost = 0;

                    for ($i = 0; $i < count($repair_types); $i++) {
                        $rtype = 'rtype' . ($i + 1);

                        if (!empty($row->$rtype) && $row->$rtype == 1) {
                            $total_cost += (int) ($repair_types[$i]->cost ?? 0);
                        }
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

                        $row->rtype1,
                        $row->rtype2,
                        $row->rtype3,
                        $row->rtype4,
                        $row->rtype5,
                        $row->rtype6,
                        $row->rtype7,
                        $row->rtype8,
                        $row->rtype9,
                        $row->rtype10,
                        $row->rtype11,
                        $row->rtype12,
                        $row->rtype13,
                        $row->rtype14,
                        $row->rtype15,
                        $row->rtype16,
                        $row->rtype17,
                        $row->rtype18,
                        $row->rtype19,
                        $row->rtype20,
                        $row->rtype21,
                        $row->rtype22,
                        $row->rtype23,
                        $row->rtype24,
                        $row->rtype25,
                        $row->rtype26,
                        $row->rtype27,
                        $row->rtype28,
                        $row->rtype29,
                        $row->rtype30,
                        $row->rtype31,
                        $row->rtype32,
                        $row->rtype33,
                        $row->rtype34,
                        $row->rtype35,
                        $row->rtype36,
                        Helper::formatIndianNumber($total_cost),

                        $row->onboard_date,
                        $is_manged,
                    ]);
                    $rowIndex++;
                }
            });
        })->download('csv');
    }
}
