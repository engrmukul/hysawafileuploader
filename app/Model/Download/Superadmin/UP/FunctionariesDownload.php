<?php

namespace App\Model\Download\Superadmin\UP;

use App\Model\District;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class FunctionariesDownload
{
  public function download(Request $request)
  {

     //$rows = "";
     // $unionName = "";
     // $upazilaName = "";
     // $districtName = "";

     // if($request->has('union_id') && $request->union_id != "")
     //  {
     //    $rows = \DB::select(\DB::Raw("select * from project_staff where unid = $request->union_id"));

     //    $union  = Union::find($request->union_id);
     //    $unions = Union::where('upid', $union->upid)->get();
     //    $unionName = $union->unname;

     //    $upazila  = Upazila::find($request->upazila_id);
     //    $upazilas = Upazila::where('disid', $upazila->disid)->get();
     //    $upazilaName = $upazila->upname;

     //    $district = District::find($request->district_id);
     //    $districtName = $district->distname;

     //  }elseif($request->has('upazila_id') && $request->upazila_id != "")
     //  {
     //    $rows = \DB::select(\DB::Raw("select * from union_staff where upid = $request->upazila_id "));

     //    $upazila  = Upazila::find($request->upazila_id);
     //    $upazilas = Upazila::where('disid', $upazila->disid)->get();
     //    $upazilaName = $upazila->upname;

     //    $district = District::find($request->district_id);
     //    $districtName = $district->distname;

     //  }elseif($request->has('district_id') && $request->district_id != "")
     //  {
     //    $rows = \DB::select(\DB::Raw("select * from union_staff where distid = $request->district_id"));
     //    $district = District::find($request->district_id);
     //    $districtName = $district->distname;
     //  }elseif($request->has('project_id') && $request->project_id != "")
     //  {
     //    $rows = \DB::select(\DB::Raw("select * from union_staff where proid = $request->project_id "));
     //  }else{

     //    //select(\DB::Raw("select * from  where proid = $request->project_id "));
     //  }

     $rows = \DB::table('union_staff')
          ->leftJoin('project',   'union_staff.proid',  '=', 'project.id')
          ->leftJoin('fdistrict', 'union_staff.distid', '=', 'fdistrict.id')
          ->leftJoin('fupazila',  'union_staff.upid',   '=', 'fupazila.id')
          ->leftJoin('funion',    'union_staff.unid',   '=', 'funion.id')
          ->get();


    \Excel::create('UP-Functionaries-'.time(), function($excel) use( $rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');

        $header = [];

        $header[] = "District";
        $header[] = "Upazila";
        $header[] = "Union";
        $header[] = "Name";
        $header[] = "Designation";
        // $header[] = "Working Word";
        $header[] = "Phone";
        $header[] = "E-mail";

        $sheet->row(1, $header);

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $dataRow = [];

          $dataRow[] = $row->distname;
          $dataRow[] = $row->upname;
          $dataRow[] = $row->unname;
          $dataRow[] = $row->name;
          $dataRow[] = $row->des;
          // $dataRow[] = $row->word;
          $dataRow[] = $row->phone;
          $dataRow[] = $row->email;

          $sheet->row($rowIndex, $dataRow);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}