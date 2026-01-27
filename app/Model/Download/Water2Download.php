<?php

namespace App\Model\Download;

use Illuminate\Http\Request;

class Water2Download
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
          'Region',
          'Project',
          'District',
          'Upazila',
          'Union',
          'TW ID',

          'CDF_no',
          'Village',
          'App_date',
          'Technology_Type',
          'Landowner',
          'Caretaker_male',
          'Caretaker_female',
          'mobile',
          'nid',
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
          'wq_as_lab',
          'wq_fe_lab',
          'wq_mn_lab',
          'wq_cl_lab',
          'x_coord',
          'y_coord',
          'gpschk',
          'depth',
          'platform',
          'approve_id',
          'estimated_cost',
          'contribute_amount',
          'paid_amount',
          'due_amount',
          'payment_status',
          'payment_date',
          'app_status',
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

          $region_name = isset($row->region) ? $row->region->region_name : "";
          $project_name = isset($row->project) ? $row->project->project : "";
          $district_name = isset($row->district) ? $row->district->distname : "";
          $upazila_name = isset($row->upazila) ? $row->upazila->upname : "";
          $union_name = isset($row->union) ? $row->union->unname : "" ;

            if ($row->com_con_id == null || $row->com_con_id == '') {
                $payment_status = "Not Paid";
                $due_amount = "";
            } else {
                if($row->com_con_amount > $row->paid_amount) {
                    $payment_status = "Due";
                    $due_amount = $row->com_con_amount - $row->paid_amount;
                } else {
                    $payment_status = "Paid";
                    $due_amount = "";
                }
            }
            if($row->pay_date == null || $row->pay_date == ''){
                $payment_date = "";
            } else {
                $payment_date = date( "Y-m-d", strtotime($row->pay_date));
            }
            $sheet->row($rowIndex, [
            $region_name,
            $project_name,
            $district_name,
            $upazila_name,
            $union_name,
            $row->id,
            $row->CDF_no,
            $row->Village,
            $row->App_date,
            $row->Technology_Type,
            $row->Landowner,
            $row->Caretaker_male,
            $row->Caretaker_female,
            $row->mobile,
            $row->nid,
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
            $row->wq_as_lab,
            $row->wq_fe_lab,
            $row->wq_mn_lab,
            $row->wq_cl_lab,
            $row->x_coord,
            $row->y_coord,
            $row->gpschk,
            $row->depth,
            $row->platform,
            $row->approve_id,
            $row->estimated_cost,
            $row->com_con_amount,
            $row->paid_amount,
            $due_amount,
            $payment_status,
            $payment_date,
            $row->app_status,
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
