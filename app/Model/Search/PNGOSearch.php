<?php

namespace App\Model\Search;

use App\Model\District;
use Illuminate\Http\Request;

class PNGOSearch
{
  private $pngos;
  private $request;
  private $pagination;

  public function __construct(Request $request, $pagination = false)
  {
    $this->request = $request;
    $this->pngos;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $q = \DB::table('osm_pngo')->
        select('fdistrict.distname','fupazila.upname','funion.unname','osm_pngo.ngoname','fdistrict.id','fupazila.id','osm_pngo.edname','osm_pngo.edmobile','osm_pngo.edemail','osm_pngo.address', 'osm_pngo.ContactPerson', 'osm_pngo.contactmobile', 'osm_pngo.contactemail','osm_pngo.contractdate', 'osm_pngo.remarks', 'osm_pngo.id')
        ->leftJoin('fdistrict', 'osm_pngo.distid', '=', 'fdistrict.id')
        ->leftJoin('fupazila', 'osm_pngo.upid', '=', 'fupazila.id')
        ->leftJoin('funion', 'osm_pngo.unid', '=', 'funion.id')
        ->where(function($query) use($request) {

          if($request->has('project_id') && $request->project_id != "")
          {
            $query->where('osm_pngo.projid', $request->project_id);
          }

          if($request->has('region_id') && $request->region_id != "" &&
            $request->has('district_id') && $request->district_id == "" &&
            $request->has('upazila_id') && $request->upazila_id == "" &&
            $request->has('union_id') && $request->union_id == ""
            )
          {
            $districts = District::where('region_id', $request->region_id)->get(['id']);
            $query->whereIn('osm_pngo.distid', $districts);

          }else{

            if($request->has('district_id') && $request->district_id != "")
            {
              $query->where('osm_pngo.distid', $request->district_id);
            }

            if($request->has('upazila_id') && $request->upazila_id != "")
            {
              $query->where('osm_pngo.upid', $request->upazila_id);
            }

            if($request->has('union_id') && $request->union_id != "")
            {
              $query->where('osm_pngo.unid', $request->union_id);
            }
          }
        })
        ->orderBy('fdistrict.distname', 'ASC')
        ->orderBy('fupazila.upname', 'ASC')
        ->orderBy('funion.unname', 'ASC');

    if($this->pagination)
    {
      $this->pngos = $q->paginate(15);
    }else{
      $this->pngos = $q->get();
    }
  }

  public function get()
  {
    return $this->pngos;
  }
}
