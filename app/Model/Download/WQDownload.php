<?php

namespace App\Model\Download;

use App\User;
use Illuminate\Http\Request;

class WQDownload
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

    \Excel::create('Water Quality HYSAWA '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'Project',
          'District',
          'Upazila',
          'Union',
          'TW ID',
          'Ward_no',
          'CDF_no',
          'Village',
          'App_date',
          'Technology_Type',
          'Landowner',
          'Caretaker_male',
          'Caretaker_female',
          'wq_Arsenic',
          'wq_fe',
          'wq_mn',
          'wq_cl',
          'wq_tc',
          'wq_fc',
          'depth',
          'Lat',
          'Lon',
          'Submitted_By',
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $project_name = isset($row->project) ? $row->project->project : "";
          $district_name = isset($row->district) ? $row->district->distname : "";
          $upazila_name = isset($row->upazila) ? $row->upazila->upname : "";
          $union_name = isset($row->union) ? $row->union->unname : "" ;

          $sheet->row($rowIndex, [
            $project_name,
            $district_name,
            $upazila_name,
            $union_name,
            $row->id,
            $row->Ward_no,
            $row->CDF_no,
            $row->Village,
            $row->App_date,
            $row->Technology_Type,
            $row->Landowner,
            $row->Caretaker_male,
            $row->Caretaker_female,
            $row->wq_Arsenic,
            $row->wq_fe,
            $row->wq_mn,
            $row->wq_cl,
            $row->wq_tc,
            $row->wq_fc,
            $row->depth,
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
