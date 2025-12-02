<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpSchoolV2Download
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

    \Excel::create(date('d-m-Y').' SafePani School List', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');

          if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
              $sheet->row(1, array(
                      'Institution ID',
                      'School Name',
                      'Establishment Year',
                      'Type_of_school: Ownership',
                      'Type_of_Institution',
                      'Union',
                      'Upazila',
                      'District',
                      'Village',
                      'Boys_registered',
                      'Girls_registered',
                      'Disabled boys',
                      'Disabled girls',
                      'Total Students',
                      'Male Staff',
                      'Female Staff',
                      'Total Staff',
                      'Waterpoints_inside_school_premises',
                      'Drinking_water_sources_inside_school_premises',
                      'Nearby_families_use_this_source_for_drinking',
                      'Name of Respondent',
                      'Designation of Respondent',
                      'Mobile of Respondent',
                      'Name of Headmaster/ CHCP',
                      'Mobile of Headmaster/ CHCP',
                      'Latitude',
                      'Longitude',
                      'Actively Managed',
                      'Onboarding Time',
                      'Last update',
                      'Baseline Date',
                      'Comments',
                      'Baseline Assessor'
                  )
              );

              $rowIndex = 2;
              foreach($rows as $row)
              {
                  if($row['is_active'] == "1" || $row['is_active'] == "3"){
                      $is_manged = 'Yes';
                  } else {
                      $is_manged = 'No';
                  }

                  $user = User::where('id', $row['base_asse'])->get(['name']);
                  if(isset($user[0]->name))
                      $user_name = $user[0]->name;
                  else
                      $user_name = "N/A";

                  $sheet->row($rowIndex, [
                      $row['institution_id'],
                      $row['sch_name_en'],
                      $row['estab_year'],
                      $row['owner_type'],
                      $row['sch_type_edu'],
                      ucfirst(strtolower($row->union->unname)),
                      ucfirst(strtolower($row->upazila->upname)),
                      ucfirst(strtolower($row->district->distname)),
                      $row['vill'],
                      $row['boy_student'],
                      $row['girl_student'],
                      $row['disabled_boys'],
                      $row['disabled_girls'],
                      $row['tot_student'],
                      $row['male_staff'],
                      $row['female_staff'],
                      $row['tot_staff'],
                      $row['water_counts'],
                      $row['drinking_counts'],
                      $row['nearby_families'],
                      $row['contact_name'],
                      $row['contact_position'],
                      $row['contact_phone'],
                      $row['headmaster_chcp'],
                      $row['head_phone'],
                      $row['lat'],
                      $row['lon'],
                      $is_manged,
                      $row['onboard_date'],
                      $row['last_update'],
                      Carbon::parse($row['base_date'])->format('Y-m-d'),
                      $row['remark'],
                      $user_name
                  ]);
                  $rowIndex++;
              }

          } else {
              $sheet->row(1, array(
                      'Institution ID',
                      'School Name',
                      'Establishment Year',
                      'Type_of_school: Ownership',
                      'Type_of_Institution',
                      'Type_of_school: Gender',
                      'Union',
                      'Upazila',
                      'District',
                      'Village',
                      'Boys_registered',
                      'Girls_registered',
                      'Disabled boys',
                      'Disabled girls',
                      'Total Students',
                      'Male Staff',
                      'Female Staff',
                      'Total Staff',
                      'Waterpoints_inside_school_premises',
                      'Drinking_water_sources_inside_school_premises',
                      'Nearby_families_use_this_source_for_drinking',
                      'Name of Respondent',
                      'Designation of Respondent',
                      'Mobile of Respondent',
                      'Name of Headmaster/ CHCP',
                      'Mobile of Headmaster/ CHCP',
                      'Name of SMC President',
                      'Mobile of SMC President',
                      'Latitude',
                      'Longitude',
                      'Actively Managed',
                      'Onboarding Time',
                      'Last update',
                      'Baseline Date',
                      'Baseline Date',
                      'Comments',
                      'Baseline Assessor'
                  )
              );

              $rowIndex = 2;
              foreach($rows as $row)
              {
                  if($row['is_active'] == "1" || $row['is_active'] == "3"){
                      $is_manged = 'Yes';
                  } else {
                      $is_manged = 'No';
                  }

                  $user = User::where('id', $row['base_asse'])->get(['name']);
                  if(isset($user[0]->name))
                      $user_name = $user[0]->name;
                  else
                      $user_name = "N/A";

                  $sheet->row($rowIndex, [
                      $row['institution_id'],
                      $row['sch_name_en'],
                      $row['estab_year'],
                      $row['owner_type'],
                      $row['sch_type_edu'],
                      $row['sch_type_gen'],
                      ucfirst(strtolower($row->union->unname)),
                      ucfirst(strtolower($row->upazila->upname)),
                      ucfirst(strtolower($row->district->distname)),
                      $row['vill'],
                      $row['boy_student'],
                      $row['girl_student'],
                      $row['disabled_boys'],
                      $row['disabled_girls'],
                      $row['tot_student'],
                      $row['male_staff'],
                      $row['female_staff'],
                      $row['tot_staff'],
                      $row['water_counts'],
                      $row['drinking_counts'],
                      $row['nearby_families'],
                      $row['contact_name'],
                      $row['contact_position'],
                      $row['contact_phone'],
                      $row['headmaster_chcp'],
                      $row['head_phone'],
                      $row['smc_president'],
                      $row['smc_phone'],
                      $row['lat'],
                      $row['lon'],
                      $is_manged,
                      $row['onboard_date'],
                      $row['last_update'],
                      Carbon::parse($row['base_date'])->format('Y-m-d'),
                      $row['remark'],
                      $user_name
                  ]);
                  $rowIndex++;
              }

          }

        });
    })->download('csv');
  }
}
