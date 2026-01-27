<?php

namespace App\Model\Search;

use Illuminate\Http\Request;
use DB;

class SpOMSearchAjax
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

        $q = DB::table('sp_om')

            ->leftjoin('sp_infrastructure', 'sp_om.infrastructure_id', '=', 'sp_infrastructure.id')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')

            ->select(array('sp_infrastructure.tech_type', 'sp_om.quarter', 'sp_om.year',
                'sp_school.is_active', 'sp_school.unid', 'sp_school.upid',
                DB::raw('count(*) as group_total')))

            ->groupBy(['sp_om.quarter', 'sp_infrastructure.tech_type'])

            ->where('sp_school.is_active', '1')

            ->where('sp_om.infrastructure_id', '!=', NULL)

            ->where(function($query) use($request) {

                $upid = $request['upazila_id'];
                $unid = $request['union_id'];
                $tech_type = $request['tech_type'];
                $institution_type = $request['institution_type'];
                $year = $request['year'];
                $quarter = $request['quarter'];

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
                    $query->where('sp_om.year', '=', $year);
                }

                if(!empty($quarter) && $quarter != 'all' && $quarter != null)
                {
                    $query->where('sp_om.quarter', '=', $quarter);
                }

            });
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
