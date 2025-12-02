<?php

namespace App\Model\Download\Superadmin\Project\Water;

use Illuminate\Http\Request;

class WaterUnionDownload
{
  public function download($type = null, $value = null)
  {
    $rows = "";

    if($type == null && $value == "null")
    {

     $rows = \DB::select(\DB::Raw("
            SELECT

            fdistrict.distname,
            fupazila.upname,
            funion.unname,
            tbl_water.unid,
            tbl_water.App_date,
            tbl_water.Tend_lot,

            SUM(tbl_water.HH_benefited) AS 'hhcount',
            Sum(tbl_water.HCHH_benefited) AS hchhcount,
            Sum(tbl_water.HCHH_benefited) / Sum(tbl_water.HH_benefited)*100 AS hcPcount,
            (Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100)*0.1 + (100-(Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100))*0.2 AS cccount,

            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != '', 1, 0)) AS 'x_coord'

            FROM
            tbl_water
            INNER JOIN funion ON funion.id = tbl_water.unid
            INNER JOIN fupazila ON fupazila.id = funion.upid
            INNER JOIN fdistrict ON fdistrict.id = fupazila.disid
            GROUP BY
                tbl_water.unid,
                tbl_water.Tend_lot,
                tbl_water.App_date

            ")
          );
    }else{
       $rows = \DB::select(\DB::Raw("
            SELECT

            fdistrict.distname,
            fupazila.upname,
            funion.unname,
            tbl_water.unid,
            tbl_water.App_date,
            tbl_water.Tend_lot,

            SUM(tbl_water.HH_benefited) AS 'hhcount',
            Sum(tbl_water.HCHH_benefited) AS hchhcount,
            Sum(tbl_water.HCHH_benefited) / Sum(tbl_water.HH_benefited)*100 AS hcPcount,
            (Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100)*0.1 + (100-(Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100))*0.2 AS cccount,

            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != '', 1, 0)) AS 'x_coord'

            FROM
            tbl_water
            INNER JOIN funion ON funion.id = tbl_water.unid
            INNER JOIN fupazila ON fupazila.id = funion.upid
            INNER JOIN fdistrict ON fdistrict.id = fupazila.disid

            WHERE tbl_water.$type = $value

            GROUP BY
                tbl_water.unid,
                tbl_water.Tend_lot,
                tbl_water.App_date

            ")
          );
    }

    \Excel::create('Approval-and-Implementation-Status-Union-Summary'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'District',
          'Upazila',
          'Union',
          'Approval date',
          'Work order/ Lot No.',
          'Total HH',
          'Hardcore HH',
          'Hardcore %',
          'Expected Contribution (%)',
          'Approved',
          'Pending',
          'Recommended',
          'Canceled',
          'Rejected',
          'In Tendering process',
          'Under construction',
          'Completed',
          'WQ tested',
          'Platform constructed',
          'Depth measured',
          'GPS Coordinates',
          'Action')
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->distname,
            $row->upname,
            $row->unname,
            $row->App_date,
            $row->Tend_lot,

            $row->hhcount,
            $row->hchhcount,
            $row->hcPcount,
            $row->cccount,

            $row->sumappcount,
            $row->Submitted,
            $row->Recomended,
            $row->Cancelled,
            $row->Rejected,
            $row->TenderingInProcess,
            $row->UnderImplementation,
            $row->Completed,
            $row->wq_Arsenic,
            $row->platform,
            $row->depth,
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
