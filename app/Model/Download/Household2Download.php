<?php

namespace App\Model\Download;

use Illuminate\Http\Request;

class Household2Download{
  private $households;
  private $request;

  public function __construct($households)
{
    $this->households = $households;
}

  public function download()
{
    $rows = $this->households;

    if(!count($rows))
    {
        return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }
    \Excel::create('Household Latrine Report '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'id',
          'Region',
          'Project',
          'District',
          'Upazila',
          'Union',
          'CDF_No',
          'village',

          
          'hh_name',
          'father_husband',
          'age',
          'occupation',
          'mobile',
          'nid',
          'economic_status',
          'social_safetynet',
          'male',
          'female',
          'children',
          'disable',
          'ownership_type',
          'latrine_type',
          'latrine_details',
          'total_cost',
          'contribute_amount',
          'paid_amount',
          'due_amount',
          'payment_status',
          'payment_date',

          'latitude	',
          'longitude',
          'app_date',
          'approve_id',
          'app_status',
          'imp_status',
          'created_by',
          'updated_by',
          'created_at',
          'updated_at'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $region   = !empty($row->region) ? $row->region->region_name : "";
          $project  = !empty($row->project) ? $row->project->project : "";
          $district = !empty($row->district) ? $row->district->distname : "";
          $upazila  = !empty($row->upazila) ? $row->upazila->upname : "";
          $union    = !empty($row->union) ? $row->union->unname: "";

          if ($row->com_con_id == null || $row->com_con_id == '') {
            $payment_status = "Not Paid";
            $due_amount = "";
           } else {
              if($row->contribute_amount > $row->paid_amount) {
                  $payment_status = "Due";
                  $due_amount = $row->contribute_amount - $row->paid_amount;
              }else {
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
            $row->id,
            $region,
            $project,
            $district,
            $upazila,
            $union,
            $row->cdfno,
            $row->village,

            $row->hh_name,
            $row->father_husband,
            $row->age,
            $row->occupation,
            $row->mobile,
            $row->nid,
            $row->economic_status,
            $row->social_safetynet,
            $row->male,
            $row->female,
            $row->children,
            $row->disable,
            $row->ownership_type,
            $row->latrine_type,
            $row->latrine_details,
            $row->total_cost,
            $row->contribute_amount,
            $row->paid_amount,
            $due_amount,
            $payment_status,
            $payment_date,
            $row->latitude,
            $row->longitude,
            $row->app_date,
            $row->approve_id,
            $row->app_status,
            $row->imp_status,
            $row->created_by,
            $row->updated_by,
            $row->created_at,
            $row->updated_at
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');;
  }
}
