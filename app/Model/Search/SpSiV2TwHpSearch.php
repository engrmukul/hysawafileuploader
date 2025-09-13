<?php

namespace App\Model\Search;

use App\Model\SPSanInspectionV2;
use App\Model\SPSanitaryInspection;
use Illuminate\Http\Request;

class SpSiV2TwHpSearch
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

        $q = SPSanInspectionV2::with('infrastructure', 'infrastructure.school', 'SpSanAnswer', 'SpSanAnswerCorr', 'SpSanAnswerCorr.SpSanCorrective', 'infrastructure.school.district',
            'infrastructure.school.upazila', 'infrastructure.school.union')

            ->where('infrastructure_id', '!=', NULL)

            ->where('infrastructure_cat', 'tw_hand')

            ->where(function($query) use($request) {

                $distid = $request->district_id;
                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $institution_type = $request->institution_type;
                $tech_type = $request->tech_type;
                $quarter = $request->quarter;
                $year = $request->year;
                $risk_level = $request->sanitary_risk;

                if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                    $query->whereHas('infrastructure.school', function($query2) use($distid)
                    {
                        $query2->where('sp_school.distid', '7');
                    });
                } else {
                    if(!empty($distid) && $distid != 'all' && $distid != null)
                    {
                        $query->whereHas('infrastructure.school', function($query2) use($distid)
                        {
                            $query2->where('sp_school.distid', $distid);
                        });
                    }
                }

                if(!empty($upid) && $upid != 'all' && $upid != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($upid)
                    {
                        $query2->where('sp_school.upid', $upid);
                    });
                }

                if(!empty($unid) && $unid != 'all' && $unid != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($unid)
                    {
                        $query2->where('sp_school.unid', $unid);
                    });
                }

                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($institution_type)
                    {
                        $query2->where('sp_school.sch_type_edu', $institution_type);
                    });
                }

                if(!empty($tech_type) && $tech_type != 'all' && $tech_type != null)
                {
                    $query->whereHas('infrastructure', function($query2) use($tech_type)
                    {
                        $query2->where('sp_infrastructure.tech_type', $tech_type);
                    });
                }

                if(!empty($quarter) && $quarter != 'all' && $quarter != null)
                {
                    $query->where('quarter', '=', $quarter);
                }

                if(!empty($year) && $year != 'all' && $year != null)
                {
                    $query->where('year', '=', $year);
                }

                if(!empty($risk_level) && $risk_level != 'all' && $risk_level != null)
                {
                    $query->where('sanitary_risk', '=', $risk_level);
                }

            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
        ->whereHas('infrastructure', function($query2)
        {
            if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                $query2->where('sp_infrastructure.is_active', '>' ,'1');
            } else {
                $query2->where('sp_infrastructure.is_active', '1');
            }
        })
        ->orderBy('inspection_date', 'desc');

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
