<?php

namespace App\Model\Download;

use App\Model\SPSanAnswerCorr;
use App\Model\SPSanQuest;
use App\Model\Union;
use App\Model\Upazila;
use App\User;
use Illuminate\Http\Request;

class SanitaryInspectionPwsTsDownload
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

        \Excel::create(date("d-m-Y").' SafePani Sanitary Inspection v2 (Tap Stand)', function($excel) use ($rows) {
            $excel->sheet('Sheetname', function ($sheet) use ($rows) {
                $sheet->setOrientation('landscape');

                $quest_pwsts = SPSanQuest::where('quest_cat', 'pipe-tapstand')->get();

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
                        'Sanitary Score',
                        'Accountable Score',
                        'Risk Level',
                        $quest_pwsts[0]['quest_en'],
                        $quest_pwsts[1]['quest_en'],
                        $quest_pwsts[2]['quest_en'],
                        $quest_pwsts[3]['quest_en'],
                        $quest_pwsts[4]['quest_en'],
                        'Correctives',
                        'image1',
                        'image2',
                        'image3',
                        'latitude',
                        'longitude',
                        'Submitted by',
                        //'Submitted At',
                        'Actively Managed',
                    )
                );

                $rowIndex = 2;
                foreach ($rows as $row) {
                    $user = User::where('id', $row->created_by)->get()->first();
                    if (isset($user))
                        $username = $user['email'];
                    else
                        $username = "";

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

                    if ($row->infrastructure->is_active == "1" || $row->infrastructure->is_active == "3") {
                        $is_manged = 'Yes';
                    } else {
                        $is_manged = 'No';
                    }

                    $correctives = '';
                    $j = 1;

                    if(isset($row->SpSanAnswerCorr[0]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[0]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[1]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[1]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[2]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[2]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[3]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[3]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[4]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[4]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[5]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[5]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[6]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[6]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[7]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[7]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[8]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[8]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[9]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[9]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[10]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[10]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[11]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[11]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[12]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[12]->SpSanCorrective->correct_en." ";
                    }
                    if(isset($row->SpSanAnswerCorr[13]->SpSanCorrective->correct_en)){
                        $correctives = $correctives.$j++.". ".$row->SpSanAnswerCorr[13]->SpSanCorrective->correct_en." ";
                    }

                    $image1 = $image2 = $image3 = '';
                    if($row['image1'] != null){
                        $image1 = "http://www.hysawa.com/mis/public/".$row['image1'];
                    }

                    if($row['image2'] != null){
                        $image2 = "http://www.hysawa.com/mis/public/".$row['image2'];
                    }

                    if($row['image3'] != null){
                        $image3 = "http://www.hysawa.com/mis/public/".$row['image3'];
                    }


                    $sheet->row($rowIndex, [
                        $row['id'],
                        $row['water_id'],
                        $water_type,
                        $row->infrastructure->school->sch_name_en,
                        ucfirst(strtolower($row->infrastructure->school->union->unname)),
                        ucfirst(strtolower($row->infrastructure->school->upazila->upname)),
                        ucfirst(strtolower($row->infrastructure->school->district->distname)),
                        $row['quarter'],
                        $row['year'],
                        $row['inspection_date'],
                        $row['sanitary_score'],
                        $row['accnt_score'],
                        $row['sanitary_risk'],
                        $row->SpSanAnswer->pwstq1,
                        $row->SpSanAnswer->pwstq2,
                        $row->SpSanAnswer->pwstq3,
                        $row->SpSanAnswer->pwstq4,
                        $row->SpSanAnswer->pwstq5,
                        $correctives,
                        $image1,
                        $image2,
                        $image3,
                        $row['lat'],
                        $row['lon'],
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

