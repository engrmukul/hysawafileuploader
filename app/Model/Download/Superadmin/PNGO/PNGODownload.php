<?php

namespace App\Model\Download\Superadmin\PNGO;

use Illuminate\Http\Request;

class PNGODownload
{
  public function download()
  {
    $rows =  \DB::table('osm_pngo')
                        ->select('fdistrict.distname','fupazila.upname','funion.unname','osm_pngo.ngoname','fdistrict.id','fupazila.id','osm_pngo.edname','osm_pngo.edmobile','osm_pngo.edemail','osm_pngo.address', 'osm_pngo.ContactPerson', 'osm_pngo.contactmobile', 'osm_pngo.contactemail','osm_pngo.contractdate', 'osm_pngo.remarks', 'osm_pngo.id')
                        ->leftJoin('fdistrict', 'osm_pngo.distid', '=', 'fdistrict.id')
                        ->leftJoin('fupazila', 'osm_pngo.upid', '=', 'fupazila.id')
                        ->leftJoin('funion', 'osm_pngo.unid', '=', 'funion.id')
                        ->orderBy('fdistrict.distname', 'ASC')
                        ->orderBy('fupazila.upname', 'ASC')
                        ->orderBy('funion.unname', 'ASC')
                        ->get();

    \Excel::create('PNGO-list'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'District',
            'Upazila',
            'Union',
            'Name of NGO',
            'Executive Director/head of NGO',
            'Mobile (ED)',
            'Email (ED)',
            'Contact Address',
            'Contact Person',
            'Mobile (Contact person)',
            'Email (Contact person)',
            'Contract date',
            'Remarks',
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
                $row->distname ,
                $row->upname ,
                $row->unname,
                $row->ngoname,
                $row->edname,
                $row->edmobile,
                $row->edemail,
                $row->address,
                $row->ContactPerson,
                $row->contactmobile,
                $row->contactemail,
                $row->contractdate,
                $row->remarks,
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}