<?php

namespace App\Model\Download;

use App\Model\District;
use App\User;
use Illuminate\Http\Request;

class WQOthersDownload
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

    \Excel::create('Water Quality OTHERS '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'District',
          'Upazila',
          'Union',
          'Test ID',
          'Ward_no',
          'CDF_no',
          'Village',
          'Technology_Type',
          'Landowner',
          'wq_Arsenic',
          'wq_fe',
          'wq_mn',
          'wq_cl',
          'wq_tc',
          'wq_fc',
          'Lat',
          'Lon',
          'Submitted_By',
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $upazila_name = isset($row->upazila) ? $row->upazila->upname : "";
          $union_name = isset($row->union) ? $row->union->unname : "" ;

          $sheet->row($rowIndex, [
            District::find($row->dist)->distname,
            $upazila_name,
            $union_name,
            $row->id,
            $row->ward,
            $row->cdf,
            $row->vill,
            $row->tech_type,
            $row->owner,
            $row->wq_as,
            $row->wq_fe,
            $row->wq_mn,
            $row->wq_cl,
            $row->wq_tc,
            $row->wq_fc,
            $row->y_coord,
            $row->x_coord,
            User::find($row->created_by)->name
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
