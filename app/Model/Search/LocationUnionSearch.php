<?php

namespace App\Model\Search;

use App\Model\Union;
use Illuminate\Http\Request;

class LocationUnionSearch
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

    $q = Union::with('project', 'region', 'upazila.district')->where(function($query) use($request) {

      if($request->has('region_id') && !empty($request->region_id))
      {
        $query->where('region_id', '=', $request->region_id);
      }

      if($request->has('project_id') && !empty($request->project_id))
      {
        $query->where('proid', '=', $request->project_id);
      }

      if($request->has('district_id') && !empty($request->district_id))
      {
        $query->where('distid', $request->district_id);
      }

      if($request->has('upazila_id') && !empty($request->upazila_id))
      {
        $query->where('upid', $request->upazila_id);
      }

      if($request->has('unname') && !empty($request->unname))
      {
        $query->where('unname', 'like', '%'.$request->unname.'%');
      }
    })->orderBy('id', 'DESC');

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
