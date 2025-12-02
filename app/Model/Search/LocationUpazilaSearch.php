<?php

namespace App\Model\Search;

use App\Model\Upazila;
use Illuminate\Http\Request;

class LocationUpazilaSearch
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

    $q = Upazila::with('district.region')->where(function($query) use($request) {

      if($request->has('region_id') && !empty($request->region_id)){
        
        $query->whereHas('district', function($qqq) use($request) {
          
          $qqq->whereHas('region', function($qq) use($request) {
            
            $qq->where('id', '=', $request->region_id);
          });

        });
        

      }

      if($request->has('district_id') && !empty($request->district_id)){
        $query->where('distid', $request->district_id);
      }

      if($request->has('upname') && !empty($request->upname)){
        $query->where('upname', 'like', '%'.$request->upname.'%');
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
