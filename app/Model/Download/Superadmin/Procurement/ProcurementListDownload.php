<?php

namespace App\Model\Download\Superadmin\Procurement;


use App\Model\Search\ProcurementListSearch;
use Illuminate\Http\Request;

class ProcurementListDownload
{
  private $request;
  public function __construct(Request $request)
  {
    $this->request = $request;
  }
  public function download()
  {
    $rows = (new ProcurementListSearch($this->request))->get();

    \Excel::create('Procurement-List- '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'Project',
            'District',
            'Upazila',
            'Union',

            'Memo No',
            'Package Name',
            'Date of announcement',
            'Last date of receiving',
            'Opening date',
            'Price of Tender Schedule',
            'Estimated Value',
            'Tender Security Amount',
            'Days to complete work',
            'Method',
            'Last date of selling',

            'Selling Office 1',
            'Selling Office 2',
            'Selling Office 3',

            'Recieving office 1',
            'Recieving office 2',
            'Recieving office 3'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->project != "" ? $row->project->project : "",

            ($row->union != "" && $row->union->upazila != "" && $row->union->upazila->district != "") ? $row->union->upazila->district->distname : "",
            ($row->union != "" && $row->union->upazila != "") ? $row->union->upazila->upname : "",
             $row->union != "" ? $row->union->unname : "",

            $row->memo_no ,
            $row->package ,
            $row->d_announce ,
            $row->d_receive ,
            $row->d_open ,
            $row->price_schedule ,
            $row->estimate ,
            $row->s_money ,
            $row->date_com_work ,
            $row->method ,
            $row->d_sell ,

            $row->s_office_1 ,
            $row->s_office_2 ,
            $row->s_office_3 ,
            $row->r_office_1 ,
            $row->r_office_2 ,
            $row->r_office_3

          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}