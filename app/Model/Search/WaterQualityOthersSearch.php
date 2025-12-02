<?php

namespace App\Model\Search;

use App\Model\WaterQualityOthers;
use Illuminate\Http\Request;

class WaterQualityOthersSearch
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

    $q = WaterQualityOthers::orderBy('test_date', 'DESC')

        ->where(function($query) use($request) {

      $distid = $request->district_id;
      $upid = $request->upazila_id;
      $unid = $request->union_id;
      $cdf_no = $request->cdf_no;
      $owner = $request->owner;
      $test_id = $request->test_id;

      $starting_date = $request->starting_date;
      $ending_date = $request->ending_date;

      if($starting_date != "" && $ending_date != "")
      {
        $query->whereDate('test_date', '>=', $starting_date)->whereDate('test_date', '<=', $ending_date);
      }

      if(!empty($distid))
      {
        $query->where('dist', '=', $distid);
      }

      if(!empty($upid))
      {
        $query->where('up', '=', $upid);
      }

      if(!empty($unid))
      {
        $query->where('un', '=', $unid);
      }


      if(!empty($cdf_no))
      {
        $query->where('cdf', 'like', "%$cdf_no%");
      }

      if(!empty($owner))
      {
        $query->where('owner', 'like', "%$owner%");
      }

      if(!empty($test_id))
      {
        $query->where('id', '=', $test_id);
      }

    })
    ->where('created_by', '=', auth()->user()->id)
    ->groupBy('id');

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
