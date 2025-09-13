<?php

namespace App\Model\Report\District;

use App\Model\District;
use App\Model\Project;
use App\Model\Region;
use App\Model\Union;
use App\Model\Upazila;
use App\ReportData;
use DB;
use Illuminate\Http\Request;
use Log;

class ProjectReportGeneratorAdmin
{
  private $reportData;
  private $row_lastdata;
  private $row_totaldata;
  private $row_getbaseline;
  private $row_row_gettargets;
  private $row_gettargets;
  private $report_type;
  public $request;
  public $requestd;
  private $view_path;

  private $role;

  private $REPORTDATAQUERYSELECT = "SELECT
      rep_data.id AS report_id,
      Sum(rep_data.cdf_no) AS cdf_no,
      Sum(sa_approved) AS sa_approved,
      Sum(sa_completed) AS sa_completed,
      Sum(sa_benef) AS sa_benef,
      Sum(sa_renovated) AS sa_renovated,
      Sum(ws_approved) AS ws_approved,
      Sum(ws_completed) AS ws_completed,
      Sum(ws_beneficiary) AS ws_beneficiary,
      Sum(ws_hc_benef) AS ws_hc_benef,
      Sum(ws_50) AS ws_50,
      Sum(rep_data.cdf_pop) AS cdf_pop,
      Sum(rep_data.cdf_male) AS cdf_male,
      Sum(rep_data.cdf_female) AS cdf_female,
      Sum(rep_data.cdf_pop_hc) AS cdf_pop_hc,
      Sum(rep_data.cdf_hh) AS cdf_hh,
      Sum(rep_data.cdf_hh_hc) AS cdf_hh_hc,
      Sum(rep_data.cdf_pop_disb) AS cdf_pop_disb,
      Sum(rep_data.cdf_pop_safety) AS cdf_pop_safety,
      Sum(rep_data.cdf_cf_tot) AS cdf_cf_tot,
      Sum(rep_data.cdf_cf_male) AS cdf_cf_male,
      Sum(rep_data.cdf_cf_female) AS cdf_cf_female,
      Sum(rep_data.cb_trg) AS cb_trg,
      Sum(rep_data.cb_trg_up_total) AS cb_trg_up_total,
      Sum(rep_data.cb_trg_up_male) AS cb_trg_up_male,
      Sum(rep_data.cb_trg_up_female) AS cb_trg_up_female,
      Sum(rep_data.cb_trg_stf_total) AS cb_trg_stf_total,
      Sum(rep_data.cb_trg_stf_male) AS cb_trg_stf_male,
      Sum(rep_data.cb_trg_stf_female) AS cb_trg_stf_female,
      Sum(rep_data.cb_trg_vol_total) AS cb_trg_vol_total,
      Sum(rep_data.cb_trg_vol_male) AS cb_trg_vol_male,
      Sum(rep_data.cb_trg_vol_female) AS cb_trg_vol_female,
      Sum(rep_data.CHY_hw_ses) AS CHY_hw_ses,
      Sum(rep_data.CHY_hw_male) AS CHY_hw_male,
      Sum(rep_data.CHY_hw_female) AS CHY_hw_female,
      Sum(rep_data.CHY_mn_ses) AS CHY_mn_ses,
      Sum(rep_data.CHY_mn_female) AS CHY_mn_female,
      Sum(rep_data.CHY_sa_ses) AS CHY_sa_ses,
      Sum(rep_data.CHY_sa_hh) AS CHY_sa_hh,
      Sum(rep_data.CHY_fh_ses) AS CHY_fh_ses,
      Sum(rep_data.CHY_fh_hh) AS CHY_fh_hh,
      Sum(rep_data.CHY_gb_new) AS CHY_gb_new,
      Sum(rep_data.CHY_gb_rep) AS CHY_gb_rep,
      Sum(rep_data.CHY_dr) AS CHY_dr,
      Sum(rep_data.CHY_dr_pop) AS CHY_dr_pop,
      Sum(rep_data.CHY_vd) AS CHY_vd,
      Sum(rep_data.CHY_vd_pop) AS CHY_vd_pop,
      Sum(rep_data.HHS_new) AS HHS_new,
      Sum(rep_data.HHS_new_amount) AS HHS_new_amount,
      Sum(rep_data.HHS_new_hc) AS HHS_new_hc,
      Sum(rep_data.HHS_rep) AS HHS_rep,
      Sum(rep_data.HHS_rep_amount) AS HHS_rep_amount,
      Sum(rep_data.HHS_rep_hc) AS HHS_rep_hc,
      Sum(rep_data.scl_tot) AS scl_tot,
      Sum(rep_data.scl_tot_std) AS scl_tot_std,
      Sum(rep_data.scl_boys) AS scl_boys,
      Sum(rep_data.scl_girls) AS scl_girls,
      Sum(rep_data.scl_pri) AS scl_pri,
      Sum(rep_data.scl_pri_std) AS scl_pri_std,
      Sum(rep_data.scl_high) AS scl_high,
      Sum(rep_data.scl_high_std) AS scl_high_std,
      Sum(rep_data.scl_mad) AS scl_mad,
      Sum(rep_data.scl_mad_std) AS scl_mad_std,
      Sum(rep_data.scl_hp_ses) AS scl_hp_ses,
      Sum(rep_data.scl_hp_boys) AS scl_hp_boys,
      Sum(rep_data.scl_hp_girls) AS scl_hp_girls,
      Sum(rep_data.scl_mn_ses) AS scl_mn_ses,
      Sum(rep_data.scl_mn_girls) AS scl_mn_girls,
      Sum(rep_data.scl_dr) AS scl_dr,
      Sum(rep_data.scl_dr_std) AS scl_dr_std,
      Sum(rep_data.scl_vd) AS scl_vd,
      Sum(rep_data.scl_vd_std) AS scl_vd_std,
      Sum(rep_data.up_stf_tot) AS up_stf_tot,
      Sum(rep_data.up_stf_male) AS up_stf_male,
      Sum(rep_data.up_stf_female) AS up_stf_female,
      Sum(rep_data.up_pngo) AS up_pngo,
      Sum(rep_data.up_cont) AS up_cont,
      Sum(rep_data.up_board) AS up_board,
      Sum(rep_data.TW_maintenance) AS TW_maintenance,
      Sum(rep_data.wsp) AS wsp,
      Sum(rep_data.CT_trg) AS CT_trg,
      Sum(rep_data.Hws) AS Hws,
      Sum(rep_data.Wf) AS Wf,
      Sum(rep_data.Pfr) AS Pfr,
      Sum(rep_data.pdb) AS pdb,
      Sum(rep_data.improved_stoves) AS improved_stoves,
      Sum(rep_data.solar_power) AS solar_power,
      Sum(rep_data.tree) AS tree";

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
    $this->requestd = $request;

