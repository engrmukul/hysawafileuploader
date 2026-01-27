<?php

namespace App\Model\Download\Superadmin\Finance\UPBudget;

use App\Model\ItemBudget;
use Illuminate\Http\Request;

class UPBudgetDownload
{
  public function download()
  {
     $rows = ItemBudget::with('union.upazila.district', 'item.subhead.head')->get();

    \Excel::create('Report-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'District',
            'Upazila',
            'Union',
            'Head',
            'Sub Head',
            'Item Name',
            'Budget',
            'Start Year',
            'End Year'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $dataRow = [];

          $distName = "";
          if($row->union != "" && $row->union->upazila && $row->union->upazila->district)
          {
            $distName = $row->union->upazila->district->distname;
          }

          $upazilaName = "";
          if($row->union != "" && $row->union->upazila)
          {
            $upazilaName = $row->union->upazila->upname;
          }

          $sheet->row($rowIndex, [
            $distName,
            $upazilaName,
            $row->union != "" ? $row->union->unname : "",
            $row->item->subhead->head->headname,
            $row->item->subhead->sname,
            $row->item->itemname,
            $row->budget,
            $row->s_year,
            $row->e_year
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
