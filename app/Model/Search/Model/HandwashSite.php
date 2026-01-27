<?php

namespace App\Model\Search\Model;

use App\Model\Search\Request\HandwashSitesSearchRequest;
use Illuminate\Http\Request;
use Auth;

class HandwashSite
{
  private $waters;
  private $request;

  public function __construct(Request $request)
  {
      $waterSearchRequest = new HandwashSitesSearchRequest;
      $waterSearchRequest->station_id = $request->input('station_id');
      $waterSearchRequest->region_id = \Auth::user()->region_id;
      $waterSearchRequest->created_by = $request->input('created_by');
      $waterSearchRequest->distid = \Auth::user()->distid;
      $waterSearchRequest->upid = \Auth::user()->upid;
      $waterSearchRequest->unid = $request->input('unid');
      $waterSearchRequest->proj_id = \Auth::user()->proj_id;
      $waterSearchRequest->ward_no = $request->input('ward_no');
      $waterSearchRequest->app_status = $request->input('app_status');
      $waterSearchRequest->imp_status = $request->input('imp_status');
      $waterSearchRequest->app_date = $request->input('app_date');

      $this->request = $waterSearchRequest;
      $this->process();
  }

  private function process()
  {

    
    $request = $this->request;

    $this->waters = \DB::table('handwash_sites')->where( function($query) use($request) {
        $station_id      = $request->station_id;
        $region_id      = \Auth::user()->region_id;
        $created_by     = $request->created_by;
        $distid         = \Auth::user()->distid;
        $upid           = \Auth::user()->upid;
        $unid           = $request->unid;
        $proj_id       = \Auth::user()->proj_id;
        $ward_no        = $request->ward_no;
        $app_status     = $request->app_status;
        $imp_status     = $request->imp_status;
        $app_date     = $request->app_date;

        if(!empty($region_id) && $region_id != '24' && $region_id != '2')
        {
            $query->where('handwash_sites.region_id', $region_id);
        }

        if(!empty($region_id) && ($region_id == '24' || $region_id == '2'))
        {
            $regIds = ['2', '24'];
            $query->whereIn('handwash_sites.region_id', $regIds);
        }

        if(!empty($app_date))
        {
            $query->where('handwash_sites.app_date', $app_date);
        }

        if(!empty($created_by))
        {
            $query->where('handwash_sites.created_by', $created_by);
        }

        if(!empty($distid) && $region_id != '24' && $region_id != '2')
        {
            $query->where('handwash_sites.distid', $distid);
        }

        if(!empty($upid) && $region_id != '24' && $region_id != '2')
        {
            $query->where('handwash_sites.upid', $upid);
        }

        if(!empty($unid) && $unid !='all')
        {
            $query->where('handwash_sites.unid', $unid);
        }

        if($region_id == '24' || $region_id == '2')
        {
            if($proj_id == '7')
            {
                $query->where('handwash_sites.proj_id', $proj_id);
            }
        } else {
            if(!empty($proj_id))
            {
                $query->where('handwash_sites.proj_id', $proj_id);
            }
        }

        if(!empty($ward_no))
        {
            $query->where('handwash_sites.ward_no', $ward_no);
        }

        if(!empty($app_status))
        {
            $query->where('handwash_sites.app_status', $app_status);
        }

        if(!empty($imp_status))
        {
            $query->where('handwash_sites.imp_status', $imp_status);
        }

        if(!empty($station_id))
        {
            $query->where('handwash_sites.id', $station_id);
        }

    })
        ->leftJoin('fdistrict', 'handwash_sites.distid', '=', 'fdistrict.id')
        ->leftJoin('fupazila', 'handwash_sites.upid', '=', 'fupazila.id')
        ->leftJoin('funion', 'handwash_sites.unid', '=', 'funion.id')
        ->leftJoin('region', 'handwash_sites.region_id', '=', 'region.id')
        ->leftJoin('project', 'handwash_sites.proj_id', '=', 'project.id')
        ->select(
            "region.region_name",
            "project.project",
            "fdistrict.distname",
            "fupazila.upname",
            "funion.unname",
            "handwash_sites.*")
        ->get();
  }

  public function get()
  {
    return $this->waters;
  }
}
