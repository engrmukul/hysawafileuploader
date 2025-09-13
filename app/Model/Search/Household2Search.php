<?php

namespace App\Model\Search;

use App\Model\Household;
use Illuminate\Http\Request;

class Household2Search
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

    $q = Household::with('project')
      ->with('region')
      ->with('union')
      ->with('upazila')
      ->with('district')

      ->where(function($query) use($request) {

        $request->date_type = "App_date";
        $starting_date = $request->input('starting_date');
        $approve_id = $this->request->approve_id;
        $hhl_id = $this->request->hhl_id;
        $payment_status = $this->request->payment_status;
        //$ending_date   = $request->input('ending_date');
        $pay_starting_date = $this->request->pay_starting_date;
        $pay_ending_date = $this->request->pay_ending_date;

        if(!empty($starting_date))
        {
          $query->where('App_date', '=', $starting_date);
        }

        if($request->has('project_id') && $request->project_id != ""){
          $query->where('household.proj_id', $request->project_id);
        }

        if($request->has('region_id') && $request->region_id != ""){
          $query->where('household.region_id', $request->region_id);
        }



        if($request->has('union_id') && $request->union_id != ""){
          $query->where('household.unid', $request->union_id);
        }

        if($request->has('upazila_id') && $request->upazila_id != ""){
          $query->where('household.upid', $request->upazila_id);
        }




        if($request->has('district_id') && $request->district_id != ""){
          $query->where('household.dist_id', $request->district_id);
        }

        if($request->has('cdf_no') && $request->cdf_no != ""){
          $query->where('household.cdfno', $request->cdf_no);
        }

        if($request->has('village') && $request->village != ""){
          $query->where('household.village', 'like', '%'.$request->village.'%');
        }

        if($request->has('app_status') && $request->app_status != ""){
          $query->where('household.app_status', $request->app_status);
        }

        if($request->has('impl_status') && $request->impl_status != ""){
          $query->where('household.imp_status', $request->impl_status);
        }

        if($request->has('imp_status') && $request->imp_status != ""){
          $query->where('household.imp_status', $request->imp_status);
        }

        if(!empty($approve_id))
        {
           $query->where('approve_id', str_pad($approve_id,3,"0",STR_PAD_LEFT));
        }

        if(!empty($hhl_id))
        {
          $query->where('id', $hhl_id);
        }

        $query->where('household.proj_id', auth()->user()->proj_id);

          if(!empty($pay_starting_date) && !empty($pay_ending_date))
          {
              $pay_starting_date = $pay_starting_date." 00:00:00";
              $pay_ending_date = $pay_ending_date." 23:59:59";
              $query->whereBetween('pay_date', array($pay_starting_date, $pay_ending_date));

          }elseif(!empty($pay_starting_date)){
              $pay_starting_date = $pay_starting_date." 00:00:00";
              $query->where('pay_date', '>=', $pay_starting_date);
          }elseif(!empty($pay_ending_date)){
              $pay_ending_date = $pay_ending_date." 23:59:59";
              $query->where('pay_date', '<=', $pay_ending_date);
          }

        if(!empty($payment_status))
          {
              if($payment_status == "Due"){
                  $query->whereRaw('contribute_amount > paid_amount');
              }else if($payment_status == "Paid"){
                  $query->where('com_con_id', '!=', null);
                  $query->where('com_con_id', '!=', '');
                  $query->where('approve_id', '!=', null);
                  $query->where('approve_id', '!=', '');
              } else {
                  $query->where('approve_id', '!=', null);
                  $query->where('approve_id', '!=', '');
                  $query->where('com_con_id', null);
                  $query->orWhere('com_con_id', '');
              }
         }

      })->orderBy('pay_date', 'DESC');

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
