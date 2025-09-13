<?php

namespace App\Model\Download\Superadmin\Project\Sanitation;

use Illuminate\Http\Request;

class SanitationDownload
{
  public function download()
  {
     $rows = \DB::select(\DB::Raw("SELECT
        fdistrict.distname,
        fupazila.upname,
        funion.unname,
        sanitation.cdfno,
        sanitation.latrineno,
        sanitation.cons_type,
        sanitation.village,
        sanitation.maintype,
        sanitation.subtype,
        sanitation.id,
        sanitation.name,
        sanitation.malechamber,
        sanitation.femalechamber,
        sanitation.overheadtank,
        sanitation.motorpump,
        sanitation.watersource,
        sanitation.sockwell,
        sanitation.seotictank,
        sanitation.tapoutside,
        sanitation.longitude,
        sanitation.latitude,
        sanitation.male_ben,
        sanitation.fem_ben,
        sanitation.child_ben,
        sanitation.disb_bene,
        sanitation.caretakername,
        sanitation.caretakerphone,
        sanitation.ch_comittee,
        sanitation.ch_com_tel,
        sanitation.app_date,
        sanitation.app_status,
        sanitation.imp_status
        FROM
        (
          (sanitation LEFT JOIN funion ON sanitation.unid = funion.id)
          LEFT JOIN fupazila ON sanitation.upid = fupazila.id
        ) LEFT JOIN fdistrict ON sanitation.dist_id = fdistrict.id
        ORDER BY
          sanitation.app_status,
          sanitation.imp_status,
          fdistrict.distname,
          fupazila.upname,
          funion.unname
        ")
          );

    \Excel::create('list-of-sanitation-schemes-list'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
             'Approval Status',
                'Imple status',
                'District',
                'Upazila',
                'Union',
                'CDF no',
                'Cons_type',
                'Village',
                'Type of inst.',
                'subtype',
                'Name',
                'Male chamber',
                'Female chamber',
                'Overhead tank',
                'Motor/Pump',
                'Water source',
                'Sockwell',
                'Septic tank',
                'Tap outside',
                'longitude',
                'latitude',
                'Male users',
                'Fem users',
                'Child_users',
                'Disb_user',
                'Caretaker',
                'Caretaker phone',
                'Chairman of comittee',
                'Phone',
                'Approval date',
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
                $row->app_status,
              $row->imp_status,
              $row->distname,
              $row->upname,
              $row->unname,
              $row->cdfno,
              $row->cons_type,
              $row->village,
              $row->maintype,
              $row->subtype,
              $row->name,
              $row->malechamber,
              $row->femalechamber,
              $row->overheadtank,
              $row->motorpump,
              $row->watersource,
              $row->sockwell,
              $row->seotictank,
              $row->tapoutside,
              $row->longitude,
              $row->latitude,
              $row->male_ben,
              $row->fem_ben,
              $row->child_ben,
              $row->disb_bene,
              $row->caretakername,
              $row->caretakerphone,
              $row->ch_comittee,
              $row->ch_com_tel,
              $row->app_date,
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}









