<?php

namespace App\Model\Download\Superadmin\Finance\Accounts;

use Illuminate\Http\Request;

class AccountItemDownload
{
  public function download()
  {
     $rows = \DB::table('fitem')
            ->select(
              'fitem.id as id',
              'fhead.headname as head',
              'fsubhead.sname as subhead',
              'fitem.itemname as name'
              )
            ->leftjoin('fhead', 'fitem.headid', '=', 'fhead.id')
            ->leftjoin('fsubhead', 'fitem.subid', '=', 'fsubhead.id')
            ->orderBy('fhead.headname', 'fsubhead.sname')
            ->get();

    \Excel::create('Account-Head-SubHead-Item-List-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'Head',
          'Subhead',
          'Item'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->head ,
            $row->subhead ,
            $row->name
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}