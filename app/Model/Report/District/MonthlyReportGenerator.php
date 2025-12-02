<?php

namespace App\Model\Report\District;

use App\Model\District;
use App\Model\Region;
use App\Model\Union;
use App\Model\Upazila;
use App\ReportData;
use DB;
use Illuminate\Http\Request;
use Log;

class MonthlyReportGenerator
{
  private $reportData;
  private $row_lastdata;
  private $row_totaldata;
  private $row_getbaseline;
  private $row_row_gettargets;
  private $row_gettargets;
  private $report_type;
  private $request;
  private $view_path;

  private $role;

  private $REPORTDATAQUERYSELECT = "SELECT
      Sum(rep_data.cdf_no) AS cdf_no,
      Sum(rep_data.sa_completed) AS sa_completed,
      Sum(rep_data.ws_completed) AS ws_completed,
      Sum(rep_data.cdf_pop) AS cdf_pop,
      Sum(rep_data.cdf_male) AS cdf_male,
      Sum(rep_data.cdf_female) AS cdf_female,
      Sum(rep_data.cdf_pop_hc) AS cdf_pop_hc,
      Sum(rep_data.cdf_hh) AS cdf_hh,
      Sum(rep_data.cdf_hh_hc) AS cdf_hh_hc,
      Sum(rep_data.cdf_pop_disb) AS cdf_pop_disb,
      Sum(rep_data.cdf_pop_safety) AS cdf_pop_safety,
      Sum(rep_data.CHY_gb_new) AS CHY_gb_new,
      Sum(rep_data.CHY_gb_rep) AS CHY_gb_rep,
      Sum(rep_data.HHS_new) AS HHS_new,
      Sum(rep_data.HHS_rep) AS HHS_rep,
      Sum(rep_data.HHS_rep_hc) AS HHS_rep_hc,
      Sum(rep_data.scl_hp_ses) AS scl_hp_ses,
      Sum(rep_data.scl_hp_boys) AS scl_hp_boys,
      Sum(rep_data.scl_hp_girls) AS scl_hp_girls,
      Sum(rep_data.up_cont) AS up_cont,
      Sum(rep_data.up_board) AS up_board,
      Sum(rep_data.CT_trg) AS CT_trg,
      Sum(rep_data.Pdb) AS Pdb,
      Sum(rep_data.improved_stoves) AS improved_stoves,
      Sum(rep_data.solar_power) AS solar_power,
      Sum(rep_data.tree) AS tree,
      Sum(rep_data.up_board_updt) AS up_board_updt,
      Sum(rep_data.up_ward) AS up_ward,
      Sum(rep_data.up_ward_male) AS up_ward_male,
      Sum(rep_data.up_ward_female) AS up_ward_female,
      Sum(rep_data.up_budget) AS up_budget,
      Sum(rep_data.up_budget_male) AS up_budget_male,
      Sum(rep_data.up_budget_female) AS up_budget_female,
      Sum(rep_data.CHY_hw_ses) AS CHY_hw_ses,
      Sum(rep_data.CHY_hw_male) AS CHY_hw_male,
      Sum(rep_data.CHY_hw_female) AS CHY_hw_female,
      Sum(rep_data.CHY_mn_ses) AS CHY_mn_ses,
      Sum(rep_data.CHY_mn_female) AS CHY_mn_female,
      Sum(rep_data.CHY_sa_ses) AS CHY_sa_ses,
      Sum(rep_data.CHY_sa_male) AS CHY_sa_male,
      Sum(rep_data.CHY_sa_female) AS CHY_sa_female,
      Sum(rep_data.CHY_fh_ses) AS CHY_fh_ses,
      Sum(rep_data.CHY_fh_male) AS CHY_fh_male,
      Sum(rep_data.CHY_fh_female) AS CHY_fh_female,
      Sum(rep_data.CHY_drama) AS CHY_drama,
      Sum(rep_data.CHY_drama_pop) AS CHY_drama_pop,
      Sum(rep_data.CHY_vdo) AS CHY_vdo,
      Sum(rep_data.CHY_vdo_pop) AS CHY_vdo_pop,
      Sum(rep_data.scl_vdo) AS scl_vdo,
      Sum(rep_data.scl_vdo_pop) AS scl_vdo_pop,
      SUM(rep_data.CHY_sa_hh) AS CHY_sa_hh,
      SUM(rep_data.CHY_tot_ben) AS CHY_tot_ben,
      SUM(rep_data.scl_tot_ben) AS scl_tot_ben,
      Sum(rep_data.gas_burner) AS gas_burner,
      Sum(rep_data.CHY_day_obs) AS CHY_day_obs,
      Sum(rep_data.CHY_day_pop) AS CHY_day_pop,
      Sum(rep_data.scl_day_obs) AS scl_day_obs,
      Sum(rep_data.scl_day_pop) AS scl_day_pop,
      Sum(rep_data.dsp_completed) AS dsp_completed,
      Sum(rep_data.pws_completed) AS pws_completed,
      Sum(rep_data.pws_rep) AS pws_rep,
      Sum(rep_data.pws_rep_hc) AS pws_rep_hc,
      Sum(rep_data.tools) AS tools,
      Sum(rep_data.infras_sess) AS infras_sess";

