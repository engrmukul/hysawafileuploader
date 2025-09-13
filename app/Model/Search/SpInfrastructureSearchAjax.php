<?php

namespace App\Model\Search;

use App\Model\SPInfrastructure;
use Illuminate\Http\Request;
use DB;

class SpInfrastructureSearchAjax
{
  private $datas;
  private $request;
  private $pagination;

  public function __construct($old, $pagination = false)
  {
    $this->old = $old;
    $this->datas;
    $this->pagination = $pagination;
    $this->process();
  }

    private function process()
    {
        $request = $this->old;

        $q =  DB::table('sp_infrastructure')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')

            ->select(array('sp_infrastructure.*', 'sp_school.unid', 'sp_school.id as school_id',
                'sp_school.upid', 'sp_school.sch_type_edu', DB::raw('count(*) as group_total')))

            ->groupBy('tech_type')

            ->where(function($query) use($request) {

                $upid = $request['upazila_id'];
                $unid = $request['union_id'];
                $drinking_use = $request['drinking_use'];
                $functional_status = $request['functional_status'];
                $institution_type = $request['institution_type'];

                $query->where('sp_infrastructure.is_active', '=', '1');

                if(!empty($upid) && $upid != 'all' && $upid != null)
                {
                    $query->where('sp_school.upid', '=', $upid);
                }

                if(!empty($unid) && $unid != 'all' && $unid != null)
                {
                    $query->where('sp_school.unid', '=', $unid);
                }

                if(!empty($drinking_use) && $drinking_use != 'all' && $drinking_use != null)
                {
                    $query->where('drinking_use', '=', $drinking_use);
                }

                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
                {
                    $query->where('sp_school.sch_type_edu', '=', $institution_type);
                }

                if(!empty($functional_status) && $functional_status != 'all' && $functional_status != null)
                {
                    $query->where('functional_status', '=', $functional_status);
                }

            });
        //->where('is_active', '1');
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');

        if($this->pagination){
            $this->datas = $q->paginate(20000);
        }else{
            $this->datas = $q->get();
        }
    }

  public function get()
  {
    return $this->datas;
  }
}
