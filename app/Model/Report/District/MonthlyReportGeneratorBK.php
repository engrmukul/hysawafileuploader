<?php

namespace App\Model\Report\District;

use App\Model\District;
use App\Model\Region;
use App\Model\Union;
use App\Model\Upazila;
use App\ReportData;
use DB;
use Illuminate\Http\Request;

class MonthlyReportGeneratorBK
{
  private $reportData;
  private $row_lastdata;
  private $row_totaldata;
  private $row_getbaseline;
  private $row_row_gettargets;
  private $report_type;
  private $request;
  private $view_path;

  private $REPORTDATAQUERYSELECT = "SELECT
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
      Sum(rep_data.HHS_new_hc) AS HHS_new_hc,
      Sum(rep_data.HHS_rep) AS HHS_rep,
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
      Sum(rep_data.TW_maintenance) AS TW_maintenance,
      Sum(rep_data.wsp) AS wsp,
      Sum(rep_data.CT_trg) AS CT_trg,
      Sum(rep_data.pdb) AS pdb";

  public function __construct(Request $request, $view_path = 'core.district.monthly-report.water.')
  {
    $this->reportData = "";
    $this->row_lastdata = "";
    $this->row_totaldata = "";
    $this->row_getbaseline = "";
    $this->row_row_gettargets = "";
    $this->report_type = $request->input('report_type');
    $this->request = $request;

    $this->view_path = $view_path;
    $this->process($request);
  }

  public function getReport()
  {
    $ReportData = $this->reportData;
    $row_lastdata = $this->row_lastdata;
    $row_totaldata = $this->row_totaldata;
    $row_getbaseline = $this->row_getbaseline;
    $row_gettargets = $this->row_gettargets;

    $reportHeader = $this->reportHeader();
    //dd($ReportData);

    if($this->report_type == "print")
    {
      return view($this->view_path.'print',
        compact('ReportData','row_lastdata','row_totaldata','row_getbaseline','row_gettargets', 'union', 'period', 'reportHeader'));
    }

    $districts = District::where('region_id', auth()->user()->region_id)->get();

    return view($this->view_path.'show',
      compact('ReportData','row_lastdata','row_totaldata','row_getbaseline','row_gettargets', 'districts', 'reportHeader')
    );
  }

  private function process(Request $request)
  {
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
    }


