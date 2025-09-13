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

class MonthlyReportGeneratorProject
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

    public function __construct(Request $request, $view_path = 'core.district.project-report.')
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

        if ($this->report_type == "print") {
            return view($this->view_path . 'print',
                compact('ReportData', 'row_lastdata', 'row_totaldata', 'row_getbaseline', 'row_gettargets', 'reportHeader', 'request'));


        } else {

            $districts = District::where('region_id', auth()->user()->region_id)->get();


            if ($this->role == "superadministrator") {
                $districts = District::all();
            }

            return view($this->view_path . 'show',
                compact('ReportData', 'row_lastdata', 'row_totaldata', 'row_getbaseline', 'row_gettargets', 'districts', 'reportHeader', 'request')
            );
        }

    }

    private function process(Request $request)
    {
        $proid = $request->input('project_id');
        $last_report_id = $this->_getLastReportId($proid);

        $this->row_getbaseline = $this->_getBaseLineData($proid);
        $this->row_gettargets = $this->_getTargetData($proid);
        $this->row_lastdata = $this->_getLastData($last_report_id, $proid);
        $this->reportData = $this->_getThisMonthData($last_report_id, $proid);
        $this->row_totaldata = $this->_getTotalData($last_report_id, $proid);
    }

    private function _getEmptyRepDataInstance($proid)
    {
        $data = DB::select(DB::raw($this->REPORTDATAQUERYSELECT . " from rep_data where rep_id != 1000 and rep_id != 999 and proj_id =" . $proid));
        $response = [];

        foreach ($data[0] as $key => $value) {
            $response[$key] = null;
        }

        // dd($response);
        return $response;
    }

    private function _getBaseLineData($proid = null)
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = 999 and proj_id = " . $proid;
        $row_getbaseline = DB::select(DB::raw($query));
        $row_getbaseline = json_decode(json_encode((array)$row_getbaseline), true);
        return @$row_getbaseline[0];
    }

    private function _getTargetData($proid = null)
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = 1000 and proj_id =" . $proid;
        $row_gettargets = DB::select(DB::raw($query));
        $row_gettargets = json_decode(json_encode((array)$row_gettargets), true);
        return @$row_gettargets[0];
    }

    private function _getLastData($last_report_id, $proid)
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id < $last_report_id and proj_id =" . $proid;
        $row_lastdata = DB::select(DB::raw($query));
        $row_lastdata = json_decode(json_encode((array)$row_lastdata), true);
        return $row_lastdata[0];
    }

    private function _getTotalData($last_report_id, $proid)
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id <= $last_report_id and proj_id =" . $proid;
        if ($proid != null) {
            $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id <= $last_report_id and proj_id =" . $proid;


        }

        $row_totaldata = DB::select(DB::raw($query));
        $row_totaldata = json_decode(json_encode((array)$row_totaldata), true);
        return $row_totaldata[0];
    }

    private function _getThisMonthData($last_report_id, $proid)
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id and proj_id =" . $proid;
        if ($proid != null) {
            $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id and proj_id =" . $proid;
        }

        $ReportData = \DB::select(\DB::raw($query));
        if (count($ReportData)) {
            return (array)$ReportData[0];
        }
    }

    private function _getThisMonthSCLBen($last_report_id, $proid = null)
    {
        $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id and proj_id =" . $proid;
        if ($proid != null) {
            $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $last_report_id AND proj_id =" . $proid;
        }

        $ReportData = \DB::select(\DB::raw($query));
        if (count($ReportData)) {
            return (array)$ReportData[0];
        }
    }

    private function _getLastReportId($proid = null)
    {
        $data = \DB::table('rep_data')
            ->where('proj_id', $proid)
            ->where('rep_id', '!=', 999)
            ->where('rep_id', '!=', 1000)
            ->orderBy('rep_id', 'DESC')
            ->get();

        if (count($data)) {
            return $data->first()->rep_id;
        }

        //throw new \Exception("Data Not Found.", 1);

    }

    public static function getEventsLastData($events_name, $rid, $unid, $upid, $distid, $proj_id, $gender)
    {

        if ($gender == 'male') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id'"));
        } else if ($gender == 'female') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id'"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id'"));
        }
        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsReportData($events_name, $rid, $unid, $upid, $distid, $proj_id, $gender)
    {

        if ($gender == 'male') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id'"));
        } else if ($gender == 'female') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id'"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.`ev_name` = '$events_name' AND `mob_app_data_list`.`proj_id` = '$proj_id'"));
        }

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsReportDataSchool($rid, $unid, $upid, $distid, $proj_id, $gender)
    {

        if ($gender == 'male') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
        } else if ($gender == 'female') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
        }

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsLastDataSchool($rid, $unid, $upid, $distid, $proj_id, $gender)
    {

        if ($gender == 'male') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
        } else if ($gender == 'female') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND ev_loc = 'School' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation')"));
        }

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;
    }

    public static function getSingleEventReportDataLoc($events_name, $events_loc, $rid, $unid, $upid, $distid, $proj_id, $gender)
    {

        if ($gender == 'male') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
        } else if ($gender == 'female') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id = '$rid' AND `mobile_app_data_events`.ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
        }

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;
    }

    public static function getSingleEventLastDataLoc($events_name, $events_loc, $rid, $unid, $upid, $distid, $proj_id, $gender)
    {

        if ($gender == 'male') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
        } else if ($gender == 'female') {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
        } else {
            $raw_query_events = DB::select(DB::raw("SELECT SUM(`mobile_app_data_events`.`ev_male`+`mobile_app_data_events`.`ev_female`) as tot_participants, COUNT(*) 
    as sessions, `mobile_app_data_events`.`ev_name` as event_name, `mob_app_data_list`.`distid` 
    as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid 
FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` 
WHERE rep_id < '$rid' AND `mobile_app_data_events`.ev_loc = '$events_loc' AND `mob_app_data_list`.`proj_id` = '$proj_id' AND `mobile_app_data_events`.`ev_name` = '$events_name'"));
        }

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;
    }

    public static function getEventsBenLastData($rid, $unid, $upid, $distid, $proj_id)
    {

        $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY proid"));
        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;

    }

    public static function getEventsBenReportData($rid, $unid, $upid, $distid, $proj_id)
    {

        $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'Community' GROUP BY `ev_cdf`) as bests GROUP BY proid"));


        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;

    }

    public static function getEventsBenLastDataSchool($rid, $unid, $upid, $distid, $proj_id)
    {
        $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` < '$rid' AND `ev_loc` = 'School' GROUP BY `ev_cdf`) as bests GROUP BY proid"));

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;

    }

    public static function getEventsBenReportDataSchool($rid, $unid, $upid, $distid, $proj_id)
    {

        $raw_query_events = DB::select(DB::raw("SELECT `ev_cdf`, SUM(best) as sum_of_max, sessions, proid, distid, upid, unid FROM (SELECT COUNT(*) as sessions, `ev_cdf`, `ev_name`, `ev_loc`, `rep_id`, MAX(`ev_male`+`ev_female`) as best, `mob_app_data_list`.`distid` as distid, `mob_app_data_list`.`upid` as upid, `mob_app_data_list`.`unid` as unid, `mob_app_data_list`.`proj_id` as proid FROM `mobile_app_data_events` LEFT JOIN `mob_app_data_list` ON `mobile_app_data_events`.`mob_app_list_id` = `mob_app_data_list`.`id` WHERE `proj_id` = '$proj_id' AND `ev_name` NOT IN('Animation/Video Show', 'Drama Show', 'Infrastructure Related', 'Site Selection', 'Day Observation') AND `rep_id` = '$rid' AND `ev_loc` = 'School' GROUP BY `ev_cdf`) as bests GROUP BY proid"));

        $row_eventdata = json_decode(json_encode((array)$raw_query_events), true);
        return $row_eventdata;

    }

    private function reportHeader()
    {
        $period = "";
        if ($this->request->has('rep_data_id')) {
            if ($this->request->input('rep_data_id') != "all") {
                //$dd = \DB::table('rep_data')->where('id', $this->request->input('rep_data_id'))->first();
                $period = \DB::table('rep_period')->where('rep_id', $this->request->rep_data_id)->first();
                $period = $period->period;
            } elseif ($this->request->input('rep_data_id') == "all") {
                $period = "All";
            }
        }

        return '<p><strong>Report Period</strong>: ' . $period . '</p>';
    }
}
