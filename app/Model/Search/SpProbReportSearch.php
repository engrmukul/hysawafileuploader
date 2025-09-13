<?php

namespace App\Model\Search;

use App\Model\SPProblemReport;
use Illuminate\Http\Request;
use DB;

class SpProbReportSearch
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

        $q =  DB::table('sp_problem_report')

            ->leftjoin('sp_infrastructure', 'sp_infrastructure.water_id', '=', 'sp_problem_report.infrastructure_id')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')

            ->leftjoin('sp_problem_verification', 'sp_problem_report.id', '=', 'sp_problem_verification.problem_id')

            ->select('sp_problem_report.*', 'sp_problem_verification.eng_updated_at', 'sp_school.unid', 'sp_school.id as school_id', 'sp_school.upid', 'sp_school.institution_id',
                'sp_school.sch_name_en', 'sp_school.sch_type_edu', 'sp_infrastructure.id as water_row_id', 'sp_infrastructure.tech_type', 'sp_infrastructure.is_active')

            ->where(function($query) use($request) {

                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $institution_type = $request->institution_type;
                $prob_type = $request->prob_type;
                $prob_id = $request->prob_id;
                $main_status = $request->main_status;
                $prob_status = $request->prob_status;
                $start_date = $request->start_date;
                $end_date = $request->end_date;

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

                if(!empty($prob_type) && $prob_type != 'all' && $prob_type != null)
                {
                    $comparing_col = "ptype".$prob_type;
                    $query->where($comparing_col, '=', $prob_type);
                }

                if(!empty($prob_id) && $prob_id != 'all' && $prob_id != null)
                {
                    $query->where('sp_problem_report.id', '=', $prob_id);
                }

                if(!empty($prob_status) && $prob_status != 'all' && $prob_status != null)
                {
                    if($prob_status == '5'){
                        $query->where('sp_problem_report.is_resolved', '>', 1);
                    } else {
                        $query->where('sp_problem_report.is_resolved', '=', $prob_status);
                    }
                }

                if(!empty($main_status) && $main_status != 'all' && $main_status != null)
                {
                    if($main_status == '5'){
                        $query->where('sp_problem_report.is_maintenance', '>', 1);
                    } else {
                        $query->where('sp_problem_report.is_maintenance', '=', $main_status);
                    }
                }

                if(!empty($start_date) && !empty($end_date))
                {
                    $query->whereBetween(DB::raw("(DATE_FORMAT(sp_problem_verification.eng_updated_at,'%Y-%m-%d'))"), array($start_date, $end_date));
                }elseif(!empty($start_date)){
                    $query->where(DB::raw("(DATE_FORMAT(sp_problem_verification.eng_updated_at,'%Y-%m-%d'))"), '>=', $start_date);
                }elseif(!empty($end_date)){
                    $query->where(DB::raw("(DATE_FORMAT(sp_problem_verification.eng_updated_at,'%Y-%m-%d'))"), '<=', $end_date);
                }
            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
        ->orderBy('id','desc');

        if($this->pagination){
            $this->datas = $q->paginate(30);
        }else{
            $this->datas = $q->get();
        }
    }

  public function get()
  {
    return $this->datas;
  }
}