    elseif($request->has('union_id') && $request->union_id != "")
    {
      if($request->has('rep_data_id') && $request->rep_data_id == "all"){
        $this->unionMonthlyReportAll($request);
      }else{
        $this->unionMonthlyReport($request);
      }
    }elseif($request->has('upazila_id') && $request->upazila_id != "")
    {
      if($request->upazila_id == "all"){
        $this->districtMonthlyReport($request);
      }else{
        $this->upazilaMonthlyReportAll($request);
      }
    }elseif($request->has('district_id') && $request->district_id != "all")
    {
      $this->districtMonthlyReport($request);
    }
    elseif($request->has('district_id') && $request->district_id = "all")
    {
       $this->allDistrictMontlyReportAll($request);
    }
    return $this;
  }

  private function unionAllReportAll(Request $request)
  {
    $upazila = Upazila::find($request->input('upazila_id'));
    $id = $upazila->id;
    $type = "upid";

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

    $id = auth()->user()->region_id;
    $type = "region_id";
    $last_report_id = $this->_getLastReportId($type, $id);

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getEmptyRepDataInstance();
    $this->reportData      = $this->_getEmptyRepDataInstance();
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
  }

  private function districtAllReportOne(Request $request)
  {
    $id = auth()->user()->region_id;
    $type = "region_id";
    $last_report_id = $request->input('rep_data_id');

    $this->row_getbaseline = $this->_getBaseLineData($id, $type);
    $this->row_gettargets  = $this->_getTargetData($id, $type);
    $this->row_lastdata    = $this->_getLastData($last_report_id, $id, $type);
    $this->reportData      = $this->_getThisMonthData($last_report_id, $id, $type);
    $this->row_totaldata = $this->_getTotalData($last_report_id, $id, $type);
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
    $data = DB::select(DB::raw($this->REPORTDATAQUERYSELECT. " from rep_data where rep_id != 100 and rep_id != 99 "));
    $response = [];

    foreach($data[0] as $key => $value)
    {
      $response[$key] = null;
    }

   // dd($response);
    return $response;
  }

  private function _getBaseLineData($id, $type = "unid")
  {
    $row_getbaseline=DB::select( DB::raw($this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 99 and $type=$id" ));
    $row_getbaseline = json_decode(json_encode((array) $row_getbaseline), true);
    return @$row_getbaseline[0];
  }

  private function _getTargetData($id, $type = "unid")
  {
    $row_gettargets=DB::select( DB::raw($this->REPORTDATAQUERYSELECT ." FROM rep_data WHERE rep_id = 100  and $type=$id" ));
    $row_gettargets = json_decode(json_encode((array) $row_gettargets), true);
    return @$row_gettargets[0];
  }

  private function _getLastData($last_report_id, $id, $type = "unid")
  {
    $row_lastdata=DB::select(DB::raw(
      $this->REPORTDATAQUERYSELECT. "
      FROM rep_data WHERE rep_id < $last_report_id  AND $type=$id")
    );
    $row_lastdata = json_decode(json_encode( (array) $row_lastdata), true);
    return $row_lastdata[0];
  }

  private function _getTotalData($last_report_id, $id, $type = "unid")
  {
    $row_totaldata =DB::select(DB::raw($this->REPORTDATAQUERYSELECT . "
      FROM rep_data WHERE rep_id <= $last_report_id AND $type = $id" ));
    $row_totaldata = json_decode(json_encode((array) $row_totaldata), true);
    return $row_totaldata[0];
  }

  private function _getThisMonthData($last_report_id, $id, $type = "unid")
  {
    $ReportData = \DB::select(\DB::raw($this->REPORTDATAQUERYSELECT .
      " FROM rep_data WHERE rep_id = $last_report_id AND $type=$id"));
    if(count($ReportData))
    {
      return (array)$ReportData[0];
    }
  }

  private function _getLastReportId($type, $id)
  {
    return \DB::table('rep_data')->where($type, $id)
      ->where('rep_id', '!=', 99)
      ->where('rep_id', '!=', 100)
      ->orderBy('rep_id', 'DESC')
      ->get()
      ->first()->rep_id;
  }

  private function reportHeader()
  {
    $period = "";
    if($this->request->has('rep_data_id'))
    {
      if($this->request->input('rep_data_id') != "all")
      {
        $dd = \DB::table('rep_data')->where('id', $this->request->input('rep_data_id'))->first();
        $period = \DB::table('rep_period')->where('rep_id', $dd->rep_id)->first();
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

        $upazila = Upazila::with('district.region')->find($this->request->input('upazila_id'));

        if($this->report_type == "print")
        {
          return '<table width="100%" border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$upazila->district->region->region_name.'</div></td>
                <th><div>District</div></th><td><div>'.$upazila->district->distname.'</div></td>
                <th><div>Upazila</div></th><td><div>'.$upazila->upname.'</div></td>
                <th><div>Union</div></th><td><div> ALL </div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$upazila->district->region->region_name.'&nbsp;|
          <strong>District</strong>: '.$upazila->district->distname.'&nbsp;|
          <strong>Upazila</strong>: '.$upazila->upname.'&nbsp;|
          <strong>Union</strong>: ALL &nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';


      }else{

        $union = Union::with('upazila.district.region')->find($this->request->input('union_id'));
        if($this->report_type == "print")
        {
          return '<table width="100%"  border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$union->upazila->district->region->region_name.'</div></td>
                <th><div>District</div></th><td><div>'.$union->upazila->district->distname.'</div></td>
                <th><div>Upazila</div></th><td><div>'.$union->upazila->upname.'</div></td>
                <th><div>Union</div></th><td><div>'.$union->unname.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$union->upazila->district->region->region_name.'&nbsp;|
          <strong>District</strong>: '.$union->upazila->district->distname.'&nbsp;|
          <strong>Upazila</strong>: '.$union->upazila->upname.'&nbsp;|
          <strong>Union</strong>: '.$union->unname.'&nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';

      }
    }

    elseif($this->request->has('upazila_id') && $this->request->upazila_id != "")
    {
      if($this->request->upazila_id == "all")
      {
        $district = District::with('region')->find($this->request->input('district_id'));
        if($this->report_type == "print")
        {
          return '<table width="100%"  border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$district->region->region_name.'</div></td>
                <th><div>District</div></th><td><div>'.$district->distname.'</div></td>
                <th><div>Upazila</div></th><td><div> ALL </div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$district->region->region_name.'&nbsp;|
          <strong>District</strong>: '.$district->distname.'&nbsp;|
          <strong>Upazila</strong>: All &nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';

      }else
      {
        $upazila = Upazila::with('district.region')->find($this->request->input('upazila_id'));
        if($this->report_type == "print")
        {
          return '<table width="100%">
              <tr>
                <th><div>Region</div></th><td><div>'.$upazila->district->region->region_name.'</div></td>
                <th><div>District</div></th><td><div>'.$upazila->district->distname.'</div></td>
                <th><div>Upazila</div></th><td><div>'.$upazila->upname.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$upazila->district->region->region_name.'&nbsp;|
          <strong>District</strong>: '.$upazila->district->distname.'&nbsp;|
          <strong>Upazila</strong>: '.$upazila->upname.'&nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';
      }
    }

    elseif($this->request->has('district_id')  && $this->request->district_id != "")
    {
      if($this->request->district_id == "all")
      {
        $region = Region::find(\auth()->user()->region_id);
        if($this->report_type == "print")
        {
          return '<table width="100%" border="1">
              <tr>
                <th><div>Region</div></th><td><div>'.$region->region_name.'</div></td>
                <th><div>District</div></th><td><div>All</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$region->region_name.'&nbsp;|
          <strong>District</strong>: All &nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';
      }else
      {
        $district = District::with('region')->find($this->request->input('district_id'));
        if($this->report_type == "print")
        {
          return '<table width="100%">
              <tr>
                <th><div>Region</div></th><td><div>'.$district->region->region_name.'</div></td>
                <th><div>District</div></th><td><div>'.$district->distname.'</div></td>
                <th><div>Report Period</div></th><td><div>'.$period.'</div></td>
              </tr>
            </table>';
        }
        return
        '<p>
          <strong>Region</strong>: '.$district->region->region_name.'&nbsp;|
          <strong>District</strong>: '.$district->distname.'&nbsp;|
          <strong>Report Period</strong>: '.$period.'
        </p>';
      }

    }
  }
}
