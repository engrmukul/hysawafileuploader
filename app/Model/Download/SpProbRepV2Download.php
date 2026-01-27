<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;

class SpProbRepV2Download
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

    \Excel::create(date('d-m-Y').' SafePani Problem Reports & Maintenance', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'S.No',
          'Upazila',
          'Union',
          'Institution_Name',
          'Institution_Type',
          'Institution_ID',
          'Waterpoint_Type',
          'Waterpoint_ID',
          'Quarter',
          'Year',
          'Month',
          'Maintenance_Activity',
          'Maintenance_Details',
          'Materials_Cost',
          'Labor_Cost',
          'Transport_Cost',
          'Total_Cost',
          'Problem_identification',
          '1st_Notification_date_&_time',
          'Finish_time_of_maintenance_work',
          'Response_time (Hours & Min)',
          'response_time',
          'Days',
          'Comments',
          'Problem ID',
          'Resolve Status (WP Manager)',
          'Problem Type',
          'Problem Date',
          'Image1',
          'Image2',
          'Image3',
          'Recording',
          'Reported By',
          'Maintenance Status',
          'Identification Date',
          'Mat. Cost (Stock)',
          'Tank Cleaning Cost',
          'Electricity Bill',
          'VAT',
          'TAX',
          'Maintenance By',
          'Verification Status',
          'Verification Comments',
          'Verified By',
          'Verification Date',
          'Actively Managed'
          )
        );

        $sl = 1;
        $rowIndex = 2;
        foreach($rows as $row)
        {
            if(isset($row->upid)){
                $upname = ucfirst(strtolower(Upazila::find($row->upid)->upname));
            } else {
                $upname = 'All';
            }

            if(isset($row->unid)){
                $unname = ucfirst(strtolower(Union::find($row->unid)->unname));
            } else {
                $unname = 'All';
            }

            $ptype_arr = array($row->ptype1, $row->ptype2, $row->ptype3, $row->ptype4,
                $row->ptype5, $row->ptype6, $row->ptype7, $row->ptype8);
            //$ptype_arr = array_filter($ptype_arr);
            $prob_type = array();

            $j = 0;
            for ($i = 0; $i < sizeof($ptype_arr); $i++) {
                if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '1') {
                    $prob_type[$j++] = 'Decrease in water flow';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '2') {
                    $prob_type[$j++] = 'No water flow';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '3') {
                    $prob_type[$j++] = 'Water leakage';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '4') {
                    $prob_type[$j++] = 'Change in water taste, smell or colour';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '5') {
                    $prob_type[$j++] = 'Requires regular maintenance (e.g. cleaning, changing filter media)';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '6') {
                    $prob_type[$j++] = 'Damage to parts';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '8') {
                    $prob_type[$j++] = 'Disinfection';
                } else if (isset($ptype_arr[$i]) && $ptype_arr[$i] == '7') {
                    $prob_type[$j++] = 'Others';
                }
            }

            $mtype_arr = array($row->mtype1, $row->mtype2, $row->mtype3, $row->mtype4,
                $row->mtype5, $row->mtype6, $row->mtype7);
            //$mtype_arr = array_filter($mtype_arr);
            $main_type = array();

            $k = 0;
            for ($i = 0; $i < sizeof($mtype_arr); $i++) {
                if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '1') {
                    $main_type[$k++] = 'Replace small parts (e.g. washer, bucket, nuts, check valve, taps)';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '2') {
                    $main_type[$k++] = 'Replace large parts (e.g. handle, rods, pipes)';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '3') {
                    $main_type[$k++] = 'Repair platform, leaks';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '4') {
                    $main_type[$k++] = 'Tank/catchment cleaning';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '5') {
                    $main_type[$k++] = 'Disinfection';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '6') {
                    $main_type[$k++] = 'Replace filter media';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '7') {
                    $main_type[$k++] = 'Others';
                } else if (isset($mtype_arr[$i]) && $mtype_arr[$i] == '8') {
                    $main_type[$k++] = 'Electricity Bill';
                }
            }

            if(isset($row->user_id)){
                $reported_by_role = RoleUser::where('user_id',$row->user_id)->get();
                $reported_by_role_id = $reported_by_role[0]['role_id'];

                if($reported_by_role_id == 11){
                    $reported_by_role_name = "WP Manager";
                } else if($reported_by_role_id == 12){
                    $reported_by_role_name = "CLO";
                } else {
                    $reported_by_role_name = "Engineer/Admin";
                }

                $reported_by = User::where('id', $row->user_id)->get()->first();
                if(isset($reported_by))
                    $reported_by = $reported_by['email']." (".$reported_by_role_name.")";
                else
                    $reported_by = "";
            } else {
                $reported_by = "";
            }

            if(isset($row->eng_updated_by)){
                $main_by_role = RoleUser::where('user_id',$row->eng_updated_by)->get();
                $main_by_role_id = $main_by_role[0]['role_id'];

                if($main_by_role_id == 11){
                    $main_by_role_name = "WP Manager";
                } else if($main_by_role_id == 12){
                    $main_by_role_name = "CLO";
                } else {
                    $main_by_role_name = "Engineer/Admin";
                }

                $main_by = User::where('id', $row->eng_updated_by)->get()->first();
                if(isset($main_by))
                    $main_by = $main_by['email']." (".$main_by_role_name.")";
                else
                    $main_by = "";
            } else {
                $main_by = "";
            }

            if(isset($row->user_updated_by)){
                $veri_by_role = RoleUser::where('user_id',$row->user_updated_by)->get();
                $veri_by_role_id = $veri_by_role[0]['role_id'];

                if($veri_by_role_id == 11){
                    $veri_by_role_name = "WP Manager";
                } else if($veri_by_role_id == 12){
                    $veri_by_role_name = "CLO";
                } else {
                    $veri_by_role_name = "Engineer/Admin";
                }

                $veri_by = User::where('id', $row->user_updated_by)->get()->first();
                if(isset($veri_by))
                    $veri_by = $veri_by['email']." (".$veri_by_role_name.")";
                else
                    $veri_by = "";
            } else {
                $veri_by = "";
            }

            if ($row->user_resolve_status == 1){
                $user_res_status = "Not Verified";
            } else if ($row->user_resolve_status == 2){
                $user_res_status = "Partially Resolved";
            }  else if ($row->user_resolve_status == 3) {
                $user_res_status = "Completely Resolved";
            } else {
                $user_res_status = "Not Specified";
            }

            if ($row->is_maintenance == '1') {
                $main_status = 'Unresolved';
            } else if ($row->is_maintenance == '2') {
                $main_status = 'Resolved within 24 hours';
            } else if ($row->is_maintenance == '3') {
                $main_status = 'Resolved within 48 hours';
            } else if ($row->is_maintenance == '4') {
                $main_status = 'Resolved beyond 48 hours';
            } else {
                $main_status = '';
            }

            if ($row->is_resolved == '1') {
                $veri_status = 'Not Verified';
            } else if ($row->is_resolved == '2') {
                $veri_status = 'Resolved within 24 hours';
            } else if ($row->is_resolved == '3') {
                $veri_status = 'Resolved within 48 hours';
            } else if ($row->is_resolved == '4') {
                $veri_status = 'Resolved beyond 48 hours';
            } else {
                $veri_status = '';
            }

            if ($row->prob_identification == 1){
                $prob_identification = "Regular Visit";
            } else if($row->prob_identification == 2){
                $prob_identification = "App Notification";
            } else if($row->prob_identification == 3){
                $prob_identification = "Phone Call";
            } else {
                $prob_identification = "Others";
            }

            if($row->p_image1 != null){
                $image1 = "http://www.hysawa.com/mis/public/".$row->p_image1;
            } else {
                $image1 = "";
            }

            if($row->p_image2 != null){
                $image2 = "http://www.hysawa.com/mis/public/".$row->p_image2;
            } else {
                $image2 = "";
            }

            if($row->p_image3 != null){
                $image3 = "http://www.hysawa.com/mis/public/".$row->p_image3;
            } else {
                $image3 = "";
            }

            if($row->p_recording != null){
                $recording = "http://www.hysawa.com/mis/public/".$row->p_recording;
            } else {
                $recording = "";
            }

            if($row->p_createdate != null){
                $problem_date = date('m-d-Y', strtotime($row->p_createdate));
            } else {
                $problem_date = "";
            }

            if($row->inserted_at != null){
                //$report_date = date('m-d-Y', strtotime($row->created_at));
                $report_date = $row->inserted_at;
            } else {
                $report_date = "";
            }

            if($row->identification_date != null){
                $identification_date = date('m-d-Y', strtotime($row->identification_date));
            } else {
                $identification_date = "";
            }

            if($row->eng_updated_at != null){
                //$maintenance_date = date('m-d-Y', strtotime($row->eng_updated_at));
                $maintenance_date = $row->eng_updated_at;
            } else {
                $maintenance_date = "";
            }

            if($row->user_updated_at != null){
                //$verification_date = date('m-d-Y', strtotime($row->user_updated_at));
                $verification_date = $row->user_updated_at;
            } else {
                $verification_date = "";
            }

            if($row->tech_type == 'DTW')
                $water_type = "Deep Tubewell";
            elseif ($row->tech_type == 'STW')
                $water_type = "Shallow Tubewell";
            elseif ($row->tech_type == 'RO')
                $water_type = "Reverse Osmosis";
            elseif ($row->tech_type == 'RWH')
                $water_type = "Rainwater Harvesting System";
            elseif ($row->tech_type == 'MAR')
                $water_type = "Managed Aquifer Recharge";
            elseif ($row->tech_type == 'PWS')
                $water_type = "Piped Water";
            elseif ($row->tech_type == 'PSF')
                $water_type = "Pond Sand Filter";
            elseif ($row->tech_type == 'SWDU')
                $water_type = "Solar Water Desalination Unit";
            else
                $water_type = "";

            if($row->response_time != null){
                $res_hm = explode(':', $row->response_time);
                if($res_hm[1] > 30){
                    $res_hours =  $res_hm[0]+1;
                } else {
                    $res_hours =  $res_hm[0];
                }
                $res_days = round($res_hours/24);
            } else {
                $res_hours = '';
                $res_days = '';
            }

            $dateValue = strtotime($row->eng_updated_at);

            $yr = date("Y", $dateValue) ." ";
            $mon = date("m", $dateValue)." ";
            $mon_name = date("M", $dateValue);
            $date = date("Y-m-d", $dateValue);

            if($mon > 9){
                $quarter = 'Q4';
            } else if($mon > 6) {
                $quarter = 'Q3';
            } else if($mon > 3) {
                $quarter = 'Q2';
            } else {
                $quarter = 'Q1';
            }

            if($row->is_active == "1"){
                $is_manged = 'Yes';
            } else {
                $is_manged = 'No';
            }

            $sheet->row($rowIndex, [
            $sl++,
            $upname,
            $unname,
            $row->sch_name_en,
            $row->sch_type_edu,
            $row->institution_id,
            $water_type,
            $row->infrastructure_id,
            $quarter,
            $yr,
            $mon_name,
            implode("; ",$main_type),
            $row->eng_main_comment,
            $row->materials_cost,
            $row->labor_cost,
            $row->transport_cost,
            $row->main_cost,
            $prob_identification,
            $report_date,
            $maintenance_date,
            $row->response_time,
            $res_hours,
            $res_days,
            $row->p_description,
            $row->id,
            $user_res_status,
            implode("; ",$prob_type),
            $problem_date,
            $image1,
            $image2,
            $image3,
            $recording,
            $reported_by,
            $main_status,
            $identification_date,
            $row->from_stock,
            $row->tank_cleaning_cost,
            $row->electricity_bill,
            $row->mat_vat,
            $row->mat_tax,
            $main_by,
            $veri_status,
            $row->user_veri_comment,
            $veri_by,
            $verification_date,
            $is_manged
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
