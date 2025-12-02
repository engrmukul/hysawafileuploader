<?php

namespace App\Model\Download\Superadmin\Procurement;

use App\Model\Search\PECSearch;
use Illuminate\Http\Request;

class PECDownload
{
  private $request;
  public function __construct(Request $request)
  {
    $this->request = $request;
  }
  public function download()
  {
    $rows = (new PECSearch($this->request))->get();

    \Excel::create('Procurement-PEC- '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'Project',
          'District',
          'Upazila',
          'Union',

          'Name',
          'Designation in Comittee',
          'Designation in UP',
          'Phone'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->project != "" ? $row->project->project : "",

            ($row->union != "" && $row->union->upazila != "" && $row->union->upazila->district != "") ? $row->union->upazila->district->distname : "",
            ($row->union != "" && $row->union->upazila != "") ? $row->union->upazila->upname : "",
             $row->union != "" ? $row->union->unname : "",

            $row->name ,
            $row->deg ,
            $row->UP_desg ,
            $row->phone
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}