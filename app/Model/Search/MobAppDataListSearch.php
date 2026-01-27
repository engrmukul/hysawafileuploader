<?php

namespace App\Model\Search;

use App\Model\MobAppDataList;
use Illuminate\Http\Request;
use App\User;

class MobAppDataListSearch
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

    $user_id = "";

    if($request->has('user_id') && $request->user_id != ""){
       $user = User::where('email', $request->user_id)->get();
       if(count($user)){
           $user_id = $user[0]->id;
       }
    }

    $q = MobAppDataList::with('project','region', 'events', 'union.upazila.district',  'user')->where(function($query) use($request, $user_id) {

      $request->date_type = "created_at";

      $starting_date = $request->starting_date;
      $ending_date = $request->ending_date;

      $created_by = $request->created_by;

      $project_id = $request->project_id;
      $region_id = $request->region_id;

      $district_id = $request->district_id;
      $upazila_id = $request->upazila_id;
      $union_id = $request->union_id;
      $cdf_no = $request->cdf_no;

      $type = $request->type;
      $ev_name = $request->ev_name;

      if(!empty($user_id))
      {
        $query->where('user_id', $user_id);
      }


      if($request->date_type != "")
      {
        if(!empty($starting_date) && !empty($ending_date))
        {
          $query->whereDate($request->date_type, '>=', $starting_date)
                ->whereDate($request->date_type, '<=', $ending_date);

        }elseif(!empty($starting_date))
        {
          $query->whereDate($request->date_type, '=', $starting_date);
        }
      }

      if(!empty($type))
      {
        $query->where('type', $type);
      }

     if(!empty($ev_name))
     {
         $query->whereHas('events', function($query2) use($ev_name)
         {
             $query2->where('mobile_app_data_events.ev_name', $ev_name);
         });
     }

      if(!empty($project_id))
      {
        $query->where('proj_id', $project_id);
      }

      if(!empty($region_id))
      {
        $query->where('region_id', $region_id);
      }

      if(!empty($district_id))
      {
        $query->where('distid', $district_id);
      }

      if(!empty($upazila_id))
      {
        $query->where('upid', $upazila_id);
      }

      if(!empty($union_id))
      {
        if($union_id == "all")
        {
          $role= auth()->user()->roles()->first()->name;
          if($role == "upazila_admin")
          {
            $query->where('upid', auth()->user()->upid);
          }

        }else
        {
          $query->where('unid', $union_id);
        }
      }

      if(!empty($cdf_no))
      {
        $query->where('cdf_no', $cdf_no);
      }

    })
    ->orderBy('created_at', 'DESC');

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
