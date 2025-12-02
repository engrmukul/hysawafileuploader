<?php

namespace App\Http\Controllers;


use App\Model\Download\MultiSheetExport;
use App\Model\SPSanAnswerObs;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function download()
    {
//        $data =  SPSanAnswerObs::with([
//            'SPSanInspectionV2',
//            'SPSanInspectionV2.infrastructure.school',
//        ])
//            ->whereHas('SPSanInspectionV2.infrastructure.school', function ($q) {
//                $q->where('sp_school.distid', 7);
//            })
//            ->whereHas('SPSanInspectionV2', function ($q) {
//                $q->orderBy('sp_san_inspection_v2.infrastructure_id');
//            })
//            ->get();
//
//        dd($data[0]->SPSanInspectionV2->infrastructure_id);
//    echo '<pre>';
//    print_r($data->toArray());
//    die;
//    echo '</pre>';
        return Excel::download(
            new MultiSheetExport,
            date("d-m-Y").' Satkhira Survey Dataset.xlsx'
        );
    }
}