  public function __construct(Request $request, $view_path = 'core.district.monthly-report.water.')
  {
    $this->reportData = "";
    $this->row_lastdata = "";
    $this->row_totaldata = "";
    $this->row_getbaseline = "";
    $this->row_row_gettargets = "";
    $this->row_gettargets = "";
    $this->report_type = $request->input('report_type');
    $this->request = $request;

    $this->view_path = $view_path;

    $this->role = \Auth::user()->roles->first()->name;

    $this->process($request);
    
  }

  public function getReport($request)
  {
    $ReportData = $this->reportData;
    $row_lastdata = $this->row_lastdata;
    $row_totaldata = $this->row_totaldata;
    $row_getbaseline = $this->row_getbaseline;
    $row_gettargets = $this->row_gettargets;

    $reportHeader = $this->reportHeader();

    if($this->report_type == "print")
    {
      return view($this->view_path.'print',
        compact('ReportData','row_lastdata','row_totaldata','row_getbaseline','row_gettargets', 'reportHeader', 'request'));


    }else
    {
      
        $districts = District::where('region_id', auth()->user()->region_id)->get();

  

        if($this->role == "superadministrator")
        {
          $districts = District::all();
        }

        return view($this->view_path.'show',
          compact('ReportData','row_lastdata','row_totaldata','row_getbaseline','row_gettargets', 'districts', 'reportHeader', 'request')
        );
    }


  }

  private function process(Request $request)
  {
    Log::info($request->input('region_id'));
    if($request->has('union_id') && $request->input('union_id') == "all" && $request->rep_data_id == "all"){
      $this->unionAllReportAll($request);
    }elseif($request->has('union_id') && $request->input('union_id') == "all" && $request->rep_data_id != ""){
      $this->unionAllReportOne($request);
    }elseif($request->has('union_id') && $request->input('union_id') != "" && $request->rep_data_id == "all"){
      $this->unionOneReportAll($request);
    }elseif($request->has('union_id') && $request->input('union_id') != "" && $request->rep_data_id != ""){
       $this->unionOneReportOne($request);
    }

    elseif($request->has('upazila_id') && $request->input('upazila_id') == "all" && $request->rep_data_id == "all"){
      $this->upazilaAllReportAll($request);
    }elseif($request->has('upazila_id') && $request->input('upazila_id') == "all" && $request->rep_data_id != ""){
      $this->upazilaAllReportOne($request);
    }elseif($request->has('upazila_id') && $request->input('upazila_id') != "" && $request->rep_data_id == "all"){
      $this->upazilaOneReportAll($request);
    }elseif($request->has('upazila_id') && $request->input('upazila_id') != "" && $request->rep_data_id != ""){
      $this->upazilaOneReportOne($request);
    }

    elseif($request->has('district_id') && $request->input('district_id') == "all" && $request->rep_data_id == "all"){
      $this->districtAllReportAll($request);
    }elseif($request->has('district_id') && $request->input('district_id') == "all" && $request->rep_data_id != ""){
      $this->districtAllReportOne($request);
    }elseif($request->has('district_id') && $request->input('district_id') != "" && $request->rep_data_id == "all"){
      $this->districtOneReportAll($request);
    }elseif($request->has('district_id') && $request->input('district_id') != "" && $request->rep_data_id != ""){
      $this->districtOneReportOne($request);
    }else{
      $this->districtOneReportAll($request);
    }

    return $this;
  }

