<?php

namespace App\Model\Search;

use App\Model\Water;
use Illuminate\Http\Request;

class WaterQualityAgencySearch
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

    $q = Water::with('region', 'district', 'upazila', 'union', 'project')->where(function($query) use($request) {

      $region_id = $request->region_id;
      $distid = $request->district_id;
      $upid = $request->upazila_id;
      $unid = $request->union_id;

      $TW_No = $request->TW_No;
      $CDF_no = $request->CDF_no;
      $app_date = $request->App_date;
      $test_status = $request->test_status;


      $starting_date = $request->starting_date; 
      $ending_date = $request->ending_date; 

      if($starting_date != "" && $ending_date != "")
      {
        $query->whereDate('created_at', '>=', $starting_date)->whereDate('created_at', '<=', $ending_date);
      }
      elseif($starting_date)
      {
        $query->whereDate('created_at', '=', $starting_date);
      }


      if(!empty($region_id) && $region_id != '2' && $region_id != '24')
      {
        $query->where('region_id', '=', $region_id);
      }

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

     if(!empty($app_date))
      {
        $query->where('App_date', '=', $app_date);
      }

     if(!empty($test_status))
     {
         if($test_status == 'Without Test'){
             $query->where('wq_test_date', '=', NULL);
         } else if ($test_status == 'With Test') {
             $query->where('wq_test_date', '!=', NULL);
         }
     }

    })
    ->where('app_status', 'Approved')
    ->whereIn('Technology_Type', ['DTW', 'DSP', 'Test TW', 'Pipeline'])
    ->groupBy('id')
    ->orderBy('created_at', 'DESC');

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
