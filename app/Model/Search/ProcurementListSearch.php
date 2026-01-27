<?php

namespace App\Model\Search;

use App\Model\Announce;
use Illuminate\Http\Request;

class ProcurementListSearch
{
  private $announces;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->announces;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;
    $q = Announce::with('union.upazila.district')
              ->with('project')
              ->where(function($query) use($request) {
                if($request->has('district_id') && $request->district_id != ""){
                  $query->where('p_announce.distid', $request->district_id);
                }
                if($request->has('upazila_id') && $request->upazila_id != ""){
                  $query->where('p_announce.upid', $request->upazila_id);
                }

                if($request->has('union_id') && $request->union_id != ""){
                  $query->where('p_announce.unid', $request->union_id);
                }

                if($request->has('project_id') && $request->project_id != ""){
                  $query->where('p_announce.proid', $request->project_id);
                }
              });

    if($this->pagination)
    {
      $this->announces = $q->paginate(15);
    }else{
      $this->announces = $q->get();
    }
  }

  public function get()
  {
    return $this->announces;
  }
}
