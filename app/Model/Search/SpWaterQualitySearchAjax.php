<?php

namespace App\Model\Search;

use Illuminate\Http\Request;
use DB;

class SpWaterQualitySearchAjax
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

        $q = DB::table('sp_water_quality')

            ->leftjoin('sp_infrastructure', 'sp_water_quality.infrastructure_id', '=', 'sp_infrastructure.id')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')

            ->select(array('sp_infrastructure.tech_type', 'sp_water_quality.quarter', 'sp_water_quality.year',
                'sp_school.unid', 'sp_school.upid', 'sp_water_quality.risk_level', 'sp_infrastructure.is_active',
                DB::raw('count(*) as group_total')))

            ->groupBy(['sp_infrastructure.tech_type', 'sp_water_quality.risk_level'])

            ->where('sp_water_quality.infrastructure_id', '!=', NULL)

            ->where(function($query) use($request) {

                $upid = $request['upazila_id'];
                $unid = $request['union_id'];
                $tech_type = $request['tech_type'];
                $institution_type = $request['institution_type'];
                $year = $request['year'];
                $quarter = $request['quarter'];
                $parameter = $request['parameter'];

                if(!empty($upid) && $upid != 'all' && $upid != null)
                {
                    $query->where('sp_school.upid', $upid);
                }

                if(!empty($unid) && $unid != 'all' && $unid != null)
                {
                    $query->where('sp_school.unid', $unid);
                }

                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
                {
                    $query->where('sp_school.sch_type_edu', $institution_type);
                }

                if(!empty($tech_type) && $tech_type != 'all' && $tech_type != null)
                {
                    $query->where('sp_infrastructure.tech_type', '=', $tech_type);
                }

                if(!empty($year) && $year != 'all' && $year != null)
                {
                    $query->where('sp_water_quality.year', '=', $year);
                }

                if(!empty($quarter) && $quarter != 'all' && $quarter != null)
                {
                    $query->where('sp_water_quality.quarter', '=', $quarter);
                }

                if(!empty($parameter) && $parameter != 'all' && $parameter != null)
                {
                    $query->where('parameter', '=', $parameter);
                }

            })
        ->where('sp_infrastructure.is_active', '1');
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
