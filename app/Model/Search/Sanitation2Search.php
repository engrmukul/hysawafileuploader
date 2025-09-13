<?php

namespace App\Model\Search;

use App\Model\Sanitation;
use Illuminate\Http\Request;

class Sanitation2Search
{
  private $datas;
  private $approvedData;
  private $completedData;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->datas;
    $this->approvedData;
    $this->completedData;
    $this->pagination = $pagination;
   // \DB::enableQueryLog();
    $this->process();
    //\Log::info(\DB::getQueryLog());
  }

  private function process()
  {
    $request = $this->request;

    $q = Sanitation::with('project')
      ->with('region')
      ->with('union')
      ->with('upazila')
      ->with('district')

      ->where(function($query) use($request) {

        $request->date_type = "App_date";
        $starting_date = $request->input('starting_date');
        //$ending_date   = $request->input('ending_date');

        if(!empty($starting_date))
        {
          $query->where('App_date', '=', $starting_date);
        }

        if($request->has('project_id') && $request->project_id != ""){
          $query->where('sanitation.proj_id', $request->project_id);
        }

        if($request->has('region_id') && $request->region_id != ""){
          $query->where('sanitation.region_id', $request->region_id);
        }



        if($request->has('union_id') && $request->union_id != ""){
          $query->where('sanitation.unid', $request->union_id);
        }

        if($request->has('upazila_id') && $request->upazila_id != ""){
          $query->where('sanitation.upid', $request->upazila_id);
        }




        if($request->has('district_id') && $request->district_id != ""){
          $query->where('sanitation.dist_id', $request->district_id);
        }

        if($request->has('cdf_no') && $request->cdf_no != ""){
          $query->where('sanitation.cdfno', $request->cdf_no);
        }

        if($request->has('village') && $request->village != ""){
          $query->where('sanitation.village', 'like', '%'.$request->village.'%');
        }

        if($request->has('app_status') && $request->app_status != ""){
          $query->where('sanitation.app_status', $request->app_status);
        }

        if($request->has('impl_status') && $request->impl_status != ""){
          $query->where('sanitation.imp_status', $request->impl_status);
        }

        if($request->has('imp_status') && $request->imp_status != ""){
          $query->where('sanitation.imp_status', $request->imp_status);
        }

        $query->where('sanitation.proj_id', auth()->user()->proj_id);

      })->orderBy('id', 'DESC');

    if($this->pagination)
    {
      $this->datas = $q->paginate(15);
      $this->approvedData = $q->where('app_status', 'approved')->paginate(15);
      $this->completedData = $q->where('imp_status', 'completed')->paginate(15);
    }else{
      $this->datas = $q->get();
      $this->approvedData = $q->where('app_status', 'approved')->get();
      $this->completedData = $q->where('imp_status', 'completed')->get();
    }
  }

  public function get()
  {
    return $this->datas;
  }

  public function siteApproved()
  {
    return $this->approvedData;
  }

  public function siteCompleted()
  {
    return$this->completedData;
  }
}
