<?php

namespace App\Model\Download;

use App\Model\Search\Request\WaterSearchRequest;
use Illuminate\Http\Request;

class WaterDownload
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

    \Excel::create('Water Report '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');


        $sheet->row(1, array(
          'TW ID',
          'Region',
          'Project',
          'District',
          'Upazila',
          'Union',

          'CDF_no',
          'Village',
          'App_date',
          'Technology_Type',
          'Landowner',
          'Caretaker_male',
          'Caretaker_female',
          'HH_benefited',
          'HCHH_benefited',
          'beneficiary_male',
          'beneficiary_female',
          'beneficiary_disable',
          'beneficiary_hardcore',
          'beneficiary_safetynet',
          'wq_Arsenic',
          'wq_fe',
          'wq_mn',
          'wq_cl',
          'wq_ph',
          'wq_pb',
          'wq_zinc',
          'wq_fc',
          'wq_td',
          'wq_turbidity',
          'wq_as_lab',
          'wq_fe_lab',
          'wq_mn_lab',
          'wq_cl_lab',
          'x_coord',
          'y_coord',
          'gpschk',
          'depth',
          'platform',
          'app_status',
          'approve_id',
          'imp_status',
          'remarks',
          'CT_trg',
          'MC_trg',
          'created_by',
          'updated_by',
          'created_at',
          'updated_at'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->id,
            $row->region_name,
            $row->project,
            $row->distname,
            $row->upname,
            $row->unname,
            $row->CDF_no,
            $row->Village,
            $row->App_date,
            $row->Technology_Type,
            $row->Landowner,
            $row->Caretaker_male,
            $row->Caretaker_female,
            $row->HH_benefited,
            $row->HCHH_benefited,
            $row->beneficiary_male,
            $row->beneficiary_female,
            $row->beneficiary_disable,
            $row->beneficiary_hardcore,
            $row->beneficiary_safetynet,
            $row->wq_Arsenic,
            $row->wq_fe,
            $row->wq_mn,
            $row->wq_cl,
            $row->wq_ph,
            $row->wq_pb,
            $row->wq_zinc,
            $row->wq_fc,
            $row->wq_td,
            $row->wq_turbidity,
            $row->wq_as_lab,
            $row->wq_fe_lab,
            $row->wq_mn_lab,
            $row->wq_cl_lab,
            $row->x_coord,
            $row->y_coord,
            $row->gpschk,
            $row->depth,
            $row->platform,
            $row->app_status,
            $row->approve_id,
            $row->imp_status,
            $row->remarks,
            $row->CT_trg,
            $row->MC_trg,
            $row->created_by,
            $row->updated_by,
            $row->created_at,
            $row->updated_at
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
