<?php

namespace App\Model\Search;

use App\Model\SPClinic;
use Illuminate\Http\Request;

class ClinicSearch
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

    $q = SPClinic::orderBy('created_at', 'DESC')

        ->where(function($query) use($request) {

      $distid = $request->district_id;
      $upid = $request->upazila_id;
      $unid = $request->union_id;
      $clinic_name = $request->clinic_name;
      $estab_year = $request->estab_year;
      $starting_date = $request->starting_date;
      $ending_date = $request->ending_date;

      if($starting_date != "" && $ending_date != "")
      {
        $query->whereDate('created_at', '>=', $starting_date)->whereDate('created_at', '<=', $ending_date);
      }

      if(!empty($distid) && $distid != 'all' && $distid != null)
      {
        $query->where('distid', '=', $distid);
      }

      if(!empty($upid) && $upid != 'all' && $upid != null)
      {
        $query->where('upid', '=', $upid);
      }

      if(!empty($unid) && $unid != 'all' && $unid != null)
      {
        $query->where('unid', '=', $unid);
      }

      if(!empty($clinic_name))
      {
        $query->where('clinic_name', 'like', "%$clinic_name%");
      }

      if(!empty($estab_year))
      {
        $query->where('estab_year', '=', $estab_year);
      }

    });
    //->where('created_by', '=', auth()->user()->id)
    //->groupBy('id');

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
