<?php

namespace App\Model\Download;

use App\Model\District;
use App\User;
use Illuminate\Http\Request;

class SchoolDownload
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

    \Excel::create('SafePani School List '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'District',
          'Upazila',
          'Union',
          'School ID',
          'School Name',
          'School Type',
          'Organization Code',
          'Ward No',
          'Village',
          'Total Students',
          'Male Stud.',
          'Female Stud.',
          'Dis. Stud.',
          'Total Teachers',
          'Male Tchr.',
          'Female Tchr.',
          'Dis. Tchr.',
          'Headmaster Name',
          'Mobile',
          'SMC President',
          'Mobile',
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
            $row->school_name,
            $row->school_type,
            $row->org_code,
            $row->ward,
            $row->vill,
            $row->tot_student,
            $row->boys,
            $row->girls,
            $row->dis_stud,
            $row->tot_teacher,
            $row->male,
            $row->female,
            $row->disable,
            $row->headmaster,
            $row->head_mob,
            $row->smc_president,
            $row->pres_mob,
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
