<?php

namespace App\Model\Search;

use App\Model\Search\Request\SanitationSearchRequest;
use App\Water;
use Illuminate\Http\Request;

class SanitationSearch
{
  private $waters;
  private $request;
  public function __construct(SanitationSearchRequest $request)
  {
    $this->request = $request;
    $this->process();
  }

  private function process()
  {

    $this->waters = \DB::table('sanitation')->where(function($query){

      $starting_date = $this->request->starting_date;
      $ending_date = $this->request->ending_date;
      $created_by = $this->request->created_by;
      $village = $this->request->village;
      $app_status     = $this->request->app_status;
      $imp_status     = $this->request->imp_status;
      $unid = $this->request->unid;
      $upid = $this->request->upid;
      $dist_id = $this->request->dist_id;
      $proj_id = $this->request->proj_id;
      $region_id = $this->request->region_id;

      $this->request->date_type = "app_date";
      
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
        $query->where('unid', $unid);
      }

      if(!empty($upid))
      {
        $query->where('upid', $upid);
      }

      if(!empty($dist_id))
      {
        $query->where('dist_id', $dist_id);
      }

      if(!empty($proj_id))
      {
        $query->where('proj_id', $proj_id);
      }

      if(!empty($region_id))
      {
        $query->where('region_id', $region_id);
      }

      if(!empty($starting_date))
      {
        $query->where($this->request->date_type , '=', $starting_date);
      }

      if(!empty($ending_date))
      {
        $query->where($this->request->date_type , '<=', $ending_date);
      }

      if(!empty($created_by))
      {
        $query->where('created_by', $created_by);
      }

      if(!empty($village))
      {
        $query->where('village', 'like', "%$village%");
      }

    })->orderBy('id', 'DESC')->paginate(10);
  }

  public function get()
  {
    return $this->waters;
  }
}
