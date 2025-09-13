<?php

namespace App\Model\Download\Superadmin\Project\Sanitation;

use Illuminate\Http\Request;

class SummaryDownload
{
  public function download()
  {
     $rows = \DB::select(\DB::Raw("
            SELECT
            region.region_name,
            COUNT(sanitation.id) AS 'subappcount',

            sum(IF(app_status = 'Approved',1,0)) AS 'Approved',
            sum(IF(app_status = 'Completed', 1, 0)) AS 'Completed',

            sum(IF(app_status = 'Approved' AND subtype LIKE '%School%' ,1, 0)) AS 'School',
            sum(IF(app_status = 'Approved' AND subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
            sum(IF(app_status = 'Approved' AND subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
            sum(IF(app_status = 'Approved' AND subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
            sum(IF(app_status = 'Approved' AND ( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
            sum(IF(app_status = 'Approved' AND ( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
            sum(IF(app_status = 'Approved' AND cons_type = 'New' ,1, 0)) AS 'New',
            sum(IF(app_status = 'Approved' AND cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            FROM
              sanitation
            INNER JOIN
              region ON region.id = sanitation.region_id
            GROUP BY
              sanitation.region_id
            ORDER BY
              region.region_name")
          );

    \Excel::create('Sanitation-Summary-Report-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');

        $sheet->row(1, array(
                'Region',
                'Approval/Implementation status',
                '',
                '',
                'Types of latrines',
                '',
                '',
                '',
                'Types by Nos. of Chamber',
                '',
                'Construction type',
                ''
          )
        );

        $sheet->row(2, array(
                'Total submitted',
                'Approved',
                'Completed',
                'School',
                'Madrasha',
                'Mosque',
                'Public',
                'Two Chamber',
                'Three Chamber',
                'New',
                'renovation',
          )
        );

        $rowIndex = 3;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
                $row->region_name,
                $row->subappcount,
                $row->Approved,
                $row->Completed,
                $row->School,
                $row->Madrasha,

                $row->Mosque,
                $row->Community,
                $row->TwoChamber,
                $row->ThreeChamber,
                $row->New,
                $row->Renovation,
          ]);
          $rowIndex++;
        }
        });

    })->download('csv');
  }
}



