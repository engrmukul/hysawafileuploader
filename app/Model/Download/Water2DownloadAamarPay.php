<?php

namespace App\Model\Download;

use Illuminate\Http\Request;

class Water2DownloadAamarPay
{
    private $waters;
    private $request;

    public function __construct($waters)
    {
        $this->waters = $waters;
    }

    public function download()
    {
        $rows = $this->waters;

        if (!count($rows)) {
            return response()->json(['status' => 'error', 'message' => 'No Data Found']);
        }

        \Excel::create('AamarPay Settlements '.date("d-m-Y"), function ($excel) use ($rows) {
            $excel->sheet('Sheetname', function ($sheet) use ($rows) {
                $sheet->setOrientation('landscape');
                $sl = 1;

                $sheet->row(1, array(
                        'Sl',
                        'Hardware ID',
                        'Hardware Type',
                        'App ID',
                        'District',
                        'Upazila',
                        'Union',
                        'Received Amount',
                        'Payment Time',
                    )
                );

                $rowIndex = 2;
                foreach ($rows as $row) {

                    $district = !empty($row->distid) ? ucfirst(strtolower($row->district->distname)) : "";
                    $upazila  = !empty($row->upid) ? ucfirst(strtolower($row->upazila->upname)) : "";
                    $union    = !empty($row->unid) ? ucfirst(strtolower($row->union->unname)): "";

                    $sheet->row($rowIndex, [
                        $sl++,
                        $row->hardware_id,
                        $row->hardware_type,
                        $row->app_id,
                        $district,
                        $upazila,
                        $union,
                        $row->recv_amount,
                        $row->pay_time
                    ]);
                    $rowIndex++;
                }
            });
        })->download('csv');
    }
}

