<?php

namespace App\Model\Search;

use App\Model\TEC;
use Illuminate\Http\Request;

class TECSearch
{
  private $tecs;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->tecs;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

   // \DB::enableQueryLog();

    $q = TEC::with('union.upazila.district')
              ->with('project')
              ->where(function($query) use($request) {
                if($request->has('district_id') && $request->district_id != ""){
                  $query->where('tec.distid', $request->district_id);
                }
                if($request->has('upazila_id') && $request->upazila_id != ""){
                  $query->where('tec.upid', $request->upazila_id);
                }

                if($request->has('union_id') && $request->union_id != ""){
                  $query->where('tec.unid', $request->union_id);
                }

                if($request->has('project_id') && $request->project_id != ""){
                  $query->where('tec.proid', $request->project_id);
                }
              });

    if($this->pagination)
    {
      $this->tecs = $q->paginate(15);
      // $this->tecs = $q->get();
    }else{
      $this->tecs = $q->get();
    }

    //\Log::info(\DB::getQueryLog());
  }

  public function get()
  {
    return $this->tecs;
  }
}
