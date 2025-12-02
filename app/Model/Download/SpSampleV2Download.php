<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;

class SpSampleV2Download
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

    \Excel::create(date('d-m-Y').' SafePani Sample Collection', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'S.No',
        'Sample ID',
        'Sample Type',
        'Sample Cat',
        'Waterpoint ID',
        'Institution Name',
        'Institution Type',
        'Union',
        'Upazila',
        'District',
        'Quarter',
        'Year',
        'EC (uS/cm)',
        'pH',
        'Temp (Deg.C)',
        'Weather',
        'Color',
        'Decontamination',
        'Sampling Date',
        'Start Time',
        'Sampling Time',
        'Disinfect Stat',
        'Elevation',
        'Lat',
        'Lon',
        'Comments',
        'Verified',
        'Notified',
        'Reported By',
        'Actively Managed'
          )
        );

        $sl = 1;
        $rowIndex = 2;
        foreach($rows as $row)
        {
          $user = User::where('id', $row->created_by)->get()->first();
            if(isset($user))
                $username = $user['email'];
            else
                $username = "";

            if(isset($row->distid)){
                $distname = ucfirst(strtolower($row->distname));
            } else {
                $distname = 'All';
            }

            if(isset($row->upid)){
                $upname = ucfirst(strtolower($row->upname));
            } else {
                $upname = 'All';
            }

            if(isset($row->unid)){
                $unname = ucfirst(strtolower($row->unname));
            } else {
                $unname = 'All';
            }

            if($row->disinfect_status == '0'){
                $disinfect_status = 'Not Required';
            } else if($row->disinfect_status == '1') {
                $disinfect_status = 'Requested';
            } else if($row->disinfect_status == '2') {
                $disinfect_status = 'Completed';
            } else {
                $disinfect_status = '';
            }

            if($row->result_verified == '1'){
                $verified = 'Yes';
            } else {
                $verified = 'No';
            }

            if($row->is_notified == '1'){
                $notified = 'Yes';
            } else {
                $notified = 'No';
            }

            if($row->is_active == "1" || $row->is_active == "3"){
                $is_manged = 'Yes';
            } else {
                $is_manged = 'No';
            }

            $sheet->row($rowIndex, [
            $sl++,
            $row->sample_id,
            $row->sample_no,
            $row->sample_cat,
            $row->water_id,
            $row->sch_name_en,
            $row->sch_type_edu,
            $unname,
            $upname,
            $distname,
            $row->quarter,
            $row->year,
            $row->ec,
            $row->ph,
            $row->temp,
            $row->weather,
            $row->color,
            $row->decon_process,
            $row->sample_date,
            $row->start_time,
            $row->sample_time,
            $disinfect_status,
            $row->elevation,
            $row->lat,
            $row->lon,
            $row->comments,
            $verified,
            $notified,
            $username,
            $is_manged
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
