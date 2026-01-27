<?php

namespace App\Model\Search;

use App\Model\SPSanitaryInspection;
use Illuminate\Http\Request;
use DB;

class SpSanitaryInspectionSearchAjax
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

        $q = DB::table('sp_sanitary_inspection')

            ->leftjoin('sp_infrastructure', 'sp_sanitary_inspection.infrastructure_id', '=', 'sp_infrastructure.id')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')

            ->select(array('sp_infrastructure.tech_type', 'sp_sanitary_inspection.quarter', 'sp_sanitary_inspection.year',
                'sp_school.unid', 'sp_school.upid', 'sp_school.sch_type_edu', 'sp_sanitary_inspection.sanitary_risk',
                DB::raw('count(*) as group_total')))

            ->groupBy(['sp_infrastructure.tech_type', 'sp_sanitary_inspection.quarter'])

            ->where('sp_sanitary_inspection.infrastructure_id', '!=', NULL)

            ->where(function($query) use($request) {

                $upid = $request['upazila_id'];
                $unid = $request['union_id'];
                $institution_type = $request['institution_type'];
                $year = $request['year'];
                $risk_level = $request['sanitary_risk'];

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
                    $query->where('sp_school.sch_type_edu', '=', $institution_type);
                }

                if(!empty($year) && $year != 'all' && $year != null)
                {
                    $query->where('sp_sanitary_inspection.year', '=', $year);
                }

                if(!empty($risk_level) && $risk_level != 'all' && $risk_level != null)
                {
                    $query->where('sanitary_risk', '=', $risk_level);
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
