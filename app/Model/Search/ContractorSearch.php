<?php

namespace App\Model\Search;

use App\Model\District;
use Illuminate\Http\Request;

class ContractorSearch
{
  private $contractors;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->contractors;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    \DB::enableQueryLog();

    $q = \DB::table('procurement')

        ->leftJoin('fdistrict', 'procurement.distid', '=', 'fdistrict.id')

        ->where(function($query) use($request) {

            if($request->has('district_id') && $request->district_id != ""){
              $query->where('procurement.distid', $request->district_id);
            }

            if($request->has('category') && $request->category != "category"){
              $query->where('procurement.category', $request->category);
            }

            if($request->has('con_name') && $request->con_name != "con_name"){
              $query->where('procurement.con_name', 'like', '%'.$request->con_name.'%');
            }
        });

    if($this->pagination)
    {
      $this->contractors = $q->paginate(15);
    }else{
      $this->contractors = $q->get();
    }

    //\Log::info(\DB::getQueryLog());

  }

  public function get()
  {
    return $this->contractors;
  }
}
