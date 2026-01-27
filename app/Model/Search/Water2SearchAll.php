<?php

namespace App\Model\Search;

use App\Model\Water;
use Illuminate\Http\Request;

class Water2SearchAll
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
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = Water::with('region', 'district', 'upazila', 'union', 'project')->where(function($query) use($request) {

      $request->date_type = "App_date";

      $region_id = $request->region_id;

      $starting_date = $request->starting_date;
      $ending_date = $request->ending_date;

      $created_by = $request->created_by;

      $distid = $request->district_id;
      $upid = $request->upazila_id;
      $unid = $request->union_id;

      $village = $request->village;
      $app_status = $request->app_status;
      $imp_status = $request->imp_status;
      $tech_type = $this->request->tech_type;
      $water_id = $this->request->water_id;
      $approve_id = $this->request->approve_id;
      $payment_status = $this->request->payment_status;
      $pay_starting_date = $this->request->pay_starting_date;
      $pay_ending_date = $this->request->pay_ending_date;

      $Tw_no = $request->Tw_no;
      $CDF_no = $request->CDF_no;

      if(!empty($region_id))
      {
        $query->where('region_id', '=', $region_id);
      }

      if($request->date_type != "")
      {
        if(!empty($starting_date) && !empty($ending_date))
        {
          $query->where($request->date_type, '>=', $starting_date)
                ->where($request->date_type, '<=', $ending_date);

        }elseif(!empty($starting_date))
        {
          $query->where($request->date_type, '=', $starting_date);
        }
      }

        $role = \Auth::user()->roles->first()->name;
        
        if($role == "upazila_admin")

        {
            $query->where('proj_id', auth()->user()->proj_id);
        }
          $query->where('proj_id', auth()->user()->proj_id);
        
      if(!empty($created_by))
      {
        $query->where('created_by', $created_by);
      }

      if(!empty($distid))
      {
        if($distid == "all"){
          $upid = "";
          $unid = "";

          

          if($role == "district_admin")
          {
            $query->where('region_id', auth()->user()->region_id);
          }

        }else{
          $query->where('distid', $distid);
        }
      }

      if(!empty($upid))
      {
        if($upid == "all"){

          $role = \Auth::user()->roles->first()->name;

          if($role == "upazila_admin"){
            $query->where('distid', auth()->user()->distid);
          }else{
            $query->where('distid', $distid);
          }
          $unid = "";
        }else{
          $query->where('upid', $upid);
        }

      }

      if(!empty($unid))
      {
        if($unid == "all"){
          $role = \Auth::user()->roles->first()->name;

          if($role == "upazila_admin"){
            $query->where('upid', auth()->user()->upid);
          }else{
            $query->where('upid', $upid);
          }



        }else{
          $query->where('unid', $unid);
        }
      }

      if(!empty($village))
      {
        $query->where('Village', 'like', "%$village%");
      }

      if(!empty($app_status))
      {
        $query->where('app_status', $app_status);
      }

      if(!empty($tech_type))
      {
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

      if(!empty($approve_id))
      {
        $query->where('approve_id', str_pad($approve_id,3,"0",STR_PAD_LEFT));
      }

      if(!empty($water_id))
      {
        $query->where('id', $water_id);
      }

      if(!empty($CDF_no))
      {
        $query->where('CDF_no', $CDF_no);
      }

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
                $query->whereRaw('com_con_amount > paid_amount');
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
    })
    ->orderBy('unid', 'ASC');

    if($this->pagination){
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
