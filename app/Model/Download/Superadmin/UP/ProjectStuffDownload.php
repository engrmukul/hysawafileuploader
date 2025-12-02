<?php

namespace App\Model\Download\Superadmin\UP;

use Illuminate\Http\Request;

class ProjectStuffDownload
{
  public function download()
  {
    $rows = \DB::table('union_staff')
          ->leftJoin('project',   'union_staff.proid',  '=', 'project.id')
          ->leftJoin('fdistrict', 'union_staff.distid', '=', 'fdistrict.id')
          ->leftJoin('fupazila',  'union_staff.upid',   '=', 'fupazila.id')
          ->leftJoin('funion',    'union_staff.unid',   '=', 'funion.id')
          ->get();

    \Excel::create('project-stuffs-'.time(), function($excel) use( $rows) {
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