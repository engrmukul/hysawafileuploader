<?php

namespace App\Model\Download;

use App\Model\Project;
use Illuminate\Http\Request;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;

class SpOMProgRepV2Download
{
  private $om_works;
  private $request;

  public function __construct($om_works, $start_date, $end_date)
  {
      $this->om_works = $om_works;
      $this->start_date = $start_date;
      $this->end_date = $end_date;
  }

  public function download()
  {
    $rows = $this->om_works;
    $start_date = $this->start_date;
    $end_date = $this->end_date;

    if(!count($rows))
    {
      return response()->json(['status' => 'error', 'message' => 'No Data Found']);
    }

    \Excel::create(date("d-m-Y").' SafePani O&M Progress Summary', function($excel) use($rows, $start_date, $end_date) {
      //$excel->sheet()->mergeCells('G1:I1');

        $excel->sheet('Sheetname', function($sheet) use($rows, $start_date, $end_date) {
        $sheet->setOrientation('landscape');
            $styleArray = [
                'fill' => [
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFC0CB'],
                ],
            ];
        $sheet->getStyle('C1:D1')->applyFromArray($styleArray);
        $sheet->getStyle('E1:F1')->applyFromArray($styleArray);
        $sheet->getStyle('I1:J1')->applyFromArray($styleArray);
        $sheet->getStyle('J1:O1')->applyFromArray($styleArray);

//        $sheet->getStyle('A1:A2000')
//                ->getAlignment()
//                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
          $sl = 1;

              $sheet->row(1, array(
                      '',
                      'Duration: ',
                      'Start: '.$start_date,
                      '',
                      'End: '.$end_date,
                      '',
                      'Infrastructure Types ',
                      '',
                      '',
                      'Maintenance Works ',
                      '',
                      '',
                      '',
                      '',
                  )
              );

              $sheet->row(2, array(
                      'Sl',
                      'Union',
                      'Upazila',
                      'Active WP',
                      'O&M Works',
                      'Tot. Progress',
                      'Tubewell',
                      'RWH',
                      'Other WP',
                      'Samll Parts',
                      'Large Parts',
                      'Tank Cleaning',
                      'Disinfection',
                      'Electricity',
                      'Other works'
                  )
              );

              $rowIndex = 3;
              foreach($rows as $row)
              {
                  $sheet->row($rowIndex, [
                      $sl++,
                      $row['unname'],
                      $row['upname'],
                      $row['waterpoints'],
                      $row['un_weekly_maint'],
                      $row['un_comm_maint'],
                      $row['tubewell'],
                      $row['rwh'],
                      $row['other_wps'],
                      $row['small_parts'],
                      $row['large_parts'],
                      $row['tank'],
                      $row['disinf'],
                      $row['electricity'],
                      $row['other_works'],

                  ]);
                  $rowIndex++;
              }

        });
    })->download('csv');
  }
}
