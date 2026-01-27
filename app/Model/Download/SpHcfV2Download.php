<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpHcfV2Download
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

    \Excel::create(date("d-m-Y").' SafePani Healthcare Facilities', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'Institution ID',
          'HCF Name',
          'Establishment Year',
          'Type_of_institution: Ownership',
          'Type_of_HCF',
          'Union',
          'Upazila',
          'District',
          'Village',
          'Male Staff',
          'Female Staff',
          'Total Staff',
          'Daily Visitor',
          'Average_Monthly_patients',
          'Patients in catchment area',
          'Waterpoints_inside_HCF_premises',
          'Drinking_water_sources_inside_HCF_premises',
          'Name of Respondent',
          'Designation of Respondent',
          'Mobile of Respondent',
            'Name of CHCP',
            'CHCP Mobile',
          'Name of SMC President',
          'Mobile of SMC President',
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
                $row['hcf_type'],
                ucfirst(strtolower($row->union->unname)),
                ucfirst(strtolower($row->upazila->upname)),
                ucfirst(strtolower($row->district->distname)),
                $row['vill'],
                $row['male_staff'],
                $row['female_staff'],
                $row['tot_staff'],
                $row['daily_visitor'],
                $row['monthly_patients'],
                $row['catchm_patients'],
                $row['water_counts'],
                $row['drinking_counts'],
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
        });
    })->download('csv');
  }
}
