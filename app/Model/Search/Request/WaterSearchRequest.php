<?php

namespace App\Model\Search\Request;

class WaterSearchRequest
{
  public $starting_date;
  public $ending_date;
  public $created_by;
  public $distid;
  public $upid;
  public $unid;
  public $ward_no;
  public $village;
  public $region_id;
  public $app_status;
  public $tech_type;
  public $imp_status;
  public $Tw_no;
  public $CDF_no;
  public $date_type;
  public $proj_id;

  public function __construct()
  {
    $this->starting_date = "";
    $this->ending_date = "";
    $this->created_by = "";
    $this->distid = "";
    $this->upid = "";
    $this->unid = "";
    $this->ward_no = "";
    $this->village = "";

    $this->region_id = "";
    $this->app_status = "";
    $this->tech_type = "";
    $this->imp_status = "";
    $this->Tw_no = "";
    $this->CDF_no = "";
    $this->date_type = "";
    $this->proj_id = "";
    
  }
}
