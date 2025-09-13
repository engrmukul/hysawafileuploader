<?php

namespace App\Model\Search;

use Illuminate\Http\Request;

class TrainingSearch
{
  private $trainings;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->trainings;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = \DB::table('tbl_training')->where(function($query) use($request) {

          if($request->has('Agency') && $request->Agency != "")
          {
            $query->where('Agency', $request->Agency);
          }

          if($request->has('TrgStatus') && $request->TrgStatus != "")
          {
            $query->where('TrgStatus', $request->TrgStatus);
          }

          if($request->has('TrgFrom') && $request->TrgFrom != "")
          {
            $query->where('TrgFrom', '>=', $request->TrgFrom);
          }

          if($request->has('TrgTo') && $request->TrgTo != "")
          {
            $query->where('TrgTo', '<=', $request->TrgTo);
          }

          // if($request->has('Organizedby') && $request->Organizedby != "")
          // {
          //   $query->where('Organizedby', $request->Organizedby);
          // }

          if($request->has('region_id') && $request->region_id != "")
          {
            $query->where('region_id', $request->region_id);
          }

          if($request->has('TrgTitle') && $request->TrgTitle != "")
          {
            $query->where('TrgTitle', 'like', '%'.$request->TrgTitle.'%');
          }
        });

    if($this->pagination)
    {
      $this->trainings = $q->paginate(15);
    }else{
      $this->trainings = $q->get();
    }
  }

  public function get()
  {
    return $this->trainings;
  }
}
