<?php

namespace App\Model\Search;

use App\Model\ProcurementEvaluation;
use Illuminate\Http\Request;

class ProcurementEvaluationSearch
{
  private $evaluations;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->evaluations;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;
    $q = ProcurementEvaluation::with('union.upazila.district')
      ->with('project')
      ->where(function($query) use($request) {
        if($request->has('district_id') && $request->district_id != ""){
          $query->where('comp_sheet.distid', $request->district_id);
        }
        if($request->has('upazila_id') && $request->upazila_id != ""){
          $query->where('comp_sheet.upid', $request->upazila_id);
        }

        if($request->has('union_id') && $request->union_id != ""){
          $query->where('comp_sheet.unid', $request->union_id);
        }

        if($request->has('project_id') && $request->project_id != ""){
          $query->where('comp_sheet.proid', $request->project_id);
        }
      });

      if($this->pagination)
      {
        $this->evaluations = $q->paginate(15);
      }else{
        $this->evaluations = $q->get();
      }
  }

  public function get()
  {
    return $this->evaluations;
  }
}
