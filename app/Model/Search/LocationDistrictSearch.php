<?php

namespace App\Model\Search;

use App\Model\District;
use Illuminate\Http\Request;

class LocationDistrictSearch
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

    $q = District::with('region')->where(function($query) use($request) {

      if($request->has('region_id') && !empty($request->region_id))
      {
        $query->where('region_id', '=', $request->region_id);
      }

      if($request->has('distname') && !empty($request->distname))
      {
        $query->where('distname', 'like', '%'.$request->distname.'%');
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
