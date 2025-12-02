<?php

namespace App\Model\Download\Superadmin\Procurement;

use Illuminate\Http\Request;

class ProcurementDownload
{
  public function download()
  {
    $rows = \DB::select(\DB::Raw("
      SELECT
        procurement.distid,
        fdistrict.distname,
        procurement.con_name,
        procurement.contact,
        procurement.category,
        procurement.phone,
        procurement.remarks,
        procurement.con_add,
        procurement.id
        FROM procurement, fdistrict
        WHERE fdistrict.id=procurement.distid
      "));

    \Excel::create('Procurement-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'District',
          'Contractor',
          'Contact Person',
          'Category',
          'Address',
          'Phone',
          'Remarks')
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->distname ,
            $row->con_name ,
            $row->contact ,
            $row->category ,
            $row->con_add ,
            $row->phone ,
            $row->remarks
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
