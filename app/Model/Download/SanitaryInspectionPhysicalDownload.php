<?php

namespace App\Model\Download;

use App\Model\SPSanAnswerCorr;
use App\Model\SPSanQuest;
use App\Model\SPSanQuestObs;
use App\Model\Union;
use App\Model\Upazila;
use App\User;
use Illuminate\Http\Request;

class SanitaryInspectionPhysicalDownload
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

        if (!count($rows)) {
            return response()->json(['status' => 'error', 'message' => 'No Data Found']);
        }

        \Excel::create(date("d-m-Y").' SafePani Physical Observation v2', function($excel) use ($rows) {
            $excel->sheet('Sheetname', function ($sheet) use ($rows) {
                $sheet->setOrientation('landscape');

                $quest_phy = SPSanQuestObs::all();

                $sheet->row(1, array(
                        'Entry ID',
                        'Waterpoint ID',
                        'Waterpoint Type',
                        'School Name',
                        'Union',
                        'Upazila',
                        'District',
                        'Quarter',
                        'Year',
                        'Inspection Date',
                        $quest_phy[0]['quest_en'],
                        $quest_phy[1]['quest_en'],
                        $quest_phy[2]['quest_en'],
                        $quest_phy[3]['quest_en'],
                        $quest_phy[4]['quest_en'],
                        $quest_phy[5]['quest_en'],
                        $quest_phy[6]['quest_en'],
                        $quest_phy[7]['quest_en'],
                        $quest_phy[8]['quest_en'],
                        'latitude',
                        'longitude',
                        'Submitted by',
                        //'Submitted At',
                        'Actively Managed',
                    )
                );

                $rowIndex = 2;
                foreach ($rows as $row) {
                    $user = User::where('id', $row->SpSanInspectionV2->created_by)->get()->first();
                    if (isset($user))
                        $username = $user['email'];
                    else
                        $username = "";

                    if ($row->SpSanInspectionV2->infrastructure->tech_type == 'DTW') {
                        $water_type = 'Deep Tubewell';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'STW') {
                        $water_type = 'Shallow Tubewell';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'RWH') {
                        $water_type = 'Rainwater Harvesting System';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'MAR') {
                        $water_type = 'Managed Aquifer Recharge';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'PWS') {
                        $water_type = 'Piped Water';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'PSF') {
                        $water_type = 'Pond Sand Filter';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'SWDU') {
                        $water_type = 'Solar Water Desalination Unit';
                    } else if ($row->SpSanInspectionV2->infrastructure->tech_type == 'RO') {
                        $water_type = 'Reverse Osmosis';
                    } else {
                        $water_type = 'Unknown';
                    }

                    $upname = ucfirst(strtolower(Upazila::find($row->SpSanInspectionV2->infrastructure->school->upid)->upname));
                    $unname = ucfirst(strtolower(Union::find($row->SpSanInspectionV2->infrastructure->school->unid)->unname));

                    if ($row->SpSanInspectionV2->infrastructure->is_active == "1" || $row->SpSanInspectionV2->infrastructure->is_active == "3") {
                        $is_manged = 'Yes';
                    } else {
                        $is_manged = 'No';
                    }

                    $sheet->row($rowIndex, [
                        $row['id'],
                        $row->SpSanInspectionV2['water_id'],
                        $water_type,
                        $row->SpSanInspectionV2->infrastructure->school->sch_name_en,
                        ucfirst(strtolower($row->SpSanInspectionV2->infrastructure->school->union->unname)),
                        ucfirst(strtolower($row->SpSanInspectionV2->infrastructure->school->upazila->upname)),
                        ucfirst(strtolower($row->SpSanInspectionV2->infrastructure->school->district->distname)),
                        $row->SpSanInspectionV2['quarter'],
                        $row->SpSanInspectionV2['year'],
                        $row->SpSanInspectionV2['inspection_date'],
                        $row->wat_user_q1,
                        $row->wat_user_q2,
                        $row->wat_user_q3,
                        $row->wat_user_q4,
                        $row->wat_user_q5,
                        $row->wat_user_q6,
                        $row->phy_obs_q1,
                        $row->phy_obs_q2,
                        $row->phy_obs_q3,
                        $row->SpSanInspectionV2['lat'],
                        $row->SpSanInspectionV2['lon'],
                        $username,
                        //$row['created_at'],
                        $is_manged
                    ]);
                    $rowIndex++;
                }
            });
        })->download('csv');
    }
}

