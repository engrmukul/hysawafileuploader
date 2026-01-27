<?php

namespace App\Model\Search;

use App\Model\SPSchool;
use Illuminate\Http\Request;

class SpComWpSearch
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

    $q = SPSchool::orderBy('id', 'DESC')

        ->where('sch_type_edu', '=', 'Community Waterpoint')

        //->where('is_active', '1')

        ->where(function($query) use($request) {

      $upid = $request->upazila_id;
      $unid = $request->union_id;
      $hcf_type = $request->hcf_type;

      if(!empty($upid) && $upid != 'all' && $upid != null)
      {
        $query->where('upid', '=', $upid);
      }

      if(!empty($unid) && $unid != 'all' && $unid != null)
      {
        $query->where('unid', '=', $unid);
      }

    });
    //->where('created_by', '=', auth()->user()->id)
    //->groupBy('id');

    if($this->pagination){
      $this->datas = $q->paginate(10000);
    }else{
      $this->datas = $q->get();
    }
  }

  public function get()
  {
    return $this->datas;
  }
}
