<?php

namespace App\Model\Download\Superadmin\Procurement;


use App\Model\Search\ProcurementEvaluationSearch;
use Illuminate\Http\Request;

class ProcurementEvaluationDownload
{
  private $request;
  public function __construct(Request $request)
  {
    $this->request = $request;
  }
  public function download()
  {
    $rows = (new ProcurementEvaluationSearch($this->request))->get();

    \Excel::create('Procurement-Evaluation- '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {

        $sheet->setOrientation('landscape');

        $sheet->row(1, array(
            'Project',
            'District',
            'Upazila',
            'Union',

            'package',
            'con_name',
            'con_add',
            'b_detail',

            'amount',
            'quate',
            'quate_perc',
            'rate',

            'm_receipt',
            'security',
            'l_asset',
            'signed',

            'r_status',
            'rank',
            'noa',
            'noa_date',

            'con_date',
            'con_status',
            'remarks',

            'created_by',
            'created_at',
            'updated_by',
            'updated_at'
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

            $row->package,
            $row->con_name,
            $row->con_add,
            $row->b_detail,

            $row->amount,
            $row->quate,
            $row->quate_perc,
            $row->rate,

            $row->m_receipt,
            $row->security,
            $row->l_asset,
            $row->signed,

            $row->r_status,
            $row->rank,
            $row->noa,
            $row->noa_date,

            $row->con_date,
            $row->con_status,
            $row->remarks,

            $row->created_by,
            $row->created_at,
            $row->updated_by,
            $row->updated_at

          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}