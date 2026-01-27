<?php

namespace App\Model\Search\Request;

class SanitationSearchRequest
{
  public $starting_date;
  public $ending_date;
  public $created_by;
  public $village;

  public $unid;
  public $upid;
  public $dist_id;
  public $proj_id;
  public $region_id;
  public $page;
  public $date_type;
  public $app_status;
  public $imp_status;


  public function __construct()
  {
    $this->starting_date = "";
    $this->ending_date = "";
    $this->created_by = "";
    $this->village = "";

    $this->unid = "";
    $this->upid = "";
    $this->dist_id = "";
    $this->proj_id = "";
    $this->region_id = "";
    $this->date_type = "";

    $this->page = "";
    $this->app_status = "";
    $this->imp_status = "";
  }
}
