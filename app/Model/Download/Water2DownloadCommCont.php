<?php

namespace App\Model\Download;

use App\Model\Project;
use Illuminate\Http\Request;

class Water2DownloadCommCont
{
  private $waters;
  private $dist_name;
  private $up_name;
  private $un_name;
  private $request;

  public function __construct($waters, $dist_name, $up_name, $un_name)
  {
    $this->waters = $waters;
    $this->dist_name = $dist_name;
    $this->up_name = $up_name;
    $this->un_name = $un_name;
  }

  public function download()
  {
    $rows = $this->waters;
    $dist_name = $this->dist_name;
    $up_name = $this->up_name;
    $un_name = $this->un_name;

    if(!count($rows))
    {
      return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    if($un_name != ""){
          $file_name_title = $un_name;
      } else if($up_name != ""){
          $file_name_title = $up_name;
      } else if($dist_name != "") {
          $file_name_title = $dist_name;
      } else {
          $file_name_title = Project::find(auth()->user()->proj_id)->project;
    }

    \Excel::create($file_name_title.' - Tubewell Contribution '.date("d-m-Y"), function($excel) use($rows, $dist_name, $up_name, $un_name) {
      $excel->sheet('Sheetname', function($sheet) use($rows, $dist_name, $up_name, $un_name) {
        $sheet->setOrientation('landscape');
          $proj_name = Project::find(auth()->user()->proj_id)->project;
          $sl = 1;

          if($up_name == "" || $un_name == ""){
              $sheet->row(1, array(
                      '',
                      'Name of Project: '.$proj_name,
                      'District: '.$dist_name,
                      'Hardware Type: DSP'
                  )
              );

              $sheet->row(2, array(
                      'Sl',
                      'ID No',
                      'Upazila',
                      'Union',
                      'CDF No',
                      'Village',
                      'Landowner',
                      'Total Cost',
                      'Net Contribution',
                      'Due Amount',
                      'Payment Status',
                      'Payment Date',
                      'App ID',
                      'App Date',
                      'App Status'
                  )
              );

              $rowIndex = 3;
              foreach($rows as $row)
              {
                  $upazila_name = isset($row->upazila) ? $row->upazila->upname : "";
                  $union_name = isset($row->union) ? $row->union->unname : "" ;

                  if ($row->com_con_id == null || $row->com_con_id == '') {
                      $payment_status = "Not Paid";
                      $net_due_amount = "";
                  } else {
                      if($row->com_con_amount > $row->paid_amount) {
                          $payment_status = "Due";
                          $net_due = $row->com_con_amount - $row->paid_amount;
                          $net_due_amount = round($net_due-$net_due*1.75/100, 2);
                      } else {
                          $payment_status = "Paid";
                          $net_due_amount = "";
                      }
                  }
                  if($row->pay_date == null || $row->pay_date == ''){
                      $payment_date = "";
                  } else {
                      $payment_date = date( "d/m/Y", strtotime($row->pay_date));
                  }
                  if($row->com_con_amount != null && $row->com_con_amount != "")
                      $net_contribution_amount = round($row->com_con_amount-$row->com_con_amount*1.75/100, 2);
                  else
                      $net_contribution_amount = "";

                  $approve_date=date_create($row->App_date);
                  $approve_date = date_format($approve_date,"d/m/Y");

                  $sheet->row($rowIndex, [
                      $sl++,
                      $row->id,
                      $upazila_name,
                      $union_name,
                      $row->CDF_no,
                      $row->Village,
                      $row->Landowner,
                      $row->estimated_cost,
                      $net_contribution_amount,
                      $net_due_amount,
                      $payment_status,
                      $payment_date,
                      $row->approve_id,
                      $approve_date,
                      $row->app_status
                  ]);
                  $rowIndex++;
              }
          } else {
              $sheet->row(1, array(
                      '',
                      'Name of Project: '.$proj_name,
                      'District: '.$dist_name,
                      'Upazila: '.$up_name,
                      'Union: '.$un_name,
                      'Hardware Type: DSP'
                  )
              );

              $sheet->row(2, array(
                      'Sl',
                      'ID No',
                      'CDF No',
                      'Village',
                      'Landowner',
                      'Total Cost',
                      'Net Contribution',
                      'Net Due',
                      'Payment Status',
                      'Payment Date',
                      'App ID',
                      'App Date',
                      'App Status'
                  )
              );

              $rowIndex = 3;
              foreach($rows as $row)
              {
                  if ($row->com_con_id == null || $row->com_con_id == '') {
                      $payment_status = "Not Paid";
                      $net_due_amount = "";
                  } else {
                      if($row->com_con_amount > $row->paid_amount) {
                          $payment_status = "Due";
                          $net_due = $row->com_con_amount - $row->paid_amount;
                          $net_due_amount = round($net_due-$net_due*1.75/100, 2);
                      } else {
                          $payment_status = "Paid";
                          $net_due_amount = "";
                      }
                  }
                  if($row->pay_date == null || $row->pay_date == ''){
                      $payment_date = "";
                  } else {
                      $payment_date = date( "d/m/Y", strtotime($row->pay_date));
                  }
                  if($row->com_con_amount != null && $row->com_con_amount != "")
                      $net_contribution_amount = round($row->com_con_amount-$row->com_con_amount*1.75/100, 2);
                  else
                      $net_contribution_amount = "";

                  $approve_date=date_create($row->App_date);
                  $approve_date = date_format($approve_date,"d/m/Y");

                  $sheet->row($rowIndex, [
                      $sl++,
                      $row->id,
                      $row->CDF_no,
                      $row->Village,
                      $row->Landowner,
                      $row->estimated_cost,
                      $net_contribution_amount,
                      $net_due_amount,
                      $payment_status,
                      $payment_date,
                      $row->approve_id,
                      $approve_date,
                      $row->app_status
                  ]);
                  $rowIndex++;
              }
          }
        });
    })->download('csv');
  }
}
