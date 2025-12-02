<?php

namespace App\Model;

use App\Water;
use Illuminate\Http\Request;

class WaterSummary
{
  private $table;
  private $data;

  public function __construct()
  {
    $this->table = "";
    $this->data = "";
    $this->process();
  }

  public function process()
  {
  //  \DB::enableQueryLog();

    $this->data = \DB::table('tbl_water')
      ->where('unid', \Auth::user()->unid)
      ->where('proj_id', \Auth::user()->proj_id)
     
      ->leftjoin('funion', 'tbl_water.unid', 'funion.id')
      ->leftjoin('fupazila', 'funion.upid', 'fupazila.id')
      ->leftjoin('fdistrict', 'fdistrict.id', 'fupazila.disid')
      ->select(
        'fdistrict.distname',
        'fupazila.upname',
        'funion.unname',
        'tbl_water.unid',
        'tbl_water.App_date',
        'tbl_water.approve_id',
        \DB::Raw('Sum(tbl_water.TW_No) as SumOfTW_No')
      )
      ->groupBy('fdistrict.distname', 'fupazila.upname', 'funion.unname', 'tbl_water.unid', 'tbl_water.App_date', 'tbl_water.approve_id')
      ->get();

    $this->buildTable();
  }


  private function buildTable()
  {
    if(count($this->data))
    {
      $this->table = '<div class="table-responsive"><p>Sorry! No Data Found</p></div>';
    }

    $this->table = $this->getTableHeader().$this->getTableBody().$this->getTableFooter();
  }

  private function getTableHeader()
  {
    return
      '<div class="table-responsive">
        <table class="table table-condensed table-hover table-bordered " >
        <tr>
          <th style="font-size: 10px;"  >Dist</th>
          <th style="font-size: 10px;"  >Up</th>
          <th style="font-size: 10px;"  >Un</th>
          <th style="font-size: 10px;"  >App. date</th>
          <th style="font-size: 10px;"  >App. ID</th>
          <th style="font-size: 10px;"  >Approved</th>
          <th style="font-size: 10px;"  >Pending</th>
          <th style="font-size: 10px;"  >Cancelled</th>
          <th style="font-size: 10px;"  >Rejected</th>
          <th style="font-size: 10px;"  >ITP</th>
          <th style="font-size: 10px;"  >UC</th>
          <th style="font-size: 10px;"  >Completed</th>
          <th style="font-size: 10px;"  >WQ Tested</th>
          <th style="font-size: 10px;"  >Platform Cons.</th>
          <th style="font-size: 10px;"  >Depth Mes.</th>
          <th style="font-size: 10px;"  >GPS Coor.</th>
        </tr>';
  }

  private function getTableBody()
  {
    $str = "";
    foreach($this->data as $item){


      $str .= "<tr>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$item->distname."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\">".$item->upname."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\">".$item->unname."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\">".$item->App_date."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\">".$item->approve_id."</td>";


      $totalApproved = \DB::table('tbl_water')
          ->where('app_status', '=', "Approved")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();
    
      $totalPending = \DB::table('tbl_water')
          ->where('app_status', '=', "Submitted")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $totalCancelled = \DB::table('tbl_water')
          ->where('app_status', '=', "Cancelled")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();


      $totalRejected = \DB::table('tbl_water')
          ->where('app_status', '=', "Rejected")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $totalInTenderingProcess = \DB::table('tbl_water')
          ->where('app_status', '=', "Tendering in process")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $totalUnderConstruction = \DB::table('tbl_water')
          ->where('app_status', '=', "Under Implementation")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $totalCompleted = \DB::table('tbl_water')
          ->where('imp_status', '=', "Completed")
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $totalWQTested = \DB::table('tbl_water')
          ->whereNotNull('wq_Arsenic')
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();


      $totalPlatformConstructed = \DB::table('tbl_water')
          ->where('platform', '=', 'Yes')
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();


      $totalDepthMeasured = \DB::table('tbl_water')
          ->where('depth', '>', 0)
          ->where('unid', $item->unid)
          ->where('proj_id', \Auth::user()->proj_id)
          ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $totalGPSCoordinates = \DB::table('tbl_water')
          ->where('x_coord', '!=', '')
          ->where('unid', $item->unid)
           ->where('proj_id', \Auth::user()->proj_id)
           ->where('region_id', \Auth::user()->region_id)
          ->where('approve_id', $item->approve_id)
          ->where('App_date', $item->App_date)
          ->groupBy('unid')
          ->count();

      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalApproved."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalPending."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalCancelled."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalRejected."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalInTenderingProcess."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalUnderConstruction."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalCompleted."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalWQTested."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalPlatformConstructed."</td>";
      $str .= "<td style=\"font-size: 10px;\" nowrap=\"nowrap\" >".$totalDepthMeasured."</td>";
      $str .= "</tr>";
    }
    return $str;
  }

  private function getTableFooter()
  {
    return '</table></div>';
  }

  public function generateReportTable()
  {
    return $this->table;
  }

}