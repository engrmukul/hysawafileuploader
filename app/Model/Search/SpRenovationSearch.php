<?php

namespace App\Model\Search;

use App\Model\SPRenovation;
use Illuminate\Http\Request;

class SpRenovationSearch
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

        $q = SPRenovation::with('infrastructure', 'infrastructure.school')

            ->where('infrastructure_id', '!=', NULL)

            ->where(function($query) use($request) {

                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $tech_type = $request->tech_type;
                $quarter = $request->quarter;
                $year = $request->year;
                $institution_type = $request->institution_type;

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

                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($institution_type)
                    {
                        $query2->where('sp_school.sch_type_edu', $institution_type);
                    });
                }

            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
        //->orderBy('id', 'desc');
//        ->whereHas('infrastructure.school', function($query2)
//        {
//            $query2->where('sp_school.is_active', "1");
//        })
        ->orderBy('year', 'DESC')->orderBy('quarter', 'DESC');

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
