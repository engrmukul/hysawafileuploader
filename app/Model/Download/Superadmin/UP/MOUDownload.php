<?php

namespace App\Model\Download\Superadmin\UP;

use Illuminate\Http\Request;

class MOUDownload
{
  public function download()
  {
     $rows = \DB::select(\DB::Raw("
              SELECT
                project.project,
                fdistrict.distname,
                fupazila.upname,
                funion.unname,
                osm_mou.moudate,
                osm_mou.remarks,
                osm_mou.id
              FROM
                osm_mou,
                fdistrict,
                fupazila,
                funion,
                project
              WHERE
                osm_mou.distid = fdistrict.id AND
                osm_mou.upid = fupazila.id AND
                osm_mou.unid =funion.id and
                project.id = osm_mou.projid
              ORDER BY project.project, fdistrict.distname, fupazila.upname, funion.unname"));

    \Excel::create('UP-MOU-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'Project',
          'District',
          'Upazila',
          'Union',
          'MOU Date',
          'Remarks'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->project ,
            $row->distname ,
            $row->upname ,
            $row->unname ,
            $row->moudate ,
            $row->remarks
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}