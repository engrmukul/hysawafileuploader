<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Model\District;
use App\Model\Download\AssesDeadlineV2Download;
use App\Model\Download\OMV2Download;
use App\Model\Download\RenovationV2Download;
use App\Model\Download\SanitaryInspectionPhysicalDownload;
use App\Model\Download\SanitaryInspectionPwsTsDownload;
use App\Model\Download\SanitaryInspectionRwhDownload;
use App\Model\Download\SanitaryInspectionTwHpDownload;
use App\Model\Download\SanitaryInspectionTwMpDownload;
use App\Model\Download\SanitaryInspectionV2Download;
use App\Model\Download\SchoolDownload;
use App\Model\Download\ClinicDownload;
use App\Model\Download\SpComWpV2Download;
use App\Model\Download\SpHcfV2Download;
use App\Model\Download\SpOMProgRepV2Download;
use App\Model\Download\SpSchoolV2Download;
use App\Model\Download\SpUptimeOMV2Download;
use App\Model\Download\VolumetricV2Download;
use App\Model\Download\WaterInfrastructuresV2Download;
use App\Model\Download\WaterOmRenV2Download;
use App\Model\Download\WaterWqStatusV2Download;
use App\Model\Download\WQTestsV2Download;
use App\Model\Download\BactNotificationDownload;
use App\Model\Download\OMNotificationDownload;
use App\Model\Download\SINotificationDownload;
use App\Model\Search\ClinicSearch;
use App\Model\Search\SpComWpSearch;
use App\Model\Search\SpHcfSearch;
use App\Model\Search\SpInfrastructureChart;
use App\Model\Search\SpInfrastructureSearch;
use App\Model\Search\SpInfrastructureSearchAjax;
use App\Model\Search\SpOMChart;
use App\Model\Search\SpOMCostChart;
use App\Model\Search\SpOMCostSearchAjax;
use App\Model\Search\SpOMNotiSearch;
use App\Model\Search\SpOMSearch;
use App\Model\Search\SpOMSearchAjax;
use App\Model\Search\SpSISearch;
use App\Model\Search\SpRenovationSearch;
use App\Model\Search\SpSanitaryInspectionChart;
use App\Model\Search\SpSanitaryInspectionSearch;
use App\Model\Search\SpSanitaryInspectionSearchAjax;
use App\Model\Search\SpSchoolSearch;
use App\Model\Search\SpSiV2PhysicalSearch;
use App\Model\Search\SpSiV2PwsTsSearch;
use App\Model\Search\SpSiV2RwhSearch;
use App\Model\Search\SpSiV2TwHpSearch;
use App\Model\Search\SpSiV2TwMpSearch;
use App\Model\Search\SpVolumetricSearch;
use App\Model\Search\SpWaterQualityChart;
use App\Model\Search\SpWaterQualitySearchAjax;
use App\Model\Search\SpBactSearch;
use App\Model\Search\SpWqTestSearch;
use App\Model\Search\WaterOmRenV2Search;
use App\Model\Search\WaterWqStatV2Search;
use App\Model\SPClinic;
use App\Model\SPFirebaseToken;
use App\Model\SPInfrastructure;
use App\Model\SPOM;
use App\Model\SPProblemReport;
use App\Model\SPProblemVerification;
use App\Model\SPRenovation;
use App\Model\SPRepairRen;
use App\Model\SPRepairType;
use App\Model\SPSampleCollection;
use App\Model\SPSanAnswer;
use App\Model\SPSanitaryInspection;
use App\Model\SPSchool;
use App\Model\SPVolumetric;
use App\Model\SPWaterQuality;
use App\Model\Upazila;
use App\Model\WaterQualityOthers;
use App\Model\SPSanInspectionV2;
use DateTime;
use Illuminate\Http\Request;
use DB;
use PHPMailer\PHPMailer\PHPMailer;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use sms_net_bd\SMS;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WeeklyWqSiReportExport;

// for writer type constants


class SPReportController extends Controller
{
  private $view_path = "core.safe_pani_admin.basic_data_entry.";
  private $route_path = "safe-pani-admin.data-entry.basic.";


  public function weeklyWqAndSiReport(Request $request)
  {
    $distid = (int) ($request->get('district_id', 6));
    $upazilaId = $request->get('upazila_id');
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date', date('Y-m-d'));

    $sql = $this->combinedSql($fromDate, $toDate, $upazilaId);

    // IMPORTANT: provide bindings for every placeholder used in SQL
    $bindings = [
      'd1'  => $distid,
      'd2'  => $distid,
      'd3'  => $distid,
      'd4'  => $distid,
      'd5'  => $distid,
      'd6'  => $distid,
      'd7'  => $distid,
      'd8'  => $distid,
      'd9'  => $distid,
      'd10' => $distid,
      'd11' => $distid,
    ];

    // Add date bindings if dates are provided
    if ($fromDate && $toDate) {
      $bindings['from_si'] = $fromDate;
      $bindings['to_si'] = $toDate;
      $bindings['from_sc'] = $fromDate;
      $bindings['to_sc'] = $toDate;
      $bindings['from_si_tech'] = $fromDate;
      $bindings['to_si_tech'] = $toDate;
      $bindings['from_bf'] = $fromDate;
      $bindings['to_bf'] = $toDate;
      $bindings['from_ecd'] = $fromDate;
      $bindings['to_ecd'] = $toDate;
      $bindings['from_dis'] = $fromDate;
      $bindings['to_dis'] = $toDate;
      $bindings['from_bs'] = $fromDate;
      $bindings['to_bs'] = $toDate;
      $bindings['from_chem'] = $fromDate;
      $bindings['to_chem'] = $toDate;
      $bindings['from_cs'] = $fromDate;
      $bindings['to_cs'] = $toDate;
    }

    // Add upazila binding if provided
    if ($upazilaId) {
      $bindings['upazila_id'] = $upazilaId;
    }

    $rows = DB::select($sql, $bindings);

    $sampleData = array_map(function ($r) {
      return [
        'name' => $r->name,
        'wp' => (int) $r->wp,
        'si' => (int) $r->si,
        'bf' => (int) $r->bf,
        'bd' => (int) $r->bd,
        'ec' => (int) $r->ec,
        'dis' => (int) $r->dis,

        'si_dtw' => (int) $r->si_dtw,
        'si_stw' => (int) $r->si_stw,
        'si_rwh' => (int) $r->si_rwh,
        'si_ro'  => (int) $r->si_ro,
        'si_pws' => (int) $r->si_pws,

        'bf_dtw' => (int) $r->bf_dtw,
        'bf_stw' => (int) $r->bf_stw,
        'bf_rwh' => (int) $r->bf_rwh,
        'bf_ro'  => (int) $r->bf_ro,
        'bf_pws' => (int) $r->bf_pws,

        'bs_s1' => (int) $r->bs_s1,
        'bs_s2' => (int) $r->bs_s2,
        'bs_fb' => (int) $r->bs_fb,
        'bs_fu' => (int) $r->bs_fu,

        'ecd_dtw' => (int) $r->ecd_dtw,
        'ecd_stw' => (int) $r->ecd_stw,
        'ecd_rwh' => (int) $r->ecd_rwh,
        'ecd_ro'  => (int) $r->ecd_ro,
        'ecd_pws' => (int) $r->ecd_pws,

        'dis_dtw' => (int) $r->dis_dtw,
        'dis_stw' => (int) $r->dis_stw,
        'dis_rwh' => (int) $r->dis_rwh,
        'dis_ro'  => (int) $r->dis_ro,
        'dis_pws' => (int) $r->dis_pws,

        'chem_dtw'  => (int) $r->chem_dtw,
        'chem_stw'  => (int) $r->chem_stw,
        'chem_rwhf' => (int) $r->chem_rwhf,
        'chem_pws'  => (int) $r->chem_pws,

        'cs_s1' => (int) $r->cs_s1,
        'cs_s2' => (int) $r->cs_s2,
        'cs_fb' => (int) $r->cs_fb,
        'cs_fu' => (int) $r->cs_fu,
      ];
    }, $rows);

    $numericKeys = array_keys(isset($sampleData[0]) ? $sampleData[0] : []);
    $numericKeys = array_filter($numericKeys, function ($k) { return $k !== 'name'; });
    $numericKeys = array_values($numericKeys);

    $totals = array_fill_keys($numericKeys, 0);
    foreach ($sampleData as $row) {
      foreach ($numericKeys as $k) {
        $totals[$k] += (int) $row[$k];
      }
    }

    $districts = District::whereIn('id', [6, 7])->get();

    //$statusRows = $this->buildQuarterStatusRows($year, $distid);
    $statusRows = $this->buildLastNQuarterStatusRows($distid, 5);

    // Build report period string based on filter dates
    $reportPeriod = '';
    if ($fromDate && $toDate) {
      $reportPeriod = date('d M Y', strtotime($fromDate)) . ' - ' . date('d M Y', strtotime($toDate));
    } elseif ($toDate) {
      $reportPeriod = 'Up to ' . date('d M Y', strtotime($toDate));
    }

    return view('core.safe_pani_admin.basic_data_entry.reports.weeklyWqAndSiReport',
      compact('districts', 'distid', 'sampleData', 'totals', 'statusRows', 'fromDate', 'toDate', 'reportPeriod')
    );
  }



