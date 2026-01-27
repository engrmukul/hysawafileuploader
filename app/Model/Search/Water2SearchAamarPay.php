<?php

namespace App\Model\Search;

use App\Model\AamarPayReceived;
use Illuminate\Http\Request;

class Water2SearchAamarPay
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

    $q = AamarPayReceived::with('district', 'upazila', 'union')->where(function($query) use($request) {

      $distid = $request->district_id;
      $upid = $request->upazila_id;
      $unid = $request->union_id;
      $approve_id = (int)($this->request->approve_id);
      $pay_starting_date = $this->request->pay_starting_date;
      $pay_ending_date = $this->request->pay_ending_date;


      $role = \Auth::user()->roles->first()->name;

      if(!empty($distid))
      {
        if($distid == "all"){
          $upid = "";
          $unid = "";

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

        if(!empty($approve_id))
        {
          $query->where('app_id', $approve_id);
        }

        if(!empty($pay_starting_date) && !empty($pay_ending_date))
        {
            $query->whereBetween('pay_date', array($pay_starting_date, $pay_ending_date));
        }elseif(!empty($pay_starting_date)){
            $query->where('pay_date', '>=', $pay_starting_date);
        }elseif(!empty($pay_ending_date)){
            $query->where('pay_date', '<=', $pay_ending_date);
        }
    });

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
