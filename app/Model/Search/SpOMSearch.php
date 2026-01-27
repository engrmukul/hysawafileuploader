<?php

namespace App\Model\Search;

use App\Model\SPOM;
use Illuminate\Http\Request;
use DB;


class SpOMSearch
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

        $q = SPOM::with('infrastructure', 'infrastructure.school')

            ->where('infrastructure_id', '!=', NULL)

            ->where(function($query) use($request) {

                $distid = $request->district_id;
                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $tech_type = $request->tech_type;
                $quarter = $request->quarter;
                $year = $request->year;
                $institution_type = $request->institution_type;
                $prob_id = $request->prob_id;
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');

                if(!empty($distid) && $distid != 'all' && $distid != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($distid)
                    {
                        $query2->where('sp_school.distid', $distid);
                    });
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

                if(!empty($prob_id) && $prob_id != 'all' && $prob_id != null)
                {
                    $query->where('problem_id', '=', $prob_id);
                }

                if(!empty($start_date) && !empty($end_date))
                {
                    $query->whereBetween(DB::raw("(DATE_FORMAT(maintenance_time,'%Y-%m-%d'))"), array($start_date, $end_date));
                }elseif(!empty($start_date)){
                    $query->where(DB::raw("(DATE_FORMAT(maintenance_time,'%Y-%m-%d'))"), '>=', $start_date);
                }elseif(!empty($end_date)){
                    $query->where(DB::raw("(DATE_FORMAT(maintenance_time,'%Y-%m-%d'))"), '<=', $end_date);
                }

//                if($request->input('end_date') == ""){
//                    $end_date = date('Y-m-d H:i:s');
//                } else {
//                    $end_date = $request->input('end_date').' 23:59:59';
//                }
//
//                if($request->input('start_date') == ""){
//                    $start_date = '2022-03-01 00:00:01';
//                } else {
//                    $start_date = $request->input('start_date').' 00:00:01';
//                }
//
//                $query->whereBetween(DB::raw("(DATE_FORMAT(sp_om.maintenance_time,'%Y-%m-%d'))"), array($start_date, $end_date));
//
//                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
//                {
//                    $query->whereHas('infrastructure.school', function($query2) use($institution_type)
//                    {
//                        $query2->where('sp_school.sch_type_edu', $institution_type);
//                    });
//                }

            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
//        ->whereHas('infrastructure', function($query2)
//        {
//            $query2->where('sp_infrastructure.is_active', '1');
//        })
        ->orderBy('notification_time', 'desc');

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