  private function combinedSql($fromDate = null, $toDate = null, $upazilaId = null): string
  {
    // Build date condition for sanitary inspection
    $siDateCondition = ($fromDate && $toDate) ? "AND DATE(si2.inspection_date) BETWEEN :from_si AND :to_si" : "";
    $siTechDateCondition = ($fromDate && $toDate) ? "AND DATE(si2.inspection_date) BETWEEN :from_si_tech AND :to_si_tech" : "";

    // Build date condition for sample collection
    $scDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_sc AND :to_sc" : "";
    $bfDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_bf AND :to_bf" : "";
    $ecdDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_ecd AND :to_ecd" : "";
    $disDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_dis AND :to_dis" : "";
    $bsDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_bs AND :to_bs" : "";
    $chemDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_chem AND :to_chem" : "";
    $csDateCondition = ($fromDate && $toDate) ? "AND DATE(sc.sample_date) BETWEEN :from_cs AND :to_cs" : "";

    // Build upazila condition
    $upazilaCondition = $upazilaId ? "AND u.id = :upazila_id" : "";

    return "
WITH
base_upazila AS (
  SELECT u.id AS upid, u.upname
  FROM fupazila u
  JOIN sp_school s ON s.upid = u.id
  JOIN sp_infrastructure i ON i.school_id = s.id AND i.is_active = 1
  WHERE s.distid = :d1 {$upazilaCondition}
  GROUP BY u.id, u.upname
),
wp AS (
  SELECT s.upid, COUNT(DISTINCT i.water_id) AS wp
  FROM sp_infrastructure i
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d2 AND i.is_active = 1
  GROUP BY s.upid
),
si AS (
  SELECT s.upid, COUNT(si2.id) AS si
  FROM sp_san_inspection_v2 si2
  JOIN sp_infrastructure i ON i.id = si2.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d3 {$siDateCondition}
  GROUP BY s.upid
),
si_tech AS (
  SELECT s.upid,
    SUM(CASE WHEN i.tech_type='DTW' THEN 1 ELSE 0 END) AS si_dtw,
    SUM(CASE WHEN i.tech_type='STW' THEN 1 ELSE 0 END) AS si_stw,
    SUM(CASE WHEN i.tech_type='RWH' THEN 1 ELSE 0 END) AS si_rwh,
    SUM(CASE WHEN i.tech_type='RO'  THEN 1 ELSE 0 END) AS si_ro,
    SUM(CASE WHEN i.tech_type='PWS' THEN 1 ELSE 0 END) AS si_pws
  FROM sp_san_inspection_v2 si2
  JOIN sp_infrastructure i ON i.id = si2.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d4 {$siTechDateCondition}
  GROUP BY s.upid
),
sampling AS (
  SELECT s.upid,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END) AS bf,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END) AS bd,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS ec,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS dis
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d5 {$scDateCondition}
  GROUP BY s.upid
),
bf_tech AS (
  SELECT s.upid,
    SUM(CASE WHEN i.tech_type='DTW' AND sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END) AS bf_dtw,
    SUM(CASE WHEN i.tech_type='STW' AND sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END) AS bf_stw,
    SUM(CASE WHEN i.tech_type='RWH' AND sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END) AS bf_rwh,
    SUM(CASE WHEN i.tech_type='RO'  AND sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END) AS bf_ro,
    SUM(CASE WHEN i.tech_type='PWS' AND sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END) AS bf_pws
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d6 {$bfDateCondition}
  GROUP BY s.upid
),
ecd_tech AS (
  SELECT s.upid,
    SUM(CASE WHEN i.tech_type='DTW' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END) AS ecd_dtw,
    SUM(CASE WHEN i.tech_type='STW' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END) AS ecd_stw,
    SUM(CASE WHEN i.tech_type='RWH' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END) AS ecd_rwh,
    SUM(CASE WHEN i.tech_type='RO'  AND sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END) AS ecd_ro,
    SUM(CASE WHEN i.tech_type='PWS' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END) AS ecd_pws
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d7 {$ecdDateCondition}
  GROUP BY s.upid
),
dis_tech AS (
  SELECT s.upid,
    SUM(CASE WHEN i.tech_type='DTW' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS dis_dtw,
    SUM(CASE WHEN i.tech_type='STW' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS dis_stw,
    SUM(CASE WHEN i.tech_type='RWH' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS dis_rwh,
    SUM(CASE WHEN i.tech_type='RO'  AND sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS dis_ro,
    SUM(CASE WHEN i.tech_type='PWS' AND sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END) AS dis_pws
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d8 {$disDateCondition}
  GROUP BY s.upid
),
bs_no AS (
  SELECT s.upid,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.sample_no='Sample 1' THEN 1 ELSE 0 END) AS bs_s1,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.sample_no='Sample 2' THEN 1 ELSE 0 END) AS bs_s2,
    SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.sample_no='FB' THEN 1 ELSE 0 END) AS bs_fb,
    SUM(CASE WHEN sc.sample_cat='Follow-up Bact.' THEN 1 ELSE 0 END) AS bs_fu
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d9 {$bsDateCondition}
  GROUP BY s.upid
),
chem_tech AS (
  SELECT s.upid,
    SUM(CASE WHEN i.tech_type='DTW' AND sc.sample_cat='Chemical' THEN 1 ELSE 0 END) AS chem_dtw,
    SUM(CASE WHEN i.tech_type='STW' AND sc.sample_cat='Chemical' THEN 1 ELSE 0 END) AS chem_stw,
    SUM(CASE WHEN i.tech_type='RWH' AND sc.sample_cat='Chemical' THEN 1 ELSE 0 END) AS chem_rwhf,
    SUM(CASE WHEN i.tech_type='PWS' AND sc.sample_cat='Chemical' THEN 1 ELSE 0 END) AS chem_pws
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d10 {$chemDateCondition}
  GROUP BY s.upid
),
cs_no AS (
  SELECT s.upid,
    SUM(CASE WHEN sc.sample_cat='Chemical' AND sc.sample_no='Sample 1' THEN 1 ELSE 0 END) AS cs_s1,
    SUM(CASE WHEN sc.sample_cat='Chemical' AND sc.sample_no='Sample 2' THEN 1 ELSE 0 END) AS cs_s2,
    SUM(CASE WHEN sc.sample_cat='Chemical' AND sc.sample_no='FB' THEN 1 ELSE 0 END) AS cs_fb,
    SUM(CASE WHEN sc.sample_cat='Follow-up Chem.' THEN 1 ELSE 0 END) AS cs_fu
  FROM sp_sample_collection sc
  JOIN sp_infrastructure i ON i.id = sc.infrastructure_id AND i.is_active = 1
  JOIN sp_school s ON s.id = i.school_id
  WHERE s.distid = :d11 {$csDateCondition}
  GROUP BY s.upid
)
SELECT
  b.upname AS name,
  COALESCE(w.wp, 0) AS wp,
  COALESCE(si2.si, 0) AS si,
  COALESCE(sa.bf, 0) AS bf,
  COALESCE(sa.bd, 0) AS bd,
  COALESCE(sa.ec, 0) AS ec,
  COALESCE(sa.dis,0) AS dis,
  COALESCE(sit.si_dtw,0) AS si_dtw,
  COALESCE(sit.si_stw,0) AS si_stw,
  COALESCE(sit.si_rwh,0) AS si_rwh,
  COALESCE(sit.si_ro, 0) AS si_ro,
  COALESCE(sit.si_pws,0) AS si_pws,
  COALESCE(bft.bf_dtw,0) AS bf_dtw,
  COALESCE(bft.bf_stw,0) AS bf_stw,
  COALESCE(bft.bf_rwh,0) AS bf_rwh,
  COALESCE(bft.bf_ro, 0) AS bf_ro,
  COALESCE(bft.bf_pws,0) AS bf_pws,
  COALESCE(bsn.bs_s1,0) AS bs_s1,
  COALESCE(bsn.bs_s2,0) AS bs_s2,
  COALESCE(bsn.bs_fb,0) AS bs_fb,
  COALESCE(bsn.bs_fu,0) AS bs_fu,
  COALESCE(ecd.ecd_dtw,0) AS ecd_dtw,
  COALESCE(ecd.ecd_stw,0) AS ecd_stw,
  COALESCE(ecd.ecd_rwh,0) AS ecd_rwh,
  COALESCE(ecd.ecd_ro, 0) AS ecd_ro,
  COALESCE(ecd.ecd_pws,0) AS ecd_pws,
  COALESCE(dst.dis_dtw,0) AS dis_dtw,
  COALESCE(dst.dis_stw,0) AS dis_stw,
  COALESCE(dst.dis_rwh,0) AS dis_rwh,
  COALESCE(dst.dis_ro, 0) AS dis_ro,
  COALESCE(dst.dis_pws,0) AS dis_pws,
  COALESCE(ct.chem_dtw,0)  AS chem_dtw,
  COALESCE(ct.chem_stw,0)  AS chem_stw,
  COALESCE(ct.chem_rwhf,0) AS chem_rwhf,
  COALESCE(ct.chem_pws,0)  AS chem_pws,
  COALESCE(csn.cs_s1,0) AS cs_s1,
  COALESCE(csn.cs_s2,0) AS cs_s2,
  COALESCE(csn.cs_fb,0) AS cs_fb,
  COALESCE(csn.cs_fu,0) AS cs_fu
FROM base_upazila b
LEFT JOIN wp w ON w.upid = b.upid
LEFT JOIN si si2 ON si2.upid = b.upid
LEFT JOIN si_tech sit ON sit.upid = b.upid
LEFT JOIN sampling sa ON sa.upid = b.upid
LEFT JOIN bf_tech bft ON bft.upid = b.upid
LEFT JOIN bs_no bsn ON bsn.upid = b.upid
LEFT JOIN ecd_tech ecd ON ecd.upid = b.upid
LEFT JOIN dis_tech dst ON dst.upid = b.upid
LEFT JOIN chem_tech ct ON ct.upid = b.upid
LEFT JOIN cs_no csn ON csn.upid = b.upid
ORDER BY b.upname
";
  }



  private function buildQuarterStatusRows($year, $distid)
  {
    $quarters = [
      1 => ['from' => "$year-01-01", 'to' => "$year-03-31"],
      2 => ['from' => "$year-04-01", 'to' => "$year-06-30"],
      3 => ['from' => "$year-07-01", 'to' => "$year-09-30"],
      4 => ['from' => "$year-10-01", 'to' => "$year-12-31"],
    ];

    $statusRows = [];

    foreach ($quarters as $q => $range) {
      $row = $this->quarterTotals($distid, $range['from'], $range['to']);

      $label = "Status of Q{$q} {$year}";
      $statusRows[$label] = $row;
    }

    return $statusRows;
  }

  private function buildLastNQuarterStatusRows($distid, $n = 5, $baseDate = null)
  {
    $statusRows = [];

    for ($i = 0; $i < $n; $i++) {
      $q = $this->quarterRangeByOffset($i, $baseDate); // 0=current, 1=prev, ...

      $statusRows[$q['label']] = $this->quarterTotals(
        $distid,
        $q['from'],
        $q['to']
      );
    }

    return $statusRows;
  }


  private function quarterRangeByOffset($offsetQuarters = 0, $baseDate = null)
  {
    $base = $baseDate ? Carbon::parse($baseDate) : Carbon::now();
    $d = $base->copy()->subMonths($offsetQuarters * 3);

    $year = (int) $d->year;
    $quarter = (int) ceil($d->month / 3);

    $startMonth = (($quarter - 1) * 3) + 1;

    $from = Carbon::create($year, $startMonth, 1)->startOfDay();
    $to   = Carbon::create($year, $startMonth, 1)->addMonths(2)->endOfMonth()->endOfDay();

    // Label logic
    if ($offsetQuarters === 0) {
      $label = "Status of Q{$quarter} {$year} (Running)";
    } else {
      $label = "Status of Q{$quarter} {$year}";
    }

    return [
      'label'   => $label,
      'from'    => $from->toDateString(),
      'to'      => $to->toDateString(),
      'quarter' => $quarter,
      'year'    => $year,
    ];
  }






  private function quarterTotals($distid, $fromDate, $toDate)
  {
    $sql = "
    SELECT
      0 AS wp,

      /* Sanitary Inspection (SI) totals + tech */
      COALESCE(SUM(CASE WHEN si2.id IS NOT NULL THEN 1 ELSE 0 END), 0) AS si,

      COALESCE(SUM(CASE WHEN si2.id IS NOT NULL AND i.tech_type='DTW' THEN 1 ELSE 0 END), 0) AS si_dtw,
      COALESCE(SUM(CASE WHEN si2.id IS NOT NULL AND i.tech_type='STW' THEN 1 ELSE 0 END), 0) AS si_stw,
      COALESCE(SUM(CASE WHEN si2.id IS NOT NULL AND i.tech_type='RWH' THEN 1 ELSE 0 END), 0) AS si_rwh,
      COALESCE(SUM(CASE WHEN si2.id IS NOT NULL AND i.tech_type='RO'  THEN 1 ELSE 0 END), 0) AS si_ro,
      COALESCE(SUM(CASE WHEN si2.id IS NOT NULL AND i.tech_type='PWS' THEN 1 ELSE 0 END), 0) AS si_pws,

      /* Bacteriological sampling */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' THEN 1 ELSE 0 END), 0) AS bf,

      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND i.tech_type='DTW' THEN 1 ELSE 0 END), 0) AS bf_dtw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND i.tech_type='STW' THEN 1 ELSE 0 END), 0) AS bf_stw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND i.tech_type='RWH' THEN 1 ELSE 0 END), 0) AS bf_rwh,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND i.tech_type='RO'  THEN 1 ELSE 0 END), 0) AS bf_ro,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND i.tech_type='PWS' THEN 1 ELSE 0 END), 0) AS bf_pws,

      /* Detected/disinfect totals (your sheet mapping) */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 THEN 1 ELSE 0 END), 0) AS bd,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END), 0) AS ec,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 THEN 1 ELSE 0 END), 0) AS dis,

      /* ECD by tech (detected) */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 AND i.tech_type='DTW' THEN 1 ELSE 0 END), 0) AS ecd_dtw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 AND i.tech_type='STW' THEN 1 ELSE 0 END), 0) AS ecd_stw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 AND i.tech_type='RWH' THEN 1 ELSE 0 END), 0) AS ecd_rwh,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 AND i.tech_type='RO'  THEN 1 ELSE 0 END), 0) AS ecd_ro,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status > 0 AND i.tech_type='PWS' THEN 1 ELSE 0 END), 0) AS ecd_pws,

      /* DIS by tech (disinfect done) */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 AND i.tech_type='DTW' THEN 1 ELSE 0 END), 0) AS dis_dtw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 AND i.tech_type='STW' THEN 1 ELSE 0 END), 0) AS dis_stw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 AND i.tech_type='RWH' THEN 1 ELSE 0 END), 0) AS dis_rwh,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 AND i.tech_type='RO'  THEN 1 ELSE 0 END), 0) AS dis_ro,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.disinfect_status = 2 AND i.tech_type='PWS' THEN 1 ELSE 0 END), 0) AS dis_pws,

      /* Bacteriological sample_no breakdown */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.sample_no='Sample 1' THEN 1 ELSE 0 END), 0) AS bs_s1,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.sample_no='Sample 2' THEN 1 ELSE 0 END), 0) AS bs_s2,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Bacteriological' AND sc.sample_no='FB'       THEN 1 ELSE 0 END), 0) AS bs_fb,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Follow-up Bact.' THEN 1 ELSE 0 END), 0) AS bs_fu,

      /* Chemical by tech */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND i.tech_type='DTW' THEN 1 ELSE 0 END), 0) AS chem_dtw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND i.tech_type='STW' THEN 1 ELSE 0 END), 0) AS chem_stw,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND i.tech_type='RWH' THEN 1 ELSE 0 END), 0) AS chem_rwhf,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND i.tech_type='PWS' THEN 1 ELSE 0 END), 0) AS chem_pws,

      /* Chemical sample_no breakdown */
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND sc.sample_no='Sample 1' THEN 1 ELSE 0 END), 0) AS cs_s1,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND sc.sample_no='Sample 2' THEN 1 ELSE 0 END), 0) AS cs_s2,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Chemical' AND sc.sample_no='FB'       THEN 1 ELSE 0 END), 0) AS cs_fb,
      COALESCE(SUM(CASE WHEN sc.sample_cat='Follow-up Chem.' THEN 1 ELSE 0 END), 0) AS cs_fu

    FROM sp_infrastructure i
    JOIN sp_school s ON s.id = i.school_id
    LEFT JOIN sp_san_inspection_v2 si2
      ON si2.infrastructure_id = i.id
     AND DATE(si2.inspection_date) BETWEEN :from1 AND :to1

    LEFT JOIN sp_sample_collection sc
      ON sc.infrastructure_id = i.id
     AND DATE(sc.sample_date) BETWEEN :from2 AND :to2

    WHERE s.distid = :dist
      AND i.is_active = 1
    ";

    $res = DB::select($sql, [
      'from1' => $fromDate, 'to1' => $toDate,
      'from2' => $fromDate, 'to2' => $toDate,
      'dist'  => (int)$distid,
    ]);

    $r = isset($res[0]) ? $res[0] : null;

    // Return array exactly like your $statusRows inner array
    return [
      'wp' => (int) ($r ? $r->wp : 0),

      'si' => (int) ($r ? $r->si : 0),
      'bf' => (int) ($r ? $r->bf : 0),
      'bd' => (int) ($r ? $r->bd : 0),
      'ec' => (int) ($r ? $r->ec : 0),
      'dis'=> (int) ($r ? $r->dis: 0),

      'si_dtw' => (int) ($r ? $r->si_dtw : 0),
      'si_stw' => (int) ($r ? $r->si_stw : 0),
      'si_rwh' => (int) ($r ? $r->si_rwh : 0),
      'si_ro'  => (int) ($r ? $r->si_ro  : 0),
      'si_pws' => (int) ($r ? $r->si_pws : 0),

      'bf_dtw' => (int) ($r ? $r->bf_dtw : 0),
      'bf_stw' => (int) ($r ? $r->bf_stw : 0),
      'bf_rwh' => (int) ($r ? $r->bf_rwh : 0),
      'bf_ro'  => (int) ($r ? $r->bf_ro  : 0),
      'bf_pws' => (int) ($r ? $r->bf_pws : 0),

      'bs_s1' => (int) ($r ? $r->bs_s1 : 0),
      'bs_s2' => (int) ($r ? $r->bs_s2 : 0),
      'bs_fb' => (int) ($r ? $r->bs_fb : 0),
      'bs_fu' => (int) ($r ? $r->bs_fu : 0),

      'ecd_dtw' => (int) ($r ? $r->ecd_dtw : 0),
      'ecd_stw' => (int) ($r ? $r->ecd_stw : 0),
      'ecd_rwh' => (int) ($r ? $r->ecd_rwh : 0),
      'ecd_ro'  => (int) ($r ? $r->ecd_ro  : 0),
      'ecd_pws' => (int) ($r ? $r->ecd_pws : 0),

      'dis_dtw' => (int) ($r ? $r->dis_dtw : 0),
      'dis_stw' => (int) ($r ? $r->dis_stw : 0),
      'dis_rwh' => (int) ($r ? $r->dis_rwh : 0),
      'dis_ro'  => (int) ($r ? $r->dis_ro  : 0),
      'dis_pws' => (int) ($r ? $r->dis_pws : 0),

      'chem_dtw'  => (int) ($r ? $r->chem_dtw  : 0),
      'chem_stw'  => (int) ($r ? $r->chem_stw  : 0),
      'chem_rwhf' => (int) ($r ? $r->chem_rwhf : 0),
      'chem_pws'  => (int) ($r ? $r->chem_pws  : 0),

      'cs_s1' => (int) ($r ? $r->cs_s1 : 0),
      'cs_s2' => (int) ($r ? $r->cs_s2 : 0),
      'cs_fb' => (int) ($r ? $r->cs_fb : 0),
      'cs_fu' => (int) ($r ? $r->cs_fu : 0),
    ];
  }





  public function getUpazilasByDistrict(Request $request)
  {
    $districtId = $request->get('district_id');

    if (!$districtId) {
      return response()->json([]);
    }

    $upazilas = Upazila::where('disid', $districtId)->get(['id', 'upname']);

    return response()->json($upazilas);
  }

  public function weeklyWqAndSiReportExport(Request $request)
  {
    $distid = (int) ($request->get('district_id', 6));
    $upazilaId = $request->get('upazila_id');
    $fromDate = $request->get('from_date');
    $toDate = $request->get('to_date', date('Y-m-d'));

    $sql = $this->combinedSql($fromDate, $toDate, $upazilaId);

    // Build bindings
    $bindings = [
      'd1'  => $distid,
      'd2'  => $distid,
      'd3'  => $distid,
      'd4'  => $distid,
      'd5'  => $distid,
      'd6'  => $distid,
      'd7'  => $distid,
      'd8'  => $distid,
      'd9'  => $distid,
      'd10' => $distid,
      'd11' => $distid,
    ];

    // Add date bindings if dates are provided
    if ($fromDate && $toDate) {
      $bindings['from_si'] = $fromDate;
      $bindings['to_si'] = $toDate;
      $bindings['from_sc'] = $fromDate;
      $bindings['to_sc'] = $toDate;
      $bindings['from_si_tech'] = $fromDate;
      $bindings['to_si_tech'] = $toDate;
      $bindings['from_bf'] = $fromDate;
      $bindings['to_bf'] = $toDate;
      $bindings['from_ecd'] = $fromDate;
      $bindings['to_ecd'] = $toDate;
      $bindings['from_dis'] = $fromDate;
      $bindings['to_dis'] = $toDate;
      $bindings['from_bs'] = $fromDate;
      $bindings['to_bs'] = $toDate;
      $bindings['from_chem'] = $fromDate;
      $bindings['to_chem'] = $toDate;
      $bindings['from_cs'] = $fromDate;
      $bindings['to_cs'] = $toDate;
    }

    // Add upazila binding if provided
    if ($upazilaId) {
      $bindings['upazila_id'] = $upazilaId;
    }

    $rows = DB::select($sql, $bindings);

    // Build sample data array
    $sampleData = array_map(function ($r) {
      return [
        'name' => $r->name,
        'wp' => (int) $r->wp,
        'si' => (int) $r->si,
        'bf' => (int) $r->bf,
        'bd' => (int) $r->bd,
        'ec' => (int) $r->ec,
        'dis' => (int) $r->dis,
        'si_dtw' => (int) $r->si_dtw,
        'si_stw' => (int) $r->si_stw,
        'si_rwh' => (int) $r->si_rwh,
        'si_ro'  => (int) $r->si_ro,
        'si_pws' => (int) $r->si_pws,
        'bf_dtw' => (int) $r->bf_dtw,
        'bf_stw' => (int) $r->bf_stw,
        'bf_rwh' => (int) $r->bf_rwh,
        'bf_ro'  => (int) $r->bf_ro,
        'bf_pws' => (int) $r->bf_pws,
        'bs_s1' => (int) $r->bs_s1,
        'bs_s2' => (int) $r->bs_s2,
        'bs_fb' => (int) $r->bs_fb,
        'bs_fu' => (int) $r->bs_fu,
        'ecd_dtw' => (int) $r->ecd_dtw,
        'ecd_stw' => (int) $r->ecd_stw,
        'ecd_rwh' => (int) $r->ecd_rwh,
        'ecd_ro'  => (int) $r->ecd_ro,
        'ecd_pws' => (int) $r->ecd_pws,
        'dis_dtw' => (int) $r->dis_dtw,
        'dis_stw' => (int) $r->dis_stw,
        'dis_rwh' => (int) $r->dis_rwh,
        'dis_ro'  => (int) $r->dis_ro,
        'dis_pws' => (int) $r->dis_pws,
        'chem_dtw'  => (int) $r->chem_dtw,
        'chem_stw'  => (int) $r->chem_stw,
        'chem_rwhf' => (int) $r->chem_rwhf,
        'chem_pws'  => (int) $r->chem_pws,
        'cs_s1' => (int) $r->cs_s1,
        'cs_s2' => (int) $r->cs_s2,
        'cs_fb' => (int) $r->cs_fb,
        'cs_fu' => (int) $r->cs_fu,
      ];
    }, $rows);

    // Calculate totals
    $numericKeys = array_keys(isset($sampleData[0]) ? $sampleData[0] : []);
    $numericKeys = array_filter($numericKeys, function ($k) { return $k !== 'name'; });
    $numericKeys = array_values($numericKeys);

    $totals = array_fill_keys($numericKeys, 0);
    foreach ($sampleData as $row) {
      foreach ($numericKeys as $k) {
        $totals[$k] += (int) $row[$k];
      }
    }

    // Get status rows
    $statusRows = $this->buildLastNQuarterStatusRows($distid, 5);

    // Build report period string
    $reportPeriod = '';
    if ($fromDate && $toDate) {
      $reportPeriod = date('d M Y', strtotime($fromDate)) . ' - ' . date('d M Y', strtotime($toDate));
    } elseif ($toDate) {
      $reportPeriod = 'Up to ' . date('d M Y', strtotime($toDate));
    }

    // Generate filename
    $dateStr = $fromDate && $toDate ? $fromDate . '_to_' . $toDate : 'cumulative';
    $filename = 'Weekly_WQ_SI_Report_' . $dateStr . '_' . date('Y-m-d_His') . '.xlsx';

    // Create Excel export
    return Excel::download(
      new WeeklyWqSiReportExport($sampleData, $totals, $statusRows, $reportPeriod),
      $filename,
      ExcelWriter::XLSX
    );
  }

  public function weeklyWqAndSiReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.weeklyWqAndSiReportPrint');
  }

  public function rbcOpFinReport(Request $request)
  {
    $year = 2025;
    $quarter = 4;
    $country = 'Bangladesh';
    $org = 'HYSAWA';

    $districts = [
      6 => 'Khulna',
      7 => 'Satkhira',
      8 => 'Bagerhat',
    ];

    /**
     * 1) Get technology counts for ALL selected districts in ONE query
     */
    $techRows = DB::table('sp_infrastructure')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_school.distid',
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 1 ELSE 0 END) AS tubewell"),
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type = 'RWH' THEN 1 ELSE 0 END) AS rwh"),
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type = 'RO'  THEN 1 ELSE 0 END) AS ro"),
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type = 'PWS' THEN 1 ELSE 0 END) AS pwp")
      )
      ->whereIn('sp_school.distid', array_keys($districts))
      ->where('sp_infrastructure.is_active', 1)
      ->groupBy('sp_school.distid')
      ->get();

    /**
     * Convert to array map: distid => counts
     */
    $techCountsByDist = [];
    foreach ($techRows as $r) {
      $techCountsByDist[(int)$r->distid] = [
        'tubewell' => (int)$r->tubewell,
        'rwh' => (int)$r->rwh,
        'ro' => (int)$r->ro,
        'pwp' => (int)$r->pwp,
      ];
    }

    /**
     * If you already have these arrays computed elsewhere, keep them.
     * Otherwise keep them empty defaults to avoid undefined variable error.
     */
    $schoolDaysByDist = isset($schoolDaysByDist) ? $schoolDaysByDist : [];
    $clinicDaysByDist = isset($clinicDaysByDist) ? $clinicDaysByDist : [];

    /**
     * 2) Build report data
     */
    $reportData = [];

    foreach ($districts as $distId => $unitName) {

      $tech = isset($techCountsByDist[$distId])
        ? $techCountsByDist[$distId]
        : ['tubewell' => 0, 'rwh' => 0, 'ro' => 0, 'pwp' => 0];

      $reportData[] = [
        'org' => $org,
        'unit' => $unitName,
        'country' => $country,
        'quarter' => $quarter,
        'year' => $year,

        'school_days' => (int)(isset($schoolDaysByDist[$distId]) ? $schoolDaysByDist[$distId] : 0),
        'clinic_days' => (int)(isset($clinicDaysByDist[$distId]) ? $clinicDaysByDist[$distId] : 0),

        'tubewells' => (int)$tech['tubewell'],
        'rwh' => (int)$tech['rwh'],
        'ro' => (int)$tech['ro'],
        'pwp' => (int)$tech['pwp'],

        'volume' => '',
        'vol_method' => '',
        'vol_notes' => '',
        'pop_served' => '',
        'pop_margin' => '',

        'local_revenue' => '',
        'direct_cost' => '',
        'indirect_cost' => '',
        'excluded_cost' => '',
        'excluded_revenue' => '',

        'currency_notes' => '',
        'rev_notes' => '',
        'cost_notes' => '',
      ];
    }

    //dd($reportData);


    return view('core.safe_pani_admin.basic_data_entry.reports.rbcOpFinReport', compact('reportData'));
  }

  public function rbcOpFinReportExport(Request $request)
  {
    $year = 2025;
    $quarter = 4;
    $country = 'Bangladesh';
    $org = 'HYSAWA';

    $districts = [
      6 => 'Khulna',
      7 => 'Satkhira',
      8 => 'Bagerhat',
    ];

    // Get technology counts for ALL selected districts in ONE query
    $techRows = DB::table('sp_infrastructure')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_school.distid',
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 1 ELSE 0 END) AS tubewell"),
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type = 'RWH' THEN 1 ELSE 0 END) AS rwh"),
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type = 'RO'  THEN 1 ELSE 0 END) AS ro"),
        DB::raw("SUM(CASE WHEN sp_infrastructure.tech_type = 'PWS' THEN 1 ELSE 0 END) AS pwp")
      )
      ->whereIn('sp_school.distid', array_keys($districts))
      ->where('sp_infrastructure.is_active', 1)
      ->groupBy('sp_school.distid')
      ->get();

    // Convert to array map: distid => counts
    $techCountsByDist = [];
    foreach ($techRows as $r) {
      $techCountsByDist[(int)$r->distid] = [
        'tubewell' => (int)$r->tubewell,
        'rwh' => (int)$r->rwh,
        'ro' => (int)$r->ro,
        'pwp' => (int)$r->pwp,
      ];
    }

    $schoolDaysByDist = isset($schoolDaysByDist) ? $schoolDaysByDist : [];
    $clinicDaysByDist = isset($clinicDaysByDist) ? $clinicDaysByDist : [];

    // Build report data
    $reportData = [];

    foreach ($districts as $distId => $unitName) {

      $tech = isset($techCountsByDist[$distId])
        ? $techCountsByDist[$distId]
        : ['tubewell' => 0, 'rwh' => 0, 'ro' => 0, 'pwp' => 0];

      $reportData[] = [
        $org,
        $unitName,
        $country,
        $quarter,
        $year,
        (int)(isset($schoolDaysByDist[$distId]) ? $schoolDaysByDist[$distId] : 0),
        (int)(isset($clinicDaysByDist[$distId]) ? $clinicDaysByDist[$distId] : 0),
        (int)$tech['tubewell'],
        (int)$tech['rwh'],
        (int)$tech['ro'],
        (int)$tech['pwp'],
        '', // volume
        '', // vol_method
        '', // vol_notes
        '', // pop_served
        '', // pop_margin
        '', // local_revenue
        '', // direct_cost
        '', // indirect_cost
        '', // excluded_cost
        '', // excluded_revenue
        '', // currency_notes
        '', // rev_notes
        '', // cost_notes
      ];
    }

    // CSV Headers
    $headers = [
      'Organization',
      'Operational Unit',
      'Country',
      'Quarter',
      'Year',
      'Number of school days this quarter',
      'Number of community clinic operational days this quarter',
      'Number of Tubewells',
      'Number of Rainwater Harvesting Systems',
      'Number of Reverse Osmosis Systems',
      'Number of Piped Water Points',
      'Volume Produced (m3)',
      'Volume Measurement Method',
      'Notes on Volume (Text)',
      'Estimated Population Served',
      'Estimated Population Margin of Error (%)',
      'Local Revenue (USD)',
      'Direct Cost (USD)',
      'Indirect Cost (USD)',
      'Excluded Cost (USD)',
      'Excluded Revenue (USD)',
      'Currency Notes (text)',
      'Revenue Notes (text)',
      'Cost Notes (text)',
    ];

    $filename = 'RBC_OpFin_Report_Q' . $quarter . '_' . $year . '_' . date('Y-m-d') . '.csv';

    // Create CSV response using Laravel's StreamedResponse
    $callback = function () use ($headers, $reportData) {
      $file = fopen('php://output', 'w');

      // Add BOM for Excel UTF-8 compatibility
      fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Write headers
      fputcsv($file, $headers);

      // Write data rows
      foreach ($reportData as $row) {
        fputcsv($file, $row);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    ]);
  }

  public function rbcOpFinReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcOpFinReportPrint');
  }

  public function rbcWaterpointsReport(Request $request)
  {
    $rows = DB::table('sp_infrastructure')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_infrastructure.water_id',
        'sp_school.lat',
        'sp_school.lon',
        'sp_infrastructure.tech_type',
        DB::raw("
            CASE
              WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 'Tubewell'
              WHEN sp_infrastructure.tech_type = 'RWH' THEN 'Rainwater harvesting system'
              WHEN sp_infrastructure.tech_type = 'RO'  THEN 'Reverse osmosis system'
              WHEN sp_infrastructure.tech_type = 'PWS' THEN 'Piped water system'
              ELSE sp_infrastructure.tech_type
            END AS technology_name
        "),
        DB::raw("
            CASE
              WHEN sp_school.sch_type_edu = 'Healthcare Facility' THEN 'Community clinic'
              ELSE 'School'
            END AS institution_type
        "),
        DB::raw("
            CASE
              WHEN sp_school.sch_type_edu = 'Healthcare Facility'
                THEN COALESCE(sp_school.tot_staff, 0)
              ELSE COALESCE(sp_school.tot_student, 0) + COALESCE(sp_school.tot_staff, 0)
            END AS beneficiary_count
        ")
      )
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->get();

    $waterpoints = $rows->map(function ($r) {

      return [
        'wp_id' => (string)$r->water_id,
        'mwater_id' => '',

        'lat' => (string)$r->lat,
        'lon' => (string)$r->lon,

        // community clinic / school
        'institution' => strtolower($r->institution_type),

        // tubewell / rainwater harvesting system / etc
        'wp_type' => strtolower($r->technology_name),

        'volume' => '',
        'volume_method' => '',

        // beneficiary count
        'population' => (int)$r->beneficiary_count,
      ];

    })->values()->toArray();

    return view('core.safe_pani_admin.basic_data_entry.reports.rbcWaterpointsReport', compact('waterpoints'));
  }

  public function rbcWaterpointsReportExport(Request $request)
  {
    $rows = DB::table('sp_infrastructure')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_infrastructure.water_id',
        'sp_school.lat',
        'sp_school.lon',
        'sp_infrastructure.tech_type',
        DB::raw("
            CASE
              WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 'Tubewell'
              WHEN sp_infrastructure.tech_type = 'RWH' THEN 'Rainwater harvesting system'
              WHEN sp_infrastructure.tech_type = 'RO'  THEN 'Reverse osmosis system'
              WHEN sp_infrastructure.tech_type = 'PWS' THEN 'Piped water system'
              ELSE sp_infrastructure.tech_type
            END AS technology_name
        "),
        DB::raw("
            CASE
              WHEN sp_school.sch_type_edu = 'Healthcare Facility' THEN 'Community clinic'
              ELSE 'School'
            END AS institution_type
        "),
        DB::raw("
            CASE
              WHEN sp_school.sch_type_edu = 'Healthcare Facility'
                THEN COALESCE(sp_school.tot_staff, 0)
              ELSE COALESCE(sp_school.tot_student, 0) + COALESCE(sp_school.tot_staff, 0)
            END AS beneficiary_count
        ")
      )
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->get();

    $reportData = $rows->map(function ($r) {
      return [
        (string)$r->water_id,
        '', // mwater_id
        (string)$r->lat,
        (string)$r->lon,
        strtolower($r->institution_type),
        strtolower($r->technology_name),
        '', // volume
        '', // volume_method
        (int)$r->beneficiary_count,
      ];
    })->toArray();

    // CSV Headers
    $headers = [
      'wp_id',
      'mWater_id',
      'lat',
      'lon',
      'institution_type',
      'wp_type',
      'volume',
      'volume_method',
      'population',
    ];

    $filename = 'RBC_Waterpoints_Report_' . date('Y-m-d') . '.csv';

    // Create CSV response
    $callback = function () use ($headers, $reportData) {
      $file = fopen('php://output', 'w');

      // Add BOM for Excel UTF-8 compatibility
      fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Write headers
      fputcsv($file, $headers);

      // Write data rows
      foreach ($reportData as $row) {
        fputcsv($file, $row);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    ]);
  }

  public function rbcWaterpointsReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcWaterpointsReportPrint');
  }

  public function rbcServiceInterruptionsReport(Request $request)
  {
    $rows = DB::table('sp_om')
      ->join('sp_infrastructure', 'sp_om.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_om.water_id',
        'sp_om.notification_time',
        'sp_om.days_frac',
        'sp_om.problem_identification',
        DB::raw("
            CASE
              WHEN sp_om.problem_identification = 'Regular visit'
                THEN 'Planned service interruption'
              ELSE 'Unplanned service interruption'
            END AS service_interruption_type
        ")
      )
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->get();

    $interruptions = $rows->map(function ($r) {

      return [
        'wp_id' => (string)$r->water_id,

        // notification_time → start_date
        'start_date' => $r->notification_time
          ? date('Y/m/d', strtotime($r->notification_time))
          : '',

        // days_frac → duration (float or null)
        'duration' => is_null($r->days_frac)
          ? null
          : (float)$r->days_frac,

        // planned / unplanned mapping
        'type' => isset($r->service_interruption_type)
          ? (string)$r->service_interruption_type
          : '',
      ];

    })->values()->toArray();


    return view('core.safe_pani_admin.basic_data_entry.reports.rbcServiceInterruptionsReport', compact('interruptions'));
  }

  public function rbcServiceInterruptionsReportExport(Request $request)
  {
    $rows = DB::table('sp_om')
      ->join('sp_infrastructure', 'sp_om.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_om.water_id',
        'sp_om.notification_time',
        'sp_om.days_frac',
        'sp_om.problem_identification',
        DB::raw("
            CASE
              WHEN sp_om.problem_identification = 'Regular visit'
                THEN 'Planned service interruption'
              ELSE 'Unplanned service interruption'
            END AS service_interruption_type
        ")
      )
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->get();

    $reportData = $rows->map(function ($r) {
      return [
        (string)$r->water_id,
        $r->notification_time ? date('Y/m/d', strtotime($r->notification_time)) : '',
        is_null($r->days_frac) ? '' : (float)$r->days_frac,
        isset($r->service_interruption_type) ? (string)$r->service_interruption_type : '',
      ];
    })->toArray();

    // CSV Headers
    $headers = [
      'wp_id',
      'start_date',
      'duration',
      'type',
    ];

    $filename = 'RBC_Service_Interruptions_Report_' . date('Y-m-d') . '.csv';

    // Create CSV response
    $callback = function () use ($headers, $reportData) {
      $file = fopen('php://output', 'w');

      // Add BOM for Excel UTF-8 compatibility
      fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Write headers
      fputcsv($file, $headers);

      // Write data rows
      foreach ($reportData as $row) {
        fputcsv($file, $row);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    ]);
  }

  public function rbcServiceInterruptionsReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcServiceInterruptionsReportPrint');
  }

  public function rbcSanitaryInspectionReport(Request $request)
  {
    $rows = DB::table('sp_san_inspection_v2')
      ->join('sp_infrastructure', 'sp_san_inspection_v2.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_san_inspection_v2.water_id',
        'sp_san_inspection_v2.inspection_date',
        'sp_san_inspection_v2.sanitary_score',
        'sp_san_inspection_v2.accnt_score',
        'sp_infrastructure.tech_type',
        DB::raw("
            CASE
              WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 'Tubewell'
              WHEN sp_infrastructure.tech_type = 'RWH' THEN 'Rainwater harvesting system'
              WHEN sp_infrastructure.tech_type = 'RO'  THEN 'Reverse osmosis system'
              WHEN sp_infrastructure.tech_type = 'PWS' THEN 'Piped water system'
              ELSE sp_infrastructure.tech_type
            END AS technology_name
        ")
      )
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->get();

    $inspections = $rows->map(function ($r) {

      return [
        'wp_id' => (string)$r->water_id,
        'assessment' => strtolower($r->technology_name), // tubewell
        'date' => $r->inspection_date
          ? date('Y/m/d', strtotime($r->inspection_date))
          : '',
        'risk' => is_null($r->sanitary_score)
          ? 0
          : (int)$r->sanitary_score,

        // using inspection_date / notify logic as per your sample
        'national' => $r->inspection_date
          ? date('Y/m/d', strtotime($r->inspection_date))
          : '',
        'district' => $r->inspection_date
          ? date('Y/m/d', strtotime($r->inspection_date))
          : '',
        'management' => $r->inspection_date
          ? date('Y/m/d', strtotime($r->inspection_date))
          : '',

        // accountable derived from accnt_score (as in your dataset)
        'accountable' => is_null($r->accnt_score)
          ? 0
          : (int)$r->sanitary_score - (int)$r->accnt_score,
      ];

    })->values()->toArray();

    return view('core.safe_pani_admin.basic_data_entry.reports.rbcSanitaryInspectionReport', compact('inspections'));
  }

  public function rbcSanitaryInspectionReportExport(Request $request)
  {
    $rows = DB::table('sp_san_inspection_v2')
      ->join('sp_infrastructure', 'sp_san_inspection_v2.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_san_inspection_v2.water_id',
        'sp_san_inspection_v2.inspection_date',
        'sp_san_inspection_v2.sanitary_score',
        'sp_san_inspection_v2.accnt_score',
        'sp_infrastructure.tech_type',
        DB::raw("
            CASE
              WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 'Tubewell'
              WHEN sp_infrastructure.tech_type = 'RWH' THEN 'Rainwater harvesting system'
              WHEN sp_infrastructure.tech_type = 'RO'  THEN 'Reverse osmosis system'
              WHEN sp_infrastructure.tech_type = 'PWS' THEN 'Piped water system'
              ELSE sp_infrastructure.tech_type
            END AS technology_name
        ")
      )
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->get();

    $reportData = $rows->map(function ($r) {
      $inspectionDate = $r->inspection_date ? date('Y/m/d', strtotime($r->inspection_date)) : '';
      $sanitaryScore = is_null($r->sanitary_score) ? 0 : (int)$r->sanitary_score;
      $accntScore = is_null($r->accnt_score) ? 0 : (int)$r->accnt_score;

      return [
        (string)$r->water_id,
        strtolower($r->technology_name),
        $inspectionDate,
        $sanitaryScore,
        $inspectionDate, // national
        $inspectionDate, // district
        $inspectionDate, // management
        $sanitaryScore - $accntScore, // accountable
      ];
    })->toArray();

    // CSV Headers
    $headers = [
      'wp_id',
      'SI_assessment',
      'SI_assessment_date',
      'SI_risk_score',
      'SI_result_reported_to_national_government_date',
      'SI_result_reported_to_district_government_date',
      'SI_result_reported_to_school_or_community_clinic_management_date',
      'SI_accountable_risk_score',
    ];

    $filename = 'RBC_Sanitary_Inspection_Report_' . date('Y-m-d') . '.csv';

    // Create CSV response
    $callback = function () use ($headers, $reportData) {
      $file = fopen('php://output', 'w');

      // Add BOM for Excel UTF-8 compatibility
      fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Write headers
      fputcsv($file, $headers);

      // Write data rows
      foreach ($reportData as $row) {
        fputcsv($file, $row);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    ]);
  }

  public function rbcSanitaryInspectionReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcSanitaryInspectionReportPrint');
  }


  public function rbcEcoliAssessmentsReport(Request $request)
  {
    $rows = DB::table('sp_water_quality')
      ->join('sp_infrastructure', 'sp_water_quality.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_water_quality.water_id',
        'sp_water_quality.test_date',
        'sp_water_quality.unit',
        'sp_water_quality.value',
        'sp_water_quality.notify_date',
        'sp_water_quality.parameter',
        'sp_water_quality.sample_cat',
        'sp_infrastructure.tech_type',
        DB::raw("
            CASE
              WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 'Tubewell'
              WHEN sp_infrastructure.tech_type = 'RWH' THEN 'Rainwater harvesting system'
              WHEN sp_infrastructure.tech_type = 'RO'  THEN 'Reverse osmosis system'
              WHEN sp_infrastructure.tech_type = 'PWS' THEN 'Piped water system'
              ELSE sp_infrastructure.tech_type
            END AS technology_name
        ")
      )
      ->where('sp_water_quality.sample_cat', 'Bacteriological')
      ->where('sp_water_quality.parameter', 'E.coli')
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->orderBy('sp_water_quality.sample_cat', 'ASC')
      ->get();

    $assessments = $rows->map(function ($r) {

      return [
        'wp_id' => (string)$r->water_id,
        'assessment' => strtolower($r->technology_name), // tubewell
        'date' => $r->test_date ? date('Y/m/d', strtotime($r->test_date)) : '',
        'units' => 'result in MPN per 100mL',
        'result' => is_null($r->value) ? '' : $r->value,
        'limit' => '', // E.coli limit is 0
        'national' => '',
        'district' => '',
        'management' => $r->notify_date ? date('Y/m/d', strtotime($r->notify_date)) : '',
      ];

    })->values()->toArray();


    return view('core.safe_pani_admin.basic_data_entry.reports.rbcEcoliAssessmentsReport', compact('assessments'));
  }

  public function rbcEcoliAssessmentsReportExport(Request $request)
  {
    $rows = DB::table('sp_water_quality')
      ->join('sp_infrastructure', 'sp_water_quality.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_water_quality.water_id',
        'sp_water_quality.test_date',
        'sp_water_quality.unit',
        'sp_water_quality.value',
        'sp_water_quality.notify_date',
        'sp_water_quality.parameter',
        'sp_water_quality.sample_cat',
        'sp_infrastructure.tech_type',
        DB::raw("
            CASE
              WHEN sp_infrastructure.tech_type IN ('DTW','STW') THEN 'Tubewell'
              WHEN sp_infrastructure.tech_type = 'RWH' THEN 'Rainwater harvesting system'
              WHEN sp_infrastructure.tech_type = 'RO'  THEN 'Reverse osmosis system'
              WHEN sp_infrastructure.tech_type = 'PWS' THEN 'Piped water system'
              ELSE sp_infrastructure.tech_type
            END AS technology_name
        ")
      )
      ->where('sp_water_quality.sample_cat', 'Bacteriological')
      ->where('sp_water_quality.parameter', 'E.coli')
      ->where('sp_school.distid', 6)
      ->where('sp_infrastructure.is_active', 1)
      ->orderBy('sp_water_quality.sample_cat', 'ASC')
      ->get();

    $reportData = $rows->map(function ($r) {
      return [
        (string)$r->water_id,
        strtolower($r->technology_name),
        $r->test_date ? date('Y/m/d', strtotime($r->test_date)) : '',
        'result in MPN per 100mL',
        is_null($r->value) ? '' : $r->value,
        '', // E.coli detection limit
        '', // national
        '', // district
        $r->notify_date ? date('Y/m/d', strtotime($r->notify_date)) : '',
      ];
    })->toArray();

    // CSV Headers
    $headers = [
      'wp_id',
      'E_coli_assessment',
      'E_coli_assessment_date',
      'E_coli_assessment_result_units',
      'E_coli_assessment_result',
      'E_coli_detection_limit',
      'E_coli_result_reported_to_national_government_date',
      'E_coli_result_reported_to_district_government_date',
      'E_coli_result_reported_to_school_or_community_clinic_management_date',
    ];

    $filename = 'RBC_Ecoli_Assessments_Report_' . date('Y-m-d') . '.csv';

    // Create CSV response
    $callback = function () use ($headers, $reportData) {
      $file = fopen('php://output', 'w');

      // Add BOM for Excel UTF-8 compatibility
      fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Write headers
      fputcsv($file, $headers);

      // Write data rows
      foreach ($reportData as $row) {
        fputcsv($file, $row);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    ]);
  }

  public function rbcEcoliAssessmentsReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcEcoliAssessmentsReportPrint');
  }

  public function rbcChemicalAssessmentsReport(Request $request)
  {
    $rows = DB::table('sp_water_quality')
      ->join('sp_infrastructure', 'sp_water_quality.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_water_quality.water_id',
        'sp_water_quality.parameter',
        DB::raw("
            CASE
                WHEN sp_water_quality.parameter = 'EC' THEN 'Electrical conductivity (EC) test'
                WHEN sp_water_quality.parameter = 'Arsenic' THEN 'Arsenic (As) test'
                WHEN sp_water_quality.parameter = 'Manganese' THEN 'Manganese (Mn) test'
                ELSE sp_water_quality.parameter
            END AS parameter_name
        "),
        'sp_water_quality.sampling_date',
        'sp_water_quality.unit',
        'sp_water_quality.value',
        'sp_water_quality.notify_date'
      )
      ->where('sp_infrastructure.is_active', 1)
      ->where('sp_school.distid', 6)   // ✅ district filter
      ->where(function ($q) {
        $q->where('sp_water_quality.sample_cat', 'Chemical')
          ->orWhere(function ($q2) {
            $q2->where('sp_water_quality.sample_cat', 'Bactrological')
              ->where('sp_water_quality.parameter', 'EC');
          });
      })
      ->orderBy('sp_water_quality.notify_date', 'DESC')
      ->get();


    $allData = $rows->map(function ($r) {

      $unitsText = '';
      if ($r->parameter === 'Arsenic') {
        $unitsText = 'As: result in mg/L or ppm';
      } elseif ($r->parameter === 'Manganese') {
        $unitsText = 'Mn: result in mg/L (ppm)';
      } elseif ($r->parameter === 'EC') {
        $unitsText = 'EC: result in uS/Cm';
      }

      return [
        'wp_id' => (string)$r->water_id,
        'assessment' => (string)$r->parameter_name, // ✅ fixed
        'date' => $r->sampling_date ? date('Y/m/d', strtotime($r->sampling_date)) : '',
        'units' => $unitsText,
        'result' => is_null($r->value) ? '' : (float)$r->value,
        'limit' => '',
        'national' => '',
        'district' => '',
        'management' => $r->notify_date ? date('Y/m/d', strtotime($r->notify_date)) : '',
      ];
    })->values();


    return view('core.safe_pani_admin.basic_data_entry.reports.rbcChemicalAssessmentsReport', compact('allData'));
  }

  public function rbcChemicalAssessmentsReportExport(Request $request)
  {
    $rows = DB::table('sp_water_quality')
      ->join('sp_infrastructure', 'sp_water_quality.infrastructure_id', '=', 'sp_infrastructure.id')
      ->join('sp_school', 'sp_infrastructure.school_id', '=', 'sp_school.id')
      ->select(
        'sp_water_quality.water_id',
        'sp_water_quality.parameter',
        DB::raw("
            CASE
                WHEN sp_water_quality.parameter = 'EC' THEN 'Electrical conductivity (EC) test'
                WHEN sp_water_quality.parameter = 'Arsenic' THEN 'Arsenic (As) test'
                WHEN sp_water_quality.parameter = 'Manganese' THEN 'Manganese (Mn) test'
                ELSE sp_water_quality.parameter
            END AS parameter_name
        "),
        'sp_water_quality.sampling_date',
        'sp_water_quality.unit',
        'sp_water_quality.value',
        'sp_water_quality.notify_date'
      )
      ->where('sp_infrastructure.is_active', 1)
      ->where('sp_school.distid', 6)
      ->where(function ($q) {
        $q->where('sp_water_quality.sample_cat', 'Chemical')
          ->orWhere(function ($q2) {
            $q2->where('sp_water_quality.sample_cat', 'Bactrological')
              ->where('sp_water_quality.parameter', 'EC');
          });
      })
      ->orderBy('sp_water_quality.notify_date', 'DESC')
      ->get();

    $reportData = $rows->map(function ($r) {
      $unitsText = '';
      if ($r->parameter === 'Arsenic') {
        $unitsText = 'As: result in mg/L or ppm';
      } elseif ($r->parameter === 'Manganese') {
        $unitsText = 'Mn: result in mg/L (ppm)';
      } elseif ($r->parameter === 'EC') {
        $unitsText = 'EC: result in uS/Cm';
      }

      return [
        (string)$r->water_id,
        (string)$r->parameter_name,
        $r->sampling_date ? date('Y/m/d', strtotime($r->sampling_date)) : '',
        $unitsText,
        is_null($r->value) ? '' : (float)$r->value,
        '', // limit
        '', // national
        '', // district
        $r->notify_date ? date('Y/m/d', strtotime($r->notify_date)) : '',
      ];
    })->toArray();

    // CSV Headers
    $headers = [
      'wp_id',
      'chemical_assessment',
      'chemical_assessment_date',
      'chemical_assessment_result_units',
      'chemical_assessment_result',
      'chemical_detection_limit',
      'chemical_result_reported_to_national_government_date',
      'chemical_result_reported_to_district_government_date',
      'chemical_result_reported_to_school_or_community_clinic_management_date',
    ];

    $filename = 'RBC_Chemical_Assessments_Report_' . date('Y-m-d') . '.csv';

    // Create CSV response
    $callback = function () use ($headers, $reportData) {
      $file = fopen('php://output', 'w');

      // Add BOM for Excel UTF-8 compatibility
      fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Write headers
      fputcsv($file, $headers);

      // Write data rows
      foreach ($reportData as $row) {
        fputcsv($file, $row);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    ]);
  }

  public function rbcChemicalAssessmentsReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcChemicalAssessmentsReportPrint');
  }

  public function rbcEcoliManagedActionsReport(Request $request)
  {
    $filters = $this->sanitizeEcoliManagedFilters($request);

    $rows = $this->buildEcoliManagedActionsQuery($filters)
      ->paginate($filters['per_page'])
      ->appends($request->all());

    return view('core.safe_pani_admin.basic_data_entry.reports.rbcEcoliManagedActionsReport', [
      'rows' => $rows,
      'filters' => $filters
    ]);
  }

  public function rbcEcoliManagedActionsReportData(Request $request)
  {
    $filters = $this->sanitizeEcoliManagedFilters($request);

    $rows = $this->buildEcoliManagedActionsQuery($filters)
      ->paginate($filters['per_page'])
      ->appends($request->all());

    $tbodyHtml = view(
      'core.safe_pani_admin.basic_data_entry.reports.partials._rbc_ecoli_managed_actions_tbody',
      ['rows' => $rows]
    )->render();

    // Laravel 5.6: links() returns HtmlString, so cast to string
    $paginationHtml = (string)$rows->links();

    return response()->json([
      'tbody_html' => $tbodyHtml,
      'pagination_html' => $paginationHtml,
      'meta' => [
        'total' => $rows->total(),
        'from' => $rows->firstItem(),
        'to' => $rows->lastItem(),
        'current_page' => $rows->currentPage(),
        'last_page' => $rows->lastPage(),
      ]
    ]);
  }


  public function rbcEcoliManagedActionsReportExport(Request $request)
  {
    $filters = $this->sanitizeEcoliManagedFilters($request);

    $data = $this->buildEcoliManagedActionsQuery($filters)->get();

    $filename = 'RBC_Ecoli_Managed_Actions_' . date('Y-m-d') . '.csv';

    $headers = [
      'Content-Type' => 'text/csv; charset=UTF-8',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function () use ($data) {
      $out = fopen('php://output', 'w');

      // Add UTF-8 BOM so Excel opens Bangla/Unicode correctly
      fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

      // Header row
      fputcsv($out, ['SL', 'Waterpoint ID', 'Managed Action Date']);

      $sl = 1;
      foreach ($data as $row) {
        fputcsv($out, [
          $sl++,
          $row->water_id,
          $row->disinfection_date
        ]);
      }

      fclose($out);
    };

    return response()->stream($callback, 200, $headers);
  }

  public function rbcEcoliManagedActionsReportPrint(Request $request)
  {
    return view('core.safe_pani_admin.basic_data_entry.reports.rbcEcoliManagedActionsReportPrint');
  }

  /* ============================
 * Helpers (private methods)
 * ============================ */

  private function sanitizeEcoliManagedFilters(Request $request)
  {
    // Default date range: current month (safe for reports)
    $defaultFrom = date('Y-m-01');
    $defaultTo = date('Y-m-t');

    $from = trim($request->get('from_date', $defaultFrom));
    $to = trim($request->get('to_date', $defaultTo));

    // Basic YYYY-MM-DD validation (Laravel 5.6 friendly)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = $defaultFrom;
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) $to = $defaultTo;

    // swap if from > to
    if ($from > $to) {
      $tmp = $from;
      $from = $to;
      $to = $tmp;
    }

    $search = trim((string)$request->get('search', ''));

    $perPage = (int)$request->get('per_page', 10);
    $allowed = [10, 25, 50, 100];
    if (!in_array($perPage, $allowed)) $perPage = 10;

    // In UI, 100 means "All" – but paginate can't "All" safely if large data
    // We'll keep 100 as max per page (good enough for UI)
    return [
      'from_date' => $from,
      'to_date' => $to,
      'search' => $search,
      'per_page' => $perPage,
    ];
  }

  private function buildEcoliManagedActionsQuery(array $filters)
  {
    // ✅ Update table/column names here if different
    $query = DB::table('sp_water_quality')
      ->select([
        'water_id',
        'disinfection_date'
      ])->where('disinfection_is_required', '>', 0);

    // Date range filter
    $query->whereBetween('disinfection_date', [$filters['from_date'], $filters['to_date']]);

    // Search filter
    if (!empty($filters['search'])) {
      $query->where('water_id', 'like', '%' . $filters['search'] . '%');
    }

    // Sorting
    $query->orderBy('disinfection_date', 'desc');

    return $query;
  }


}
