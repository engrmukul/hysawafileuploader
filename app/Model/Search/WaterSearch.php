<?php

namespace App\Model\Search;

use App\Model\Search\Request\WaterSearchRequest;
use App\Water;
use Illuminate\Http\Request;

class WaterSearch
{
  private $waters;
  private $request;
  public function __construct(WaterSearchRequest $request)
  {
    // \DB::enableQueryLog();
    $this->request = $request;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = \DB::table('tbl_water')->where(function($query) use($request) {

      $request->date_type = "App_date";

      $region_id = $this->request->region_id;
      $starting_date = $this->request->starting_date;
      $ending_date = $this->request->ending_date;
      $created_by = $this->request->created_by;
      $distid = $this->request->distid;
      $upid = $this->request->upid;
      $unid = $this->request->unid;
      $proj_id = $this->request->proj_id;
      $village = $this->request->village;
      $app_status = $this->request->app_status;
      $imp_status = $this->request->imp_status;

      $Tw_no = $this->request->Tw_no;
      $CDF_no = $this->request->CDF_no;
      $approve_id = $this->request->approve_id;
      $water_id = $this->request->water_id;

      if(!empty($region_id))
      {
        $query->where('region_id', '=', $region_id);
      }

      if($request->date_type != "")
      {
        if(!empty($starting_date))
        {
          $query->where($request->date_type, '=', $starting_date);
        }

        if(!empty($ending_date))
        {
          $query->where($request->date_type, '<=', $ending_date);
        }
      }

      if(!empty($created_by))
      {
        $query->where('created_by', $created_by);
      }

      if(!empty($distid))
      {
        $query->where('distid', $distid);
      }

      if(!empty($upid))
      {
        $query->where('upid', $upid);
      }

      if(!empty($unid))
      {
        $query->where('unid', $unid);
      }

      if(!empty($proj_id))
      {
        $query->where('proj_id', $proj_id);
      }

      if(!empty($village))
      {
        $query->where('Village', 'like', "%$village%");
      }

      if(!empty($app_status))
      {
        $query->where('app_status', $app_status);
      }

      if(!empty($approve_id))
        {
            $query->where('approve_id', $approve_id);
        }

      if(!empty($water_id))
        {
            $query->where('id', $water_id);
        }

      if(!empty($this->request->tech_type))
      {
        $tech_type = $this->request->tech_type;
        $query->where('Technology_Type', $tech_type);
      }

      if(!empty($imp_status))
      {
        $query->where('imp_status', $imp_status);
      }

      if(!empty($Tw_no))
      {
        $query->where('TW_No', $Tw_no);
      }

      if(!empty($CDF_no))
      {
        $query->where('CDF_no', $CDF_no);
      }

    })
    ->orderBy('id', 'DESC');

    $this->waters = $q->paginate(15);
  }

  public function get()
  {
    return $this->waters;
  }
}
