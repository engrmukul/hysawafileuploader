<?php

namespace App\Model\Search\Request;

class HandwashSitesSearchRequest
{
  public $station_id;
  public $starting_date;
  public $ending_date;
  public $created_by;
  public $distid;
  public $upid;
  public $unid;
  public $ward_no;
  public $region_id;
  public $app_status;
  public $imp_status;
  public $handwash_site_no;
  public $date_type;
  public $proj_id;

  public function __construct()
  {
    $this->station_id;
    $this->starting_date = "";
    $this->ending_date = "";
    $this->created_by = "";
    $this->distid = "";
    $this->upid = "";
    $this->unid = "";
    $this->ward_no = "";
    $this->region_id = "";
    $this->app_status = "";
    $this->imp_status = "";
    $this->handwash_site_no = "";
    $this->date_type = "";
    $this->proj_id = "";
    
  }
}