    $this->view_path = $view_path;

    $this->role = \Auth::user()->roles->first()->name;

    $this->process($request);
    
  }

  public function getReport()
  {
    $ReportData = $this->reportData;
    $ReportIdInfo = $this->reportIdInfo;
    $ReportPeriodInfo = $this->reportPeriodInfo;
    $row_lastdata = $this->row_lastdata;
    $row_totaldata = $this->row_totaldata;
    $row_getbaseline = $this->row_getbaseline;
    $row_gettargets = $this->row_gettargets;
    $reportHeader = $this->reportHeader();

    if($this->report_type == "print")
    {
      return view($this->view_path.'print',
        compact('ReportData','row_lastdata','row_totaldata','row_getbaseline','row_gettargets', 'period', 'reportHeader', 'ReportIdInfo', 'ReportPeriodInfo'));

    }else
    {
        return view($this->view_path.'show',
          compact('ReportData','row_lastdata','row_totaldata','row_getbaseline','row_gettargets', 'reportHeader', 'ReportInfo', 'ReportIdInfo', 'ReportPeriodInfo')
        );
    }


  }

  private function process(Request $request)
  {
      $this->projectOneReportOne($request);

    return $this;
  }

//  private function projectOneReportAll(Request $request)
//  {
//    $request = $this->request;
//    $id = $request->input('project_id');
//    $type = "proj_id";
//    $last_report_id = $this->_getLastReportId($type, $id);
//    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
//    $this->row_gettargets  = $this->_getTargetData($id, $type);
//    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
//    $this->reportData      = $this->_getEmptyRepDataInstance();
//    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
//  }

    private function projectOneReportOne(Request $request)
    {
        $request = $this->request;
        $id = $request->input('project_id');
        $type = "proj_id";
        //$last_report_id = $this->_getLastReportId($type, $id);
        $current_report_id = $request->input('rep_data_id');

        $this->row_getbaseline = $this->_getBaseLineData($id, $type);
        $this->row_gettargets  = $this->_getTargetData($id, $type);
        $this->row_lastdata    = $this->_getLastData($current_report_id, $id, $type);
        $this->reportData      = $this->_getThisMonthData($current_report_id, $id, $type);
        $this->reportIdInfo      = $this->_getReportIdInfo($current_report_id);
        $this->reportPeriodInfo      = $this->_getReportPeriodInfo($current_report_id);
        $this->row_totaldata = $this->_getTotalData($current_report_id, $id, $type);
    }

  private function _getEmptyRepDataInstance()
  {
      $request = $this->request;
      $data = DB::select(DB::raw($this->REPORTDATAQUERYSELECT. " from rep_data where rep_id != 100 and rep_id != 99 and proj_id =".$request->input('project_id').""));
      $response = [];

      foreach($data[0] as $key => $value)
      {
          $response[$key] = null;
      }

      // dd($response);
      return $response;
  }

  private function _getBaseLineData($id = null, $type = "proj_id")
  {
    $request = $this->request;
    $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 99 and proj_id = ".$request->input('project_id')."";
    $row_getbaseline = DB::select(DB::raw($query));
    $row_getbaseline = json_decode(json_encode((array) $row_getbaseline), true);
    return @$row_getbaseline[0];
  }

  private function _getTargetData($id = null, $type = "proj_id")
  {
    $request = $this->request;
    $query = $this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 100 and proj_id =".$request->input('project_id')."";
    $row_gettargets=DB::select( DB::raw($query));
    $row_gettargets = json_decode(json_encode((array) $row_gettargets), true);
    return @$row_gettargets[0];
  }

  private function _getLastData($current_report_id, $id = null, $type = "proj_id")
  {
    $request = $this->request;
    $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id < $current_report_id and proj_id =".$request->input('project_id')."";

    $row_lastdata = DB::select(DB::raw($query));
    $row_lastdata = json_decode(json_encode( (array) $row_lastdata), true);
    return $row_lastdata[0];
  }

  private function _getTotalData($current_report_id, $id = null, $type = "proj_id")
  {
    $request = $this->request;
    $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id <= $current_report_id and proj_id =".$request->input('project_id')." ";

    $row_totaldata =DB::select(DB::raw($query));
    $row_totaldata = json_decode(json_encode((array) $row_totaldata), true);
    return $row_totaldata[0];
  }

  private function _getThisMonthData($current_report_id, $id = null, $type = "proj_id")
  {
    $request = $this->request;
    $query = $this->REPORTDATAQUERYSELECT . " FROM rep_data WHERE rep_id = $current_report_id and proj_id =".$request->input('project_id')."";

    $ReportData = \DB::select(\DB::raw($query));
    if(count($ReportData))
    {
      return (array)$ReportData[0];
    }
  }

  private function _getLastReportId($type = null, $id = null)
  {
    $data = \DB::table('rep_data')
      ->where($type, $id)
      ->where('rep_id', '!=', 99)
      ->where('rep_id', '!=', 100)
      ->orderBy('rep_id', 'DESC')
      ->get();

    if(count($data))
    {
      return $data->first()->rep_id;
    }

    throw new \Exception("Data Not Found.", 1);

  }

    private function _getReportIdInfo($current_report_id)
    {
        return $current_report_id;
    }

    private function _getReportPeriodInfo($current_report_id)
    {
        $query = "SELECT period FROM rep_period WHERE rep_id = " .$current_report_id;

        $PeriodData = \DB::select(\DB::raw($query));

        return $PeriodData[0]->period;
    }

  private function reportHeader()
  {
    $request = $this->request;
    $period = "";
    $project = Project::find($request->input('project_id'));
    $p_name = isset($project->project) ? $project->project : "";

    if($this->request->has('rep_data_id'))
    {

        $period = \DB::table('rep_period')->where('rep_id', $this->request->rep_data_id)->first();
        $period = $period->period;

    }


        if($this->report_type == "print")
        {
            return '<table width="100%" border="1">
              <tr>
                <th><div>Project</div></th><td><div>'.$p_name.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        } else {
            return
                '<p>
            <strong>Project</strong>: '.$p_name.' &nbsp;|
            <strong>Report Period</strong>: '.$period.'
          </p>';
        }
  }
}
