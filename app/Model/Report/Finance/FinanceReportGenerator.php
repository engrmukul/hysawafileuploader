<?php

namespace App\Model\Report\Finance;

use App\Model\Report\Finance\FinanceDistrictReport;
use App\Model\Report\Finance\FinanceUnionReport;
use App\Model\Report\Finance\FinanceUpazilaReport;
use Illuminate\Http\Request;

class FinanceReportGenerator
{

  private $report;

  private $unionReport;
  private $districtReport;
  private $upazilaReport;
  private $regionReport;

  public function __construct(Request $request)
  {
    if($request->has('union_id') && $request->union_id != "" && $request->union_id != "all"){
      $this->unionReport = new FinanceUnionReport($request);
    }elseif($request->has('upazila_id') && $request->upazila_id != "" && $request->upazila_id != "all"){
      $this->upazilaReport = new FinanceUpazilaReport($request);
    }elseif($request->has('district_id') && $request->district_id != "" && $request->district_id != "all"){
      $this->districtReport = new FinanceDistrictReport($request);
    }else{
      throw new \Exception("Invalid Report Type Detected.", 1);
    }
  }

  public function download()
  {
    return $this->report->download();
  }

  public function generateHeading($type = 'html')
  {
    return $this->report->getHead($type);
  }

  public function getReportBody($type = 'html')
  {
    return $this->report->getBody($type);
  }

}