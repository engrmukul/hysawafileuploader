<?php

namespace App\Model\Search;

use App\Model\Water;
use App\Model\WaterQualityResult;
use Illuminate\Http\Request;

class WaterQualityTesterSearch
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

    $submitted_by = $request->submitted_by;
    $distid = $request->district_id;
    $upid = $request->upazila_id;
    $unid = $request->union_id;
    $starting_date = $request->starting_date;
    $ending_date = $request->ending_date;

    if(!empty($submitted_by) && $submitted_by == auth()->user()->name){

//        $q = Water::where(function($query) use($request) {
//            $query->orderBy('wq_test_date', 'DESC');
//        })
//            ->whereHas('qualityResults', function($query) use($request){
//                    $query->where('created_by', auth()->user()->id);
//         });

        if($starting_date != "" && $ending_date != ""){
            if(!empty($unid))
            {
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->whereDate('report_date', '>=', $starting_date)->whereDate('report_date', '<=', $ending_date)
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.unid', $unid)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }elseif(!empty($upid)){
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->whereDate('report_date', '>=', $starting_date)->whereDate('report_date', '<=', $ending_date)
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.upid', $upid)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }elseif(!empty($distid)){
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->whereDate('report_date', '>=', $starting_date)->whereDate('report_date', '<=', $ending_date)
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->where('tbl_water.distid', $distid)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }else{
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->whereDate('report_date', '>=', $starting_date)->whereDate('report_date', '<=', $ending_date)
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }
        } else {
            if(!empty($unid))
            {
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.unid', $unid)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }elseif(!empty($upid)){
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.upid', $upid)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }elseif(!empty($distid)){
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->where('tbl_water.distid', $distid)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }else{
                $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
                    ->where('water_quality_results.created_by', auth()->user()->id)
                    ->where('tbl_water.region_id', auth()->user()->region_id)
                    ->orderBy('water_quality_results.report_date','DESC')
                    ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                        'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                        'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                        'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                        'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                        'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
            }
        }

    } elseif($starting_date != "" && $ending_date != ""){
        $q = Water::join('water_quality_results', 'water_quality_results.water_id', '=', 'tbl_water.id')
            ->whereDate('report_date', '>=', $starting_date)->whereDate('report_date', '<=', $ending_date)
            ->where('tbl_water.region_id', auth()->user()->region_id)
            ->orderBy('water_quality_results.report_date','DESC')
            ->select('tbl_water.id', 'tbl_water.CDF_no', 'tbl_water.Technology_Type', 'tbl_water.Ward_no', 'tbl_water.Village', 'tbl_water.App_date',
                'tbl_water.Landowner', 'tbl_water.Caretaker_male', 'tbl_water.unid', 'tbl_water.upid',
                'water_quality_results.arsenic as wq_Arsenic', 'water_quality_results.fe as wq_fe',
                'water_quality_results.cl as wq_cl', 'water_quality_results.mn as wq_mn', 'water_quality_results.tc as wq_tc',
                'water_quality_results.fc as wq_fc', 'water_quality_results.report_date as wq_test_date',
                'water_quality_results.lon as x_coord', 'water_quality_results.lat as y_coord');
    } else {
        $q = Water::orderBy('wq_test_date', 'DESC')

            ->where(function($query) use($request) {

            $distid = $request->district_id;
            $upid = $request->upazila_id;
            $unid = $request->union_id;
            $TW_No = $request->TW_No;
            $CDF_no = $request->CDF_no;

            if(!empty($distid))
            {
                $query->where('distid', '=', $distid);
            }

            if(!empty($upid))
            {
                $query->where('upid', '=', $upid);
            }

            if(!empty($unid))
            {
                $query->where('unid', '=', $unid);
            }

            if(!empty($TW_No))
            {
                $query->where('id', 'like', "%$TW_No%");
            }

            if(!empty($CDF_no))
            {
                $query->where('CDF_no', 'like', "%$CDF_no%");
            }

            $query->where('app_status', 'Approved');
            //$query->whereNotIn('Technology_Type', ['Raised Platform']);
            $query->where('region_id', auth()->user()->region_id);
            $query->where('proj_id', auth()->user()->proj_id);

        });
    }

    if($this->pagination){
      $this->datas = $q->paginate(15);
    }else{
      $this->datas = $q->get();
    }
  }

  public function get()
  {
    return $this->datas;
  }
}
