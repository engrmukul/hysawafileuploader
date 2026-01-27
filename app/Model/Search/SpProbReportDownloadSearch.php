<?php

namespace App\Model\Search;

use App\Model\SPProblemReport;
use Illuminate\Http\Request;
use DB;

class SpProbReportDownloadSearch
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

            ->leftjoin('sp_problem_verification', 'sp_problem_verification.problem_id', '=', 'sp_problem_report.id')

            ->leftjoin('sp_infrastructure', 'sp_infrastructure.water_id', '=', 'sp_problem_report.infrastructure_id')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')

            ->select('sp_problem_report.*',
                'sp_problem_verification.eng_main_date',
                'sp_problem_verification.eng_main_comment',
                'sp_problem_verification.user_main_date',
                'sp_problem_verification.user_veri_comment',
                'sp_problem_verification.prob_identification',
                'sp_problem_verification.identification_date',
                'sp_problem_verification.user_resolve_status',
                'sp_problem_verification.materials_cost',
                'sp_problem_verification.from_stock',
                'sp_problem_verification.labor_cost',
                'sp_problem_verification.transport_cost',
                'sp_problem_verification.tank_cleaning_cost',
                'sp_problem_verification.electricity_bill',
                'sp_problem_verification.mat_vat',
                'sp_problem_verification.mat_tax',
                'sp_problem_verification.main_cost',
                'sp_problem_verification.mtype1',
                'sp_problem_verification.mtype2',
                'sp_problem_verification.mtype3',
                'sp_problem_verification.mtype4',
                'sp_problem_verification.mtype5',
                'sp_problem_verification.mtype6',
                'sp_problem_verification.mtype7',
                'sp_problem_verification.mtype8',
                'sp_problem_verification.eng_updated_at',
                'sp_problem_verification.eng_updated_by',
                'sp_problem_verification.user_updated_at',
                'sp_problem_verification.user_updated_by',
                'sp_school.unid', 'sp_school.id as school_id', 'sp_school.upid',
                'sp_school.sch_name_en',
                'sp_school.sch_type_edu',
                'sp_school.institution_id',
                'sp_infrastructure.tech_type',
                'sp_infrastructure.is_active',
                'sp_infrastructure.id as water_row_id')

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

                if(!empty($prob_type))
                {
                    $comparing_col = "ptype".$prob_type;
                    $query->where($comparing_col, '=', $prob_type);
                }

                if(!empty($prob_id))
                {
                    $query->where('sp_problem_report.id', '=', $prob_id);
                }

                if(!empty($prob_status))
                {
                    $query->where('sp_problem_report.is_resolved', '=', $prob_status);
                }

                if(!empty($main_status))
                {
                    $query->where('sp_problem_report.is_maintenance', '=', $main_status);
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
