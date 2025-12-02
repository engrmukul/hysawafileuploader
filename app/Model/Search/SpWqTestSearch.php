<?php

namespace App\Model\Search;

use App\Model\SPWaterQuality;
use Illuminate\Http\Request;

class SpWqTestSearch
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

    $q = SPWaterQuality::with('infrastructure', 'infrastructure.school',
        'infrastructure.school.district', 'infrastructure.school.upazila', 'infrastructure.school.union')

        ->where('infrastructure_id', '!=', NULL)

        ->where(function($query) use($request) {

            $distid = $request->district_id;
            $upid = $request->upazila_id;
            $unid = $request->union_id;
            $institution_type = $request->institution_type;
            $institution_id = $request->institution_id;
            $sample_id = $request->sample_id;
            $tech_type = $request->tech_type;
            $quarter = $request->quarter;
            $year = $request->year;
            $parameter = $request->parameter;
            $risk_level = $request->risk_level;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

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

            if(!empty($parameter) && $parameter != 'all' && $parameter != null)
            {
                $query->where('parameter', '=', $parameter);
            }

            if(!empty($risk_level) && $risk_level != 'all' && $risk_level != null)
            {
                $query->where('risk_level', '=', $risk_level);
            }

            if(!empty($institution_id) && $institution_id != 'all' && $institution_id != null)
            {
                $query->whereHas('infrastructure.school', function($query2) use($institution_id)
                {
                    $query2->where('sp_school.id', $institution_id);
                });
            }

            if(!empty($sample_id) && $sample_id != 'all' && $sample_id != null)
            {
                $query->whereHas('infrastructure.school', function($query2) use($sample_id)
                    {
                        $query2->where('sp_school.sch_name_en','like', "%$sample_id%");
                    })
                    ->orWhere('sample_id', 'like', "%$sample_id%")
                    ->orWhere('water_id', 'like', "%$sample_id%");
            }

            if(!empty($start_date) && !empty($end_date))
            {
                $query->whereBetween('test_date', array($start_date, $end_date));
            }elseif(!empty($start_date)){
                $query->where('test_date', '>=', $start_date);
            }elseif(!empty($end_date)){
                $query->where('test_date', '<=', $end_date);
            }

        })
      //->where('created_by', '=', auth()->user()->id)
      //->groupBy('id');
      ->whereHas('infrastructure', function($query2)
      {
          if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
              $query2->where('sp_infrastructure.is_active', '>', 1);
          } else {
              $query2->where('sp_infrastructure.is_active', '>', 0);
          }
      })
      ->orderBy('test_date', 'desc');

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
