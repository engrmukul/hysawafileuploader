<?php

namespace App\Model\Search;

use App\FundList;
use Illuminate\Http\Request;
use App\User;

class FundListDataListSearch
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

    $q = FundList::with('union.upazila.district')
    ->with('project')
    ->where(function($query) use($request) {
      if($request->has('district_id') && $request->district_id != ""){
        $query->where('fundlist.distid', $request->district_id);
      }
      if($request->has('upazila_id') && $request->upazila_id != ""){
        $query->where('fundlist.upid', $request->upazila_id);
      }

      if($request->has('union_id') && $request->union_id != ""){
        $query->where('fundlist.unid', $request->union_id);
      }

      if($request->has('project_id') && $request->project_id != ""){
        $query->where('fundlist.proj_id', $request->project_id);
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
