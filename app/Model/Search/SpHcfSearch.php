<?php

namespace App\Model\Search;

use App\Model\SPSchool;
use Illuminate\Http\Request;

class SpHcfSearch
{
  private $datas;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->datas;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = SPSchool::with('district', 'upazila', 'union')
        ->orderBy('id', 'DESC')

        ->where('sch_type_edu', '=', 'Healthcare Facility')

        ->where(function($query) use($request) {

      $distid = $request->district_id;
      $upid = $request->upazila_id;
      $unid = $request->union_id;
      $hcf_type = $request->hcf_type;
      $keyword = $request->keyword;
      $actively_managed = $request->actively_managed;
      $waterpoint_counts = $request->waterpoint_counts;
      $drinking_counts = $request->drinking_counts;
      $waterpoint_status = $request->waterpoint_status;
      $query_options = $request->query_options;


    if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
        $query->where('distid', '=', '7');
    } else {
        if(!empty($distid) && $distid != 'all' && $distid != null)
        {
            $query->where('distid', '=', $distid);
        }
    }

     if(!empty($upid) && $upid != 'all' && $upid != null)
     {
        $query->where('upid', '=', $upid);
     }

      if(!empty($unid) && $unid != 'all' && $unid != null)
      {
        $query->where('unid', '=', $unid);
      }

      if(!empty($hcf_type) && $hcf_type != 'all' && $hcf_type != null)
      {
        $query->where('hcf_type', '=', $hcf_type);
      }

      if(!empty($keyword) && $keyword != 'all' && $keyword != null)
        {
            $query->where('institution_id', 'like', "%$keyword%")
                ->orWhere('sch_name_en', 'like', "%$keyword%");
        }

    if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
        $query->where('sp_school.is_active', '>', 2);
    } else {
        if(!empty($actively_managed) && $actively_managed != null)
        {
            if($actively_managed == 'Yes'){
                $query->where('sp_school.is_active', '1');
            } else if($actively_managed == 'No') {
                $query->where('sp_school.is_active', '!=', '1');
            }
        } else {
            $query->where('sp_school.is_active', '1');
        }
    }

    if($waterpoint_counts != 'all' && $waterpoint_counts != null)
    {
        $query->where('sp_school.water_counts', '=', $waterpoint_counts);
    }

    if($drinking_counts != 'all' && $drinking_counts != null)
    {
        $query->where('sp_school.drinking_counts', '=', $drinking_counts);
    }

    if($query_options != 'new_assessment')
    {
        if($query_options == 'assess_updated'){
            $query->where('sp_school.is_asses', '1');
        } else if($query_options == 'assess_not_updated') {
            $query->where('sp_school.is_asses', '0');
        }
    }

    if($waterpoint_status != 'all' && $waterpoint_status != null)
    {
        if($waterpoint_status == 'Yes'){
            $query->where('sp_school.func_counts', '>', '0');
        } else if($waterpoint_status == 'No') {
            $query->where('sp_school.func_counts', '0');
        }
    }

    });
   //->where('sp_school.is_active', '1');
    //->where('created_by', '=', auth()->user()->id)
    //->groupBy('id');

    if($this->pagination){
      $this->datas = $q->paginate(10000);
    }else{
      $this->datas = $q->get();
    }
  }

  public function get()
  {
    return $this->datas;
  }
}
