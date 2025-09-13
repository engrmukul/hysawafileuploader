<?php

namespace App\Model\Download\Superadmin\Finance\Report;

use App\Model\Budget;
use Illuminate\Http\Request;

class AnalysisDownload
{
  public function download(Request $request)
  {
    $rows = "";

    $sql = "";
    $sql2 = "";

    if($request->has('project_id') && $request->project_id != "" &&
      $request->has('starting_date') && $request->starting_date != "" &&
      $request->has('ending_date') && $request->ending_date != ""
    )
    {
      $sql .= " and desdate between '$request->starting_date' and '$request->ending_date' and proid=$request->project_id";
      $sql2 .= " and date between '$request->starting_date' and '$request->ending_date' and proid=$request->project_id";
    }

    $rows = \DB::table('osm_mou')
      ->select(
        "fdistrict.id",
        "fdistrict.distname",
        "fupazila.id",
        "fupazila.upname",
        "funion.id as unid",
        "funion.unname")
      ->leftjoin('fdistrict', 'osm_mou.distid', '=', 'fdistrict.id')
      ->leftjoin('fupazila', 'osm_mou.upid', '=', 'fupazila.id')
      ->leftjoin('funion', 'osm_mou.unid', '=', 'funion.id')
      ->where('osm_mou.projid', $request->project_id)
      ->get();

    \Excel::create('Report-'.time(), function($excel) use($rows, $sql, $sql2) {
      $excel->sheet('Sheetname', function($sheet) use($rows, $sql, $sql2) {
        $sheet->setOrientation('landscape');

        $sheet->row(1, array(
          '', '', '',
          'HYSAWA Disbursements', '', '', '',
          'UP Statement', '', '', '', '', ''
          )
        );

        $sheet->row(2, array(
          'District', 'Upazila', 'Union',
          'Total', 'Hardware', 'PNGO', 'Others',
          'HYSAWA', 'Comm. Contri', 'Oth. Income', 'TOTAL INCOME', 'TOTAL EXP.', 'BALANCE'
          )
        );

        $data1_1 = 0;
        $data1_2 = 0;
        $data1_3 = 0;
        $data1_4 = 0;

        $data2_1 = 0;
        $data2_2 = 0;
        $data2_3 = 0;
        $data2_4 = 0;
        $data2_5 = 0;
        $data2_6 = 0;


        $rowIndex = 3;
        foreach($rows as $row)
        {

          $data1 = Budget::data1($row->unid,  $sql);
          $data2 = Budget::data2($row->unid, $sql2);

          $data1_1 += $data1[0]->tdesbus;
          $data1_2 += $data1[0]->cdf_no;
          $data1_3 += $data1[0]->cdf_no2;
          $data1_4 += $data1[0]->cdf_no3;

          $data2_1 += $data2[0]->amount;
          $data2_2 += $data2[0]->amount2;
          $data2_3 += $data2[0]->amount3;
          $data2_4 += $data2[0]->amount4;
          $data2_5 += $data2[0]->amount5;
          $data2_6 += ($data2[0]->amount - $data2[0]->amount3);

          $sheet->row($rowIndex, [
            $row->distname ,
            $row->upname ,
            $row->unname ,
            $data1[0]->tdesbus,
            $data1[0]->cdf_no,
            $data1[0]->cdf_no2,
            $data1[0]->cdf_no3,
            $data2[0]->amount,
            $data2[0]->amount2,
            $data2[0]->amount3,
            $data2[0]->amount4,
            $data2[0]->amount5,
            ($data2[0]->amount - $data2[0]->amount3)
          ]);
          $rowIndex++;
        }


        $sheet->row($rowIndex++, array(''));
        $sheet->row($rowIndex++, array(''));
        $sheet->row($rowIndex++, array('Summary'));
        $sheet->row($rowIndex++, array(''));
        $sheet->row($rowIndex++, array(
          '', '', '',
          'HYSAWA Disbursements', '', '', '',
          'UP Statement', '', '', '', '', ''
          )
        );

        $sheet->row($rowIndex++, array(
          '', '', '',
          'Total', 'Hardware', 'PNGO', 'Others',
          'HYSAWA', 'Comm. Contri', 'Oth. Income', 'TOTAL INCOME', 'TOTAL EXP.', 'BALANCE'
          )
        );

        $sheet->row($rowIndex++, [
          '' ,
          '' ,
          '' ,
          $data1_1,
          $data1_2,
          $data1_3,
          $data1_4,
          $data2_1,
          $data2_2,
          $data2_3,
          $data2_4,
          $data2_5,
          $data2_6
          ]);

        });
    })->download('csv');
  }
}
