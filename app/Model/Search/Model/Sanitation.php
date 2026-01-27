<?php

namespace App\Model\Search\Model;

use App\Model\Search\Request\SanitationSearchRequest;
use Illuminate\Http\Request;

class Sanitation
{
  private $sanitations;
  private $request;

  public function __construct(Request $request)
  {
    $sanSearchReq = new SanitationSearchRequest;
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

    $this->sanitations = \DB::table('sanitation')->where(function($query) use ($request) {
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
        $query->where('sanitation.unid', $unid);
      }

      if(!empty($upid))
      {
        $query->where('sanitation.upid', $upid);
      }

      if(!empty($dist_id))
      {
        $query->where('sanitation.dist_id', $dist_id);
      }

      if(!empty($proj_id))
      {
        $query->where('sanitation.proj_id', $proj_id);
      }

      if(!empty($region_id))
      {
        $query->where('sanitation.region_id', $region_id);
      }

      if($request->date_type != "")
      {
        if(!empty($starting_date))
        {
          $query->where("sanitation.".$request->date_type, '>=', $starting_date);
        }

        if(!empty($ending_date))
        {
          $query->where("sanitation.".$request->date_type, '<=', $ending_date);
        }
      }

      if(!empty($created_by))
      {
        $query->where('sanitation.created_by', $created_by);
      }

      if(!empty($village))
      {
        $query->where('sanitation.village', 'like', "%$village%");
      }
    })
    ->leftJoin('fdistrict', 'sanitation.dist_id',    '=', 'fdistrict.id')
    ->leftJoin('fupazila',  'sanitation.upid',      '=', 'fupazila.id')
    ->leftJoin('funion',    'sanitation.unid',      '=', 'funion.id')
    ->leftJoin('region',    'sanitation.region_id', '=', 'region.region_id')
    ->leftJoin('project',   'sanitation.proj_id',   '=', 'project.id')
    ->select(
      "sanitation.id",
      "region.region_name",
      "project.project",
      "fdistrict.distname",
      "fupazila.upname",
      "funion.unname",

      "sanitation.app_status",
      "sanitation.imp_status",
      "sanitation.app_date",
      "sanitation.cdfno",

      "sanitation.cons_type",
      "sanitation.village",
      "sanitation.maintype",
      "sanitation.subtype",

      "sanitation.name",
      "sanitation.malechamber",
      "sanitation.femalechamber",
      "sanitation.male_ben",
      "sanitation.fem_ben",

      "sanitation.created_by",
      "sanitation.updated_by",
      "sanitation.created_at",
      "sanitation.updated_at"
    )->get();
  }

  public function get()
  {
    return $this->sanitations;
  }
}
