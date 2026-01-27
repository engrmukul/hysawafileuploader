<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class WaterInfrastructuresV2Download
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

        \Excel::create(date('d-m-Y').' SafePani Water Infrastructures', function($excel) use($rows) {
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->setOrientation('landscape');

                if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                    $sheet->row(1, array(
                            'Waterpoint ID',
                            'Waterpoint Type',
                            'Institution Name',
                            'Institution ID',
                            'Institution Type',
                            'Union',
                            'Upazila',
                            'District',
                            'Functional Status',
                            'non_func_status',
                            'non_func_days',
                            'Drinking Use',
                            'non_drink_reason',
                            'run_year_round',
                            'run_year_no_reason',
                            'Installed Year',
                            'Installed By',
                            'Pumping mechanism',
                            'Depth (ft)',
                            'No. of Tanks',
                            'Tank material',
                            'Tank capacity (ltr)',
                            'Tank Distance',
                            'Water_hours',
                            'Catchment Area (m2)',
                            'Catchment Material',
                            'Prod. cap. (ltr/hr)',
                            'water_lasts_month',
                            'repair_ren_is_required',
                            'Latitude',
                            'Longitude',
                            'Image',
                            'Onboarding Time',
                            'Actively Managed',
                            'Comments'
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

                        if($row->image != null){
                            $image = "http://www.hysawa.com/mis/public/sp_assets/SafePani_Waterpoints_Photo/".$row->image;
                        } else {
                            $image = "";
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
                            $row->functional_status,
                            $row->non_func_status,
                            $row->non_func_days,
                            $row->drinking_use,
                            $row->non_drink_reason,
                            $row->run_year_round,
                            $row->run_year_reason,
                            $row->install_year,
                            $row->install_by,
                            $row->pumping,
                            $row->depth,
                            $row->tanks_count,
                            $row->tank_material,
                            $row->tank_capacity,
                            $row->tank_distance,
                            $row->water_hours,
                            $row->catchment_area,
                            $row->catchment_material,
                            $row->capacity_liter,
                            $row->water_lasts_month,
                            $row->is_om_req,
                            $row->lat,
                            $row->lon,
                            $image,
                            $row->onboard_date,
                            $is_manged,
                            $row->comments
                        ]);
                        $rowIndex++;
                    }

                } else {
                    $sheet->row(1, array(
                            'Waterpoint ID',
                            'Waterpoint Type',
                            'Institution Name',
                            'Institution ID',
                            'Institution Type',
                            'Union',
                            'Upazila',
                            'District',
                            'Functional Status',
                            'non_func_status',
                            'non_func_days',
                            'Drinking Use',
                            'non_drink_reason',
                            'run_year_round',
                            'run_year_no_reason',
                            'Installed Year',
                            'Installed By',
                            'Pumping mechanism',
                            'Depth (ft)',
                            'No. of Tanks',
                            'Tank material',
                            'Tank capacity (ltr)',
                            'Tank Distance',
                            'Water_hours',
                            'Catchment Area (m2)',
                            'Catchment Material',
                            'Prod. cap. (ltr/hr)',
                            'water_lasts_month',
                            'Water source',
                            'Monthly bill (Tk)',
                            'repair_ren_is_required',
                            'Latitude',
                            'Longitude',
                            'Image',
                            'Onboarding Time',
                            'Actively Managed',
                            'Comments'
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

                        if($row->image != null){
                            $image = "http://www.hysawa.com/mis/public/sp_assets/SafePani_Waterpoints_Photo/".$row->image;
                        } else {
                            $image = "";
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
                            $row->functional_status,
                            $row->non_func_status,
                            $row->non_func_days,
                            $row->drinking_use,
                            $row->non_drink_reason,
                            $row->run_year_round,
                            $row->run_year_reason,
                            $row->install_year,
                            $row->install_by,
                            $row->pumping,
                            $row->depth,
                            $row->tanks_count,
                            $row->tank_material,
                            $row->tank_capacity,
                            $row->tank_distance,
                            $row->water_hours,
                            $row->catchment_area,
                            $row->catchment_material,
                            $row->capacity_liter,
                            $row->water_lasts_month,
                            $row->water_source,
                            $row->monthly_bill,
                            $row->is_om_req,
                            $row->lat,
                            $row->lon,
                            $image,
                            $row->onboard_date,
                            $is_manged,
                            $row->comments
                        ]);
                        $rowIndex++;
                    }

                }

           });
        })->download('csv');
    }
}