  private function unionAllReportAll(Request $request)
  {
    $upazila = "";
    $id = "";
    $type = "upid";

    $role = \Auth::user()->roles->first()->name;

    if($role == "upazila_admin"){


      $id = auth()->user()->upid;
      $type = "upid";


    }else{
      $upazila = Upazila::find($request->input('upazila_id'));
      $id = $upazila->id;
      $type = "upid";
    }



    $last_report_id = $this->_getLastReportId($type, $id);

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
    $this->reportData      = $this->_getEmptyRepDataInstance();
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function unionAllReportOne(Request $request)
  {
    $last_report_id = $request->input('rep_data_id');

    $upazila = Upazila::find($request->input('upazila_id'));
    $id = $upazila->id;
    $type = "upid";

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
    $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function unionOneReportAll(Request $request)
  {
    $id = $request->input('union_id');
    $type = "unid";
    $last_report_id = $this->_getLastReportId($type, $id);

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
    $this->reportData      = $this->_getEmptyRepDataInstance();
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function unionOneReportOne(Request $request)
  {

    $last_report_id = $request->input('rep_data_id');
    $id = $request->input('union_id');
    $type = "unid";

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
    $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }


  private function upazilaAllReportAll(Request $request)
  {
    $district = District::find($request->input('district_id'))->first();
    $id = $district->id;
    $type = "distid";

    $last_report_id = $this->_getLastReportId($type, $id);

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
    $this->reportData      = $this->_getEmptyRepDataInstance();
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function upazilaAllReportOne(Request $request)
  {
    $last_report_id = $request->input('rep_data_id');

    $district = District::find($request->input('district_id'))->first();
    $id = $district->id;
    $type = "distid";

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
    $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function upazilaOneReportAll(Request $request)
  {
    $id = $request->input('upazila_id');
    $type = "upid";
    $last_report_id = $this->_getLastReportId($type, $id);
    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
    $this->reportData      = $this->_getEmptyRepDataInstance();
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function upazilaOneReportOne(Request $request)
  {
    $last_report_id = $request->input('rep_data_id');
    $id = $request->input('upazila_id');
    $type = "upid";
    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
    $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function districtAllReportAll(Request $request)
  {

    if($this->role == "superadministrator"){
      $last_report_id = $this->_getLastReportId();
      $this->row_getbaseline = $this->_getBaseLineData();
      $this->row_gettargets  = $this->_getTargetData();
      $this->row_lastdata    = $this->_getEmptyRepDataInstance();
      $this->reportData      = $this->_getEmptyRepDataInstance();
      $this->row_totaldata = $this->_getTotalData($last_report_id);
    }else{
      $id = auth()->user()->region_id;
      $type = "region_id";
      $last_report_id = $this->_getLastReportId($type, $id);

      $this->row_getbaseline = $this->_getBaseLineData($id, $type);
      $this->row_gettargets  = $this->_getTargetData($id, $type);
      $this->row_lastdata    = $this->_getEmptyRepDataInstance();
      $this->reportData      = $this->_getEmptyRepDataInstance();
      $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
    }
  }

  private function districtAllReportOne(Request $request)
  {
    if($this->role == "superadministrator"){
      $id = auth()->user()->region_id;
      $last_report_id = $request->input('rep_data_id');
      $this->row_getbaseline = $this->_getBaseLineData();
      $this->row_gettargets  = $this->_getTargetData();
      $this->row_lastdata    = $this->_getLastData($last_report_id);
      $this->reportData      = $this->_getThisMonthData($last_report_id);
      $this->row_totaldata   = $this->_getTotalData($last_report_id);
    }else{
      $id = auth()->user()->region_id;
      $type = "region_id";
      $last_report_id = $request->input('rep_data_id');
      $this->row_getbaseline = $this->_getBaseLineData($id, $type);
      $this->row_gettargets  = $this->_getTargetData($id, $type);
      $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
      $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
      $this->row_totaldata   = $this->_getTotalData($last_report_id, $id, $type);
    }
  }

  private function districtOneReportAll(Request $request)
  {
    $id = $request->input('district_id');
    $type = "distid";
    $last_report_id = $this->_getLastReportId($type, $id);

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
    $this->reportData      = $this->_getEmptyRepDataInstance();
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function districtOneReportOne(Request $request)
  {
    $last_report_id = $request->input('rep_data_id');

    $id = $request->input('district_id');
    $type = "distid";

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
    $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function _getEmptyRepDataInstance()
  {
    $data = DB::select(DB::raw($this->REPORTDATAQUERYSELECT. " from rep_data where rep_id != 1000 and rep_id != 999 and proj_id =".auth()->user()->proj_id.""));
    $response = [];

    foreach($data[0] as $key => $value)
    {
      $response[$key] = null;
    }

    // dd($response);
    return $response;
  }

  private function _getBaseLineData($id = null, $type = "unid")
  {
    // dd(auth()->user()->proj_id);
    $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 999 and proj_id = ".auth()->user()->proj_id."";
 //  dd($this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 999 and proj_id = ".auth()->user()->proj_id."");
    if($id != null){
      $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 999 and $type=$id and proj_id =".auth()->user()->proj_id."";
    }

    $row_getbaseline = DB::select(DB::raw($query));
    $row_getbaseline = json_decode(json_encode((array) $row_getbaseline), true);
    return @$row_getbaseline[0];
  }

  private function _getTargetData($id = null, $type = "unid")
  {
    $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 1000 and proj_id =".auth()->user()->proj_id."";
    if($id != null){
      $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 1000  and $type=$id and proj_id =".auth()->user()->proj_id."" ;
    }

    $row_gettargets=DB::select( DB::raw($query));
    $row_gettargets = json_decode(json_encode((array) $row_gettargets), true);
    return @$row_gettargets[0];
  }

  private function _getLastData($last_report_id, $id = null, $type = "unid")
  {
    $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id < $last_report_id and proj_id =".auth()->user()->proj_id."";
    if($id != null){
      $query = $this->REPORTDATAQUERYSELECT. " FROM rep_data WHERE rep_id < $last_report_id  AND $type=$id and proj_id =".auth()->user()->proj_id."" ;
    }

    $row_lastdata = DB::select(DB::raw($query));
    $row_lastdata = json_decode(json_encode( (array) $row_lastdata), true);
    return $row_lastdata[0];
  }

    private function _getLastCDFBen($last_report_id, $id = null, $type = "unid")
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id < $last_report_id and proj_id =".auth()->user()->proj_id."";
        if($id != null){
            $query = $this->REPORTDATAQUERYSELECT. " FROM rep_data WHERE rep_id < $last_report_id  AND $type=$id and proj_id =".auth()->user()->proj_id."" ;
        }

        $row_lastdata = DB::select(DB::raw($query));
        $row_lastdata = json_decode(json_encode( (array) $row_lastdata), true);
        return $row_lastdata[0];
    }

    private function _getLastSCLBen($last_report_id, $id = null, $type = "unid")
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id < $last_report_id and proj_id =".auth()->user()->proj_id."";
        if($id != null){
            $query = $this->REPORTDATAQUERYSELECT. " FROM rep_data WHERE rep_id < $last_report_id  AND $type=$id and proj_id =".auth()->user()->proj_id."" ;
        }

        $row_lastdata = DB::select(DB::raw($query));
        $row_lastdata = json_decode(json_encode( (array) $row_lastdata), true);
        return $row_lastdata[0];
    }

  private function _getTotalData($last_report_id, $id = null, $type = "unid")
  {
    $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id <= $last_report_id and proj_id =".auth()->user()->proj_id." ";
    if($id != null){
      $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id <= $last_report_id and proj_id =".auth()->user()->proj_id."" ;
    
    
    }

    $row_totaldata =DB::select(DB::raw($query));
    $row_totaldata = json_decode(json_encode((array) $row_totaldata), true);
    return $row_totaldata[0];
  }

  private function _getThisMonthData($last_report_id, $id = null, $type = "unid")
  {
    $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id and proj_id =".auth()->user()->proj_id."";
    if($id != null){
      $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = $last_report_id AND $type=$id and proj_id =".auth()->user()->proj_id."";
    }

    $ReportData = \DB::select(\DB::raw($query));
    if(count($ReportData))
    {
      return (array)$ReportData[0];
    }
  }

    private function _getThisMonthCDFBen($last_report_id, $id = null, $type = "unid")
    {
        $raw_query = "SELECT `ev_cdf`, SUM(best), sessions FROM (SELECT COUNT(*) as sessions, `ev_cdf`, MAX(`ev_male`+`ev_female`) as best FROM `mobile_app_data_events` GROUP BY `ev_cdf`) as bests GROUP BY `ev_cdf`";
        $raw_query = "SELECT `ev_cdf`, SUM(best), sessions, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, MAX(`ev_male`+`ev_female`) as best, `cdf_nos`.`union_id` as unid FROM `mobile_app_data_events` LEFT JOIN `cdf_nos` ON `mobile_app_data_events`.`ev_cdf` = `cdf_nos`.`cdf_no` GROUP BY `ev_cdf`) as bests GROUP BY `ev_cdf`";
        $raw_query = "SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` GROUP BY `ev_cdf`) as bests WHERE upid = 124 GROUP BY upid";

        $raw_query = "SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `rep_id` = '123' AND `mobile_app_data_events`.`ev_name` = 'Latrine Maintenance/Waste Management/Drainage' GROUP BY unid;

";


        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id and proj_id =".auth()->user()->proj_id."";
        if($id != null){
            $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = $last_report_id AND $type=$id and proj_id =".auth()->user()->proj_id."";
        }

        $ReportData = \DB::select(\DB::raw($query));
        if(count($ReportData))
        {
            return (array)$ReportData[0];
        }
    }

    private function _getThisMonthSCLBen($last_report_id, $id = null, $type = "unid")
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id and proj_id =".auth()->user()->proj_id."";
        if($id != null){
            $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = $last_report_id AND $type=$id and proj_id =".auth()->user()->proj_id."";
        }

        $ReportData = \DB::select(\DB::raw($query));
        if(count($ReportData))
        {
            return (array)$ReportData[0];
        }
    }

  private function _getLastReportId($type = null, $id = null)
  {
    if($id == null){

      $data = \DB::table('rep_data')
            ->where('rep_id', '!=', 999)
            ->where('rep_id', '!=', 1000)
            ->orderBy('rep_id', 'DESC')
            ->get();

      if(count($data))
      {
        return $data->first()->rep_id;
      }

      throw new \Exception("Data Not Found.", 1);
    }

    $data = \DB::table('rep_data')
      ->where($type, $id)
      ->where('rep_id', '!=', 999)
      ->where('rep_id', '!=', 1000)
      ->orderBy('rep_id', 'DESC')
      ->get();

    if(count($data))
    {
      return $data->first()->rep_id;
    }

    throw new \Exception("Data Not Found.", 1);

  }

    public static function getEventsLastData($events_name, $rid, $unid, $upid, $distid, $proj_id, $gender){

      if($unid != null){
          if($gender == 'male'){
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid'"));
          } else if($gender == 'female'){
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid'"));
          } else {
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid'"));
          }
      } elseif ($upid != null){
          if($gender == 'male'){
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid'"));
          } else if($gender == 'female'){
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid'"));
          } else {
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid'"));
          }
      } else {
          if($gender == 'male'){
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid'"));
          } else if($gender == 'female'){
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid'"));
          } else {
              $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid'"));
          }
      }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsReportData($events_name, $rid, $unid, $upid, $distid, $proj_id, $gender){

        if($unid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid'"));
            }
        } elseif ($upid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid'"));
            }
        } else {
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid'"));
            }
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsReportDataSchool($rid, $unid, $upid, $distid, $proj_id, $gender){

        if($unid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            }
        } elseif ($upid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            }
        } else {
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            }
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;
    }

    public static function getSingleEventReportDataLoc($events_name, $events_loc, $rid, $unid, $upid, $distid, $proj_id, $gender){

        if($unid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            }
        } elseif ($upid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            }
        } else {
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            }
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsLastDataSchool($rid, $unid, $upid, $distid, $proj_id, $gender){

        if($unid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            }
        } elseif ($upid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            }
        } else {
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
            }
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;
    }

    public static function getSingleEventLastDataLoc($events_name, $events_loc, $rid, $unid, $upid, $distid, $proj_id, $gender){

        if($unid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND unid = '$unid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            }
        } elseif ($upid != null){
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND upid = '$upid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            }
        } else {
            if($gender == 'male'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else if($gender == 'female'){
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            } else {
                $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND distid = '$distid' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
            }
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsBenLastData($rid, $unid, $upid, $distid, $proj_id)
    {
        if($unid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `unid` = '$unid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY unid"));
        } elseif ($upid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `upid` = '$upid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY upid"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `distid` = '$distid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY distid"));
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;

    }

    public static function getEventsBenReportData($rid, $unid, $upid, $distid, $proj_id)
    {
        if($unid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `unid` = '$unid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY unid"));
        } elseif ($upid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `upid` = '$upid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY upid"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `distid` = '$distid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY distid"));
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;

    }

    public static function getEventsBenLastDataSchool($rid, $unid, $upid, $distid, $proj_id)
    {
        if($unid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `unid` = '$unid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND ev_loc = 'School' GROUP BY `ev_cdf`) as bests GROUP BY unid"));
        } elseif ($upid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `upid` = '$upid' AND proj_id = '$proj_id' AND ev_name NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND ev_loc = 'School' GROUP BY `ev_cdf`) as bests GROUP BY upid"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `distid` = '$distid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND ev_loc = 'School' GROUP BY `ev_cdf`) as bests GROUP BY distid"));
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;

    }

    public static function getEventsBenReportDataSchool($rid, $unid, $upid, $distid, $proj_id)
    {
        if($unid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `unid` = '$unid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'School' GROUP BY `ev_cdf`) as bests GROUP BY unid"));
        } elseif ($upid != null){
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `upid` = '$upid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'School' GROUP BY `ev_cdf`) as bests GROUP BY upid"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `distid` = '$distid' AND `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'School' GROUP BY `ev_cdf`) as bests GROUP BY distid"));
        }

        $row_eventdata = json_decode(json_encode((array) $raw_query_events), true);
        return $row_eventdata;

    }

  private function reportHeader()
  {
    $period = "";
    if($this->request->has('rep_data_id'))
    {
      if($this->request->input('rep_data_id') != "all")
      {
        //$dd = \DB::table('rep_data')->where('id', $this->request->input('rep_data_id'))->first();
        $period = \DB::table('rep_period')->where('rep_id', $this->request->rep_data_id)->first();
        $period = $period->period;
      }
      elseif($this->request->input('rep_data_id') == "all")
      {
        $period = "All";
      }
    }

    if($this->request->has('union_id') && $this->request->union_id != "")
    {

      if($this->request->union_id == "all")
      {
        $role = \Auth::user()->roles->first()->name;

        if($role == "upazila_admin"){
          $upazila = Upazila::with('district.region')->find(auth()->user()->upid);
        }else{
          $upazila = Upazila::with('district.region')->find($this->request->input('upazila_id'));
        }


        $r_name = "";
        $d_name = "";
        $up_name = "";

        if(isset($upazila->district->region->region_name)) $r_name = $upazila->district->region->region_name;
        if(isset($upazila->district->distname)) $d_name = $upazila->district->distname;
        if(isset($upazila->upname)) $up_name = $upazila->upname;

        if($this->report_type == "print")
        {
          return '<table width="100%" border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$r_name.'</div></td>
                <th><div>District</div></th><td><div>'.$d_name.'</div></td>
                <th><div>Upazila</div></th><td><div>'.$up_name.'</div></td>
                <th><div>Union</div></th><td><div> ALL </div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }

        return
          '<p>
            <strong>Region</strong>: '.$r_name.'&nbsp;|
            <strong>District</strong>: '.$d_name.'&nbsp;|
            <strong>Upazila</strong>: '.$up_name.'&nbsp;|
            <strong>Union</strong>: ALL &nbsp;|
            <strong>Report Period</strong>: '.$period.'
          </p>';

      }else{

        $union = Union::with('upazila.district.region')->find($this->request->input('union_id'));

        $r_name = "";
        $d_name = "";
        $up_name = "";
        $u_name = "";

        if(isset($union->upazila->district->region->region_name)) $r_name = $union->upazila->district->region->region_name;
        if(isset($union->upazila->district->distname)) $d_name = $union->upazila->district->distname;
        if(isset($union->upazila->upname)) $up_name = $union->upazila->upname;
        if(isset($union->unname)) $u_name = $union->unname;


        if($this->report_type == "print")
        {
          return '<table width="100%"  border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$r_name.'</div></td>
                <th><div>District</div></th><td><div>'.$d_name.'</div></td>
                <th><div>Upazila</div></th><td><div>'.$up_name.'</div></td>
                <th><div>Union</div></th><td><div>'.$u_name.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
          '<p>
            <strong>Region</strong>: '.$r_name.'&nbsp;|
            <strong>District</strong>: '.$d_name.'&nbsp;|
            <strong>Upazila</strong>: '.$up_name.'&nbsp;|
            <strong>Union</strong>: '.$u_name.'&nbsp;|
            <strong>Report Period</strong>: '.$period.'
          </p>';
      }
    }

    elseif($this->request->has('upazila_id') && $this->request->upazila_id != "")
    {
      if($this->request->upazila_id == "all")
      {
        $district = District::with('region')->find($this->request->input('district_id'));

        $r_name = "";
        $d_name = "";

        if(isset($district->region->region_name)) $r_name = $district->region->region_name;
        if(isset($district->distname)) $d_name = $district->distname;


        if($this->report_type == "print")
        {
          return '<table width="100%"  border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$r_name.'</div></td>
                <th><div>District</div></th><td><div>'.$d_name.'</div></td>
                <th><div>Upazila</div></th><td><div> ALL </div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$r_name.'&nbsp;|
          <strong>District</strong>: '.$d_name.'&nbsp;|
          <strong>Upazila</strong>: All &nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';

      }else
      {
        $upazila = Upazila::with('district.region')->find($this->request->input('upazila_id'));


        $r_name = "";
        $d_name = "";
        $up_name = "";

        if(isset($upazila->district->region->region_name)) $r_name = $upazila->district->region->region_name;
        if(isset($upazila->district->distname)) $d_name = $upazila->district->distname;
        if(isset($upazila->upname)) $up_name = $upazila->upname;

        if($this->report_type == "print")
        {
          return '<table width="100%">
              <tr>
                <th><div>Region</div></th><td><div>'.$r_name.'</div></td>
                <th><div>District</div></th><td><div>'.$d_name.'</div></td>
                <th><div>Upazila</div></th><td><div>'.$up_name.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$r_name.'&nbsp;|
          <strong>District</strong>: '.$d_name.'&nbsp;|
          <strong>Upazila</strong>: '.$up_name.'&nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';
      }
    }

    elseif($this->request->has('district_id')  && $this->request->district_id != "")
    {
      if($this->request->district_id == "all")
      {

        if($this->role == "superadministrator")
        {
          if($this->report_type == "print")
          {
            return '<table width="100%" border="1">
                <tr>
                  <th><div>District</div></th><td><div>All</div></td>
                  <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
                </tr>
              </table>';
          }
          return
          '<p>
            <strong>District</strong>: All &nbsp;|
            <strong>Report Period</strong>: '.$period.'
          </p>';
        }



        $region = Region::find(auth()->user()->region_id);

        $r_name = isset($region->region_name) ? $region->region_name : "";

        if($this->report_type == "print")
        {
          return '<table width="100%" border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$r_name.'</div></td>
                <th><div>District</div></th><td><div>All</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }

        return
        '<p>
          <strong>Region</strong>: '.$r_name.'&nbsp;|
          <strong>District</strong>: All &nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';
      }else
      {
        $district = District::with('region')->find($this->request->input('district_id'));

        $r_name = "";
        $d_name = "";


        if(isset($district->region->region_name)) $r_name = $district->region->region_name;
        if(isset($district->distname)) $d_name = $district->distname;


        if($this->report_type == "print")
        {
          return '<table width="100%">
              <tr>
                <th><div>Region</div></th><td><div>'.$r_name.'</div></td>
                <th><div>District</div></th><td><div>'.$d_name.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$r_name.'&nbsp;|
          <strong>District</strong>: '.$d_name.'&nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';
      }

    }
  }
}
