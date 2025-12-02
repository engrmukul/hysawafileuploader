<?php

namespace App\Model\Download;

use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Http\Request;

class SpComWpV2Download
{
  private $rows;
  private $request;

  public function __construct($rows)
  {
    $this->waters = $rows;
  }

  public function download()
  {
    $rows = $this->waters;

    if(!count($rows))
    {
      return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    \Excel::create(date("d-m-Y").' SafePani Community Waterpoints', function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'Institution ID',
          'Community Name',
          'Upazila',
          'Union',
          'Village',
          'Establishment Year',
          'Longitude',
          'Latitude',
          'Actively Managed'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
            $upname = ucfirst(strtolower(Upazila::find($row['upid'])->upname));
            $unname = ucfirst(strtolower(Union::find($row['unid'])->unname));

            if($row['is_active'] == "1"){
                $is_manged = 'Yes';
            } else {
                $is_manged = 'No';
            }

            $sheet->row($rowIndex, [
            $row['institution_id'],
            $row['sch_name_en'],
            $upname,
            $unname,
            $row['vill'],
            $row['estab_year'],
            $row['lon'],
            $row['lat'],
            $is_manged
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
