<?php

namespace App\Model\Download;

use App\Model\District;
use App\User;
use Illuminate\Http\Request;

class ClinicDownload
{
  private $waters;
  private $request;

  public function __construct($waters)
  {
    $this->waters = $waters;
  }

  public function download()
  {
    $rows = $this->waters;

    if(!count($rows))
    {
      return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    \Excel::create('SafePani Clinic List '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'District',
          'Upazila',
          'Union',
          'Clinic ID',
          'Clinic Name',
          'Organization Code',
          'Ward No',
          'Village',
          'Total Staffs',
          'Male Staff',
          'Female Staff',
          'Disable Staff',
          'Avg. Visitors',
          'Male Visitors',
          'Female Visitors',
          'Child Visitors',
          'Establish. Yr.',
          'Remark',
          'Created_At',
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $upazila_name = isset($row->upazila) ? $row->upazila->upname : "";
          $union_name = isset($row->union) ? $row->union->unname : "" ;

          $sheet->row($rowIndex, [
            District::find($row->distid)->distname,
            $upazila_name,
            $union_name,
            $row->id,
            $row->clinic_name,
            $row->org_code,
            $row->ward,
            $row->vill,
            $row->tot_staff,
            $row->male_staff,
            $row->female_staff,
            $row->avg_visitor,
            $row->male_visitor,
            $row->female_visitor,
            $row->child_visitor,
            $row->estab_year,
            $row->remark,
            date_format($row->created_at,"Y/m/d H:i:s")
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
