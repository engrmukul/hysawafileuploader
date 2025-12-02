<?php

namespace App\Model\Search;

use App\Model\PEC;
use Illuminate\Http\Request;

class PECSearch
{
  private $pecs;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->pecs;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;
    $q = PEC::with('union.upazila.district')
              ->with('project')
              ->where(function($query) use($request) {
                if($request->has('district_id') && $request->district_id != ""){
                  $query->where('pec.distid', $request->district_id);
                }
                if($request->has('upazila_id') && $request->upazila_id != ""){
                  $query->where('pec.upid', $request->upazila_id);
                }

                if($request->has('union_id') && $request->union_id != ""){
                  $query->where('pec.unid', $request->union_id);
                }

                if($request->has('project_id') && $request->project_id != ""){
                  $query->where('pec.proid', $request->project_id);
                }
              });

    if($this->pagination)
    {
      $this->pecs = $q->paginate(15);
    }else{
      $this->pecs = $q->get();
    }
  }

  public function get()
  {
    return $this->pecs;
  }
}
