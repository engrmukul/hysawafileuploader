<?php

namespace App\Model\Search\Model;

use App\Model\Search\Request\SanitationHouseholdRequest;
use Illuminate\Http\Request;

class Household
{
  private $sanitations;
  private $request;

  public function __construct(Request $request)
  {
    $sanSearchReq = new SanitationHouseholdRequest;
    $sanSearchReq->starting_date = $request->input('starting_date');
    $sanSearchReq->ending_date = $request->input('ending_date');
    $sanSearchReq->created_by = $request->input('created_by');

    $sanSearchReq->unid = \Auth::user()->unid;
    $sanSearchReq->upid = $request->input('upid');
    $sanSearchReq->dist_id = $request->input('dist_id');
    $sanSearchReq->proj_id = $request->input('proj_id');
    $sanSearchReq->region_id = $request->input('region_id');
    $sanSearchReq->ward_no = $request->input('ward_no');
    $sanSearchReq->village = $request->input('village');
    $sanSearchReq->app_status     = $request->input('app_status');
    $sanSearchReq->imp_status     = $request->input('imp_status');

    $request->date_type = "app_date";

    $this->request = $sanSearchReq;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $this->sanitations = \DB::table('household')->where(function($query) use ($request) {
      $starting_date = $request->starting_date;
      $ending_date   = $request->ending_date;
      $created_by    = $request->created_by;
      $village       = $request->village;
      $unid          = $request->unid;
      $upid          = $request->upid;
      $dist_id       = $request->dist_id;
      $proj_id       = $request->proj_id;
      $region_id     = $request->region_id;
      $app_status     = $request->app_status;
      $imp_status     = $request->imp_status;
      
      $request->date_type = "app_date";
      
      
      if(!empty($app_status))
        {
          $query->where('app_status', $app_status);
        }

        if(!empty($imp_status))
        {
          $query->where('imp_status', $imp_status);
        }
        
      if(!empty($unid))
      {
        $query->where('household.unid', $unid);
      }

      if(!empty($upid))
      {
        $query->where('household.upid', $upid);
      }

      if(!empty($dist_id))
      {
        $query->where('household.dist_id', $dist_id);
      }

      if(!empty($proj_id))
      {
        $query->where('household.proj_id', $proj_id);
      }

      if(!empty($region_id))
      {
        $query->where('household.region_id', $region_id);
      }

      if($request->date_type != "")
      {
        if(!empty($starting_date))
        {
          $query->where("household.".$request->date_type, '>=', $starting_date);
        }

        if(!empty($ending_date))
        {
          $query->where("household.".$request->date_type, '<=', $ending_date);
        }
      }

      if(!empty($created_by))
      {
        $query->where('household.created_by', $created_by);
      }

      if(!empty($village))
      {
        $query->where('household.village', 'like', "%$village%");
      }
    })
    ->leftJoin('fdistrict', 'household.dist_id',    '=', 'fdistrict.id')
    ->leftJoin('fupazila',  'household.upid',      '=', 'fupazila.id')
    ->leftJoin('funion',    'household.unid',      '=', 'funion.id')
    ->leftJoin('region',    'household.region_id', '=', 'region.region_id')
    ->leftJoin('project',   'household.proj_id',   '=', 'project.id')
    ->select(
      "household.id",
      "region.region_name",
      "project.project",
      "fdistrict.distname",
      "fupazila.upname",
      "funion.unname",

      "household.app_status",
      "household.imp_status",
      "household.app_date",
      "household.cdfno",
      "household.imp_date",
      "household.nid",
      "household.approve_id",

      "household.village",
      "household.hh_name",
      "household.father_husband",
      "household.age",

      "household.occupation",
      "household.mobile",
      "household.economic_status",
      "household.social_safetynet",
      "household.male",

      "household.female",
      "household.children",
      "household.disable",
      "household.ownership_type",
      "household.latrine_type",

      "household.latrine_details",
      "household.total_cost",
      "household.contribute_amount",
      "household.latitude",
      "household.longitude",

      "household.created_by",
      "household.updated_by",
      "household.created_at",
      "household.updated_at"
    )->get();
  }

  public function get()
  {
    return $this->sanitations;
  }
}
