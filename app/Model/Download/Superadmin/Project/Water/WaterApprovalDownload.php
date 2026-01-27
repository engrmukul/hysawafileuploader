<?php

namespace App\Model\Download\Superadmin\Project\Water;

use Illuminate\Http\Request;

class WaterApprovalDownload
{
  public function download()
  {
     $rows = \DB::select(\DB::Raw("SELECT
            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1,0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            SUM(IF(depth != '0', 1, 0)) AS 'depth',
            SUM(IF(x_coord != '', 1, 0)) AS 'x_coord'
            FROM
            tbl_water")
          );

    \Excel::create('Approval-and-Implementation-Status-Approval-Summary'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');

        // $sheet->row(1, array(
        //   'Approval status',
        //   'Implementation status')
        // );

        // $sheet->mergeCells('A1:A6');
        // $sheet->mergeCells('A7:A14');

        $sheet->row(1, array(
          'Total Submitted',
          'Approved',
          'Pending',
          'Recomended',
          'Cancelled',
          'Rejected',
          'In Tendering process',
          'Under construction',
          'Completed',
          'WQ tested',
          'Platform constructed',
          'Depth measured',
          'GPS Coordinates',
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
              $row->subappcount,
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
              $row->x_coord,
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
