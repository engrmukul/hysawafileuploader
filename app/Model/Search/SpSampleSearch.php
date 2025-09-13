<?php

namespace App\Model\Search;

use App\Model\SPProblemReport;
use Illuminate\Http\Request;
use DB;

class SpSampleSearch
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

        $q =  DB::table('sp_sample_collection')

            ->leftjoin('sp_infrastructure', 'sp_infrastructure.id', '=', 'sp_sample_collection.infrastructure_id')

            ->leftjoin('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
            ->leftjoin('fdistrict', 'sp_school.distid', '=', 'fdistrict.id')
            ->leftjoin('fupazila', 'sp_school.upid', '=', 'fupazila.id')
            ->leftjoin('funion', 'sp_school.unid', '=', 'funion.id')

            ->select('sp_sample_collection.*', 'sp_school.unid', 'sp_school.id as school_id', 'sp_school.upid', 'sp_school.distid',
                'sp_school.sch_name_en', 'sp_school.sch_type_edu', 'sp_infrastructure.id as water_row_id', 'sp_infrastructure.is_active',
                'fdistrict.distname', 'fupazila.upname', 'funion.unname')

            ->where(function($query) use($request) {

                $distid = $request->district_id;
                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $institution_type = $request->institution_type;
                $sample_id = $request->sample_id;
                $sample_cat = $request->sample_cat;
                $quarter = $request->quarter;
                $year = $request->year;

                if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                    $query->where('sp_school.distid', '=', '7');
                } else {
                    if(!empty($distid) && $distid != 'all' && $distid != null)
                    {
                        $query->where('sp_school.distid', '=', $distid);
                    }
                }


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

                if(!empty($sample_id) && $sample_id != 'all' && $sample_id != null)
                {
                    $query->where('sp_sample_collection.sample_id', '=', $sample_id);
                }

                if(!empty($quarter) && $quarter != 'all' && $quarter != null)
                {
                    $query->where('quarter', '=', $quarter);
                }

                if(!empty($year) && $year != 'all' && $year != null)
                {
                    $query->where('year', '=', $year);
                }

                if(!empty($sample_cat) && $sample_cat != 'all' && $sample_cat != null)
                {
                    if($sample_cat == 'Field Blank' || $sample_cat == 'Sample 1' || $sample_cat == 'Sample 2'){
                        if($sample_cat == 'Field Blank') $sample_cat = 'FB';
                        $query->where('sp_sample_collection.sample_no', '=', $sample_cat);
                    } else {
                        $query->where('sp_sample_collection.sample_cat', '=', $sample_cat);
                    }
                }

                if(!empty($sample_id) && $sample_id != 'all' && $sample_id != null)
                {
                    $query->where('sp_sample_collection.sample_id', 'like', "%$sample_id%")
                        ->orWhere('sp_school.sch_name_en', 'like', "%$sample_id%")
                        ->orWhere('sp_sample_collection.water_id', 'like', "%$sample_id%")
                        ->orWhere('sp_sample_collection.id', 'like', "%$sample_id%");
                }

                if(auth()->user()->roles()->first()->name == 'sp_survey_supervisor'){
                    $query->where('sp_infrastructure.is_active', '>', '1');
                } else {
                    $query->where('sp_infrastructure.is_active', "1");
                }
            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
        ->orderBy('updated_at','desc')
        ->orderBy('id','desc');

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
