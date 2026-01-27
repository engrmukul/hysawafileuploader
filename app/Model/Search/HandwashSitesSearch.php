<?php

namespace App\Model\Search;

use App\HandwashSites;
use App\Model\Search\Request\HandwashSitesSearchRequest;
use App\Water;
use Illuminate\Http\Request;

class HandwashSitesSearch
{
  private $waters;
  private $request;
  public function __construct(HandwashSitesSearchRequest $request)
  {
    // \DB::enableQueryLog();
    $this->request = $request;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = \DB::table('handwash_sites')->where(function($query) use($request) {

      $station_id = $this->request->station_id;
      $region_id = $this->request->region_id;
      $created_by = $this->request->created_by;
      $distid = $this->request->distid;
      $upid = $this->request->upid;
      $unid = $this->request->unid;
      $proj_id = $this->request->proj_id;
      $ward_no = $this->request->ward_no;
      $app_status = $this->request->app_status;
      $imp_status = $this->request->imp_status;
      $app_date = $this->request->app_date;

     if(!empty($station_id)) {
         $query->where('id', $station_id);
     }

     if(!empty($region_id) && $region_id != '24' && $region_id != '2')
     {
         $query->where('region_id', '=', $region_id);
     }

     if(!empty($region_id) && ($region_id == '24' || $region_id == '2'))
     {
         $regIds = ['2', '24'];
         $query->whereIn('region_id', $regIds);
     }

     if(!empty($app_date))
     {
         $query->where('app_date', $app_date);
     }

     if(!empty($created_by))
     {
         $query->where('created_by', $created_by);
     }

     if(!empty($distid) && $region_id != '24' && $region_id != '2')
     {
         $query->where('distid', $distid);
     }

     if(!empty($upid) && $region_id != '24' && $region_id != '2')
     {
         $query->where('upid', $upid);
     }

     if(!empty($unid) && $unid !='all')
     {
         $query->where('unid', $unid);
     }

     if($region_id == '24' || $region_id == '2')
     {
         if($proj_id == '7')
         {
             $query->where('proj_id', $proj_id);
         }
     } else {
         if(!empty($proj_id))
         {
             $query->where('proj_id', $proj_id);
         }
     }

     if(!empty($ward_no))
     {
         $query->where('Ward_no', $ward_no);
     }

     if(!empty($app_status))
     {
         $query->where('app_status', $app_status);
     }

     if(!empty($imp_status))
     {
         $query->where('imp_status', $imp_status);
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
