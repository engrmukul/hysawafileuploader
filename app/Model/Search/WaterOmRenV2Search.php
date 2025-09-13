<?php

namespace App\Model\Search;

use App\Model\SPInfrastructure;
use Illuminate\Http\Request;
use DB;

class WaterOmRenV2Search
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

        $q =  DB::table('sp_repair_ren')
            ->leftjoin('sp_infrastructure', 'sp_repair_ren.id', '=', 'sp_infrastructure.ren_om_id')
            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
            ->leftjoin('fdistrict', 'sp_school.distid', '=', 'fdistrict.id')
            ->leftjoin('fupazila', 'sp_school.upid', '=', 'fupazila.id')
            ->leftjoin('funion', 'sp_school.unid', '=', 'funion.id')

            ->select('sp_repair_ren.*', 'sp_infrastructure.tech_type', 'sp_infrastructure.drinking_use', 'sp_infrastructure.water_id',
                'sp_infrastructure.is_active', 'sp_infrastructure.is_asses', 'sp_school.unid', 'sp_school.distid',
                'sp_school.id as school_id', 'sp_infrastructure.wq_status_id', 'sp_infrastructure.onboard_date',
                'sp_school.upid', 'sp_school.sch_name_en', 'sp_school.institution_id', 'sp_school.sch_type_edu',
                'fdistrict.distname', 'fupazila.upname', 'funion.unname')

            ->where(function($query) use($request) {

                $distid = $request->district_id;
                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $institution_type = $request->institution_type;
                $tech_type = $request->tech_type;
                $drinking_use = $request->drinking_use;
                $functional_status = $request->functional_status;
                $actively_managed = $request->actively_managed;
                $query_options = $request->query_options;
                $keyword = $request->keyword;

                if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                    $query->where('sp_school.distid', '=', '7');
                } else {
                    if(!empty($distid) && $distid != 'all' && $distid != null)
                    {
                        $query->where('sp_school.distid', '=', $distid);
                    }
                }

                if(!empty($upid) && $upid != 'all' && $upid != null)
                {
                    $query->where('sp_school.upid', '=', $upid);
                }

                if(!empty($unid) && $unid != 'all' && $unid != null)
                {
                    $query->where('sp_school.unid', '=', $unid);
                }

                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
                {
                    $query->where('sp_school.sch_type_edu', '=', $institution_type);
                }

                if(!empty($tech_type) && $tech_type != 'all' && $tech_type != null)
                {
                    $query->where('sp_infrastructure.tech_type', '=', $tech_type);
                }

                if(!empty($drinking_use) && $drinking_use != 'all' && $drinking_use != null)
                {
                    $query->where('sp_infrastructure.drinking_use', '=', $drinking_use);
                }

                if(!empty($functional_status) && $functional_status != 'all' && $functional_status != null)
                {
                    $query->where('sp_infrastructure.functional_status', '=', $functional_status);
                }

                if(!empty($keyword) && $keyword != 'all' && $keyword != null)
                {
                    $query->where('sp_infrastructure.water_id', 'like', "%$keyword%")
                        ->orWhere('sp_school.sch_name_en', 'like', "%$keyword%");
                }

                if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                    //$query->where('sp_infrastructure.is_active', '>', '1');
                    if(!empty($actively_managed) && $actively_managed != null)
                    {
                        if($actively_managed == 'Yes'){
                            $query->where('sp_infrastructure.is_active', '3');
                        } else if($actively_managed == 'No') {
                            $query->where('sp_infrastructure.is_active', '!=', '3');
                        }
                    } else {
                        $query->where('sp_infrastructure.is_active', '>', '1');
                    }
                } else {
                    if(!empty($actively_managed) && $actively_managed != null)
                    {
                        if($actively_managed == 'Yes'){
                            $query->where('sp_infrastructure.is_active', '1');
                        } else if($actively_managed == 'No') {
                            $query->where('sp_infrastructure.is_active', '!=', '1');
                        }
                    } else {
                        $query->where('sp_infrastructure.is_active', '1');
                    }
                }

                if($query_options != 'new_assessment')
                {
                    if($query_options == 'assess_updated'){
                        $query->where('sp_infrastructure.is_asses', '1');
                    } else if($query_options == 'assess_not_updated') {
                        $query->where('sp_infrastructure.is_asses', '0');
                    } else if($query_options == 'ren_req') {
                        $query->where('sp_infrastructure.is_ren_req', '1');
                    } else if($query_options == 'om_req') {
                        $query->where('sp_infrastructure.is_om_req', '1');
                    }
                }

            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
        //->where('sp_infrastructure.is_active', '1')
        ->orderBy('id', 'desc');

        if($this->pagination){
            $this->datas = $q->paginate(20);
        }else{
            $this->datas = $q->get();
        }
    }

  public function get()
  {
    return $this->datas;
  }
}
