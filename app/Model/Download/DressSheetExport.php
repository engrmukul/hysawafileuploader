<?php

namespace App\Model\Download;

use App\Model\SPInfrastructure;
use App\Model\SPSanInspectionV2;
use App\Model\SPSanQuest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class DressSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $inspections;
    protected $orderRowMap; // Orders sheet: infra_id => row
    public $inspectionRowMap = []; // Dress(SI): infra_id => row

    public function __construct(array $orderRowMap)
    {
        $this->orderRowMap = $orderRowMap;

        $this->inspections = SPSanInspectionV2::with([
            'infrastructure',
            'infrastructure.school',
            'infrastructure.school.district',
            'infrastructure.school.upazila',
            'infrastructure.school.union',
            'SpSanAnswer',
        ])
            ->whereHas('infrastructure.school', function ($q) {
                return $q->where('sp_school.distid', 7);
            })
            ->orderBy('id')
            ->get()
            ->map(function ($item) {

                $image1 = $image2 = $image3 = '';
                if($item->image1 != null){
                    $image1 = "http://www.hysawa.com/mis/public/".$item->image1;
                }

                if($item->image2 != null){
                    $image2 = "http://www.hysawa.com/mis/public/".$item->image2 ;
                }

                if($item->image3 != null){
                    $image3 = "http://www.hysawa.com/mis/public/".$item->image3;
                }

                if($item->infrastructure->is_active == "1" || $item->infrastructure->is_active == "3") {
                    $is_manged = 'Yes';
                } else {
                    $is_manged = 'No';
                }

                if ($item->infrastructure->tech_type == 'DTW') {
                    $water_type = 'Deep Tubewell';
                } else if ($item->infrastructure->tech_type == 'STW') {
                    $water_type = 'Shallow Tubewell';
                } else if ($item->infrastructure->tech_type == 'RWH') {
                    $water_type = 'Rainwater Harvesting System';
                } else if ($item->infrastructure->tech_type == 'MAR') {
                    $water_type = 'Managed Aquifer Recharge';
                } else if ($item->infrastructure->tech_type == 'PWS') {
                    $water_type = 'Piped Water';
                } else if ($item->infrastructure->tech_type == 'PSF') {
                    $water_type = 'Pond Sand Filter';
                } else if ($item->infrastructure->tech_type == 'SWDU') {
                    $water_type = 'Solar Water Desalination Unit';
                } else if ($item->infrastructure->tech_type == 'RO') {
                    $water_type = 'Reverse Osmosis';
                } else if ($item->infrastructure->tech_type == 'AIRP') {
                    $water_type = 'Arsenic-Iron Removal Plant';
                }  else {
                    $water_type = 'Unknown';
                }

                return [
                    'si_id'             => $item->id,
                    'infrastructure_id' => $item->infrastructure->id ?? null,
                    'water_id'  => $item->water_id  ?? '',
                    'Infrastructure Type' =>  $water_type  ?? '',
                    $item->infrastructure->school->sch_name_en,
                    ucfirst(strtolower($item->infrastructure->school->union->unname)) ?? '',
                    ucfirst(strtolower($item->infrastructure->school->upazila->upname)) ?? '',
                    ucfirst(strtolower($item->infrastructure->school->district->distname)) ?? '',
                    'inspection_date' => \Carbon\Carbon::parse($item->inspection_date)->format('Y-m-d') ?? '',
                    'sanitary_score'  => $item->sanitary_score  ?? '',
                    'accnt_score'     => $item->accnt_score     ?? '',
                    'sanitary_risk'   => $item->sanitary_risk   ?? '',
                    'twhpq1'  => $item->SpSanAnswer->twhpq1  ?? '',
                    'twhpq2'  => $item->SpSanAnswer->twhpq2  ?? '',
                    'twhpq3'  => $item->SpSanAnswer->twhpq3  ?? '',
                    'twhpq4'  => $item->SpSanAnswer->twhpq4  ?? '',
                    'twhpq5'  => $item->SpSanAnswer->twhpq5  ?? '',
                    'twhpq6'  => $item->SpSanAnswer->twhpq6  ?? '',
                    'twhpq7'  => $item->SpSanAnswer->twhpq7  ?? '',
                    'twhpq8'  => $item->SpSanAnswer->twhpq8  ?? '',
                    'twhpq9'  => $item->SpSanAnswer->twhpq9  ?? '',

                    'bhmpq1'  => $item->SpSanAnswer->bhmpq1  ?? '',
                    'bhmpq2'  => $item->SpSanAnswer->bhmpq2  ?? '',
                    'bhmpq3'  => $item->SpSanAnswer->bhmpq3  ?? '',
                    'bhmpq4'  => $item->SpSanAnswer->bhmpq4  ?? '',
                    'bhmpq5'  => $item->SpSanAnswer->bhmpq5  ?? '',
                    'bhmpq6'  => $item->SpSanAnswer->bhmpq6  ?? '',
                    'bhmpq7'  => $item->SpSanAnswer->bhmpq7  ?? '',
                    'bhmpq8'  => $item->SpSanAnswer->bhmpq8  ?? '',
                    'bhmpq9'  => $item->SpSanAnswer->bhmpq9  ?? '',
                    'bhmpq10' => $item->SpSanAnswer->bhmpq10  ?? '',

                    'rwhq1'   => $item->SpSanAnswer->rwhq1  ?? '',
                    'rwhq2'   => $item->SpSanAnswer->rwhq2  ?? '',
                    'rwhq3'   => $item->SpSanAnswer->rwhq3  ?? '',
                    'rwhq4'   => $item->SpSanAnswer->rwhq4  ?? '',
                    'rwhq5'   => $item->SpSanAnswer->rwhq5  ?? '',
                    'rwhq6'   => $item->SpSanAnswer->rwhq6  ?? '',
                    'rwhq7'   => $item->SpSanAnswer->rwhq7  ?? '',
                    'rwhq8'   => $item->SpSanAnswer->rwhq8  ?? '',
                    'rwhq9'   => $item->SpSanAnswer->rwhq9  ?? '',
                    'rwhq10'  => $item->SpSanAnswer->rwhq10  ?? '',
                    'rwhq11'  => $item->SpSanAnswer->rwhq11  ?? '',
                    'rwhq12'  => $item->SpSanAnswer->rwhq12  ?? '',
                    'rwhq13'  => $item->SpSanAnswer->rwhq13  ?? '',
                    'rwhq14'  => $item->SpSanAnswer->rwhq14  ?? '',
                    'rwhq15'  => $item->SpSanAnswer->rwhq15  ?? '',
                    'rwhq16'  => $item->SpSanAnswer->rwhq16  ?? '',

                    'pwstq1'  => $item->SpSanAnswer->pwstq1  ?? '',
                    'pwstq2'  => $item->SpSanAnswer->pwstq2  ?? '',
                    'pwstq3'  => $item->SpSanAnswer->pwstq3  ?? '',
                    'pwstq4'  => $item->SpSanAnswer->pwstq4  ?? '',
                    'pwstq5'  => $item->SpSanAnswer->pwstq5  ?? '',
                    'image1' => '=HYPERLINK("'.$image1.'", "Image1 Link")'  ?? '',
                    'image2' => '=HYPERLINK("'.$image2.'", "Image2 Link")'  ?? '',
                    'image3' => '=HYPERLINK("'.$image3.'", "Image3 Link")'  ?? '',
                    'is_manged'          => $is_manged                ?? '',
                    'comments'   => $item->comments   ?? '',
                ];
            });
    }

    public function collection()
    {
        return $this->inspections;
    }

    public function prepareRowMap()
    {
        $row = 2;
        foreach ($this->inspections as $inspection) {
            $infraId = $inspection['infrastructure_id'];
            if ($infraId) {
                $this->inspectionRowMap[$infraId] = $row;
            }
            $row++;
        }
    }

    public function headings(): array
    {
        $quest_twhp = SPSanQuest::where('quest_cat', 'hand-pump')->get();
        $quest_twmp = SPSanQuest::where('quest_cat', 'motor-pump')->get();
        $quest_rwh = SPSanQuest::where('quest_cat', 'rainwater')->get();
        $quest_pwsts = SPSanQuest::where('quest_cat', 'pipe-tapstand')->get();

        return [
            'SI ID',
            'Infras. Serial',
            'Infrastructure ID',
            'Infrastructure Type',
            'Institution Name',
            'Union',
            'Upazila',
            'District',
            'Inspection Date',
            'Sanitary Score',
            'Accountable Score',
            'Risk Level',
            $quest_twhp[0]['quest_en'],
            $quest_twhp[1]['quest_en'],
            $quest_twhp[2]['quest_en'],
            $quest_twhp[3]['quest_en'],
            $quest_twhp[4]['quest_en'],
            $quest_twhp[5]['quest_en'],
            $quest_twhp[6]['quest_en'],
            $quest_twhp[7]['quest_en'],
            $quest_twhp[8]['quest_en'],
            $quest_twmp[0]['quest_en'],
            $quest_twmp[1]['quest_en'],
            $quest_twmp[2]['quest_en'],
            $quest_twmp[3]['quest_en'],
            $quest_twmp[4]['quest_en'],
            $quest_twmp[5]['quest_en'],
            $quest_twmp[6]['quest_en'],
            $quest_twmp[7]['quest_en'],
            $quest_twmp[8]['quest_en'],
            $quest_twmp[9]['quest_en'],
            $quest_rwh[0]['quest_en'],
            $quest_rwh[1]['quest_en'],
            $quest_rwh[2]['quest_en'],
            $quest_rwh[3]['quest_en'],
            $quest_rwh[4]['quest_en'],
            $quest_rwh[5]['quest_en'],
            $quest_rwh[6]['quest_en'],
            $quest_rwh[7]['quest_en'],
            $quest_rwh[8]['quest_en'],
            $quest_rwh[9]['quest_en'],
            $quest_rwh[10]['quest_en'],
            $quest_rwh[11]['quest_en'],
            $quest_rwh[12]['quest_en'],
            $quest_rwh[13]['quest_en'],
            $quest_rwh[14]['quest_en'],
            $quest_rwh[15]['quest_en'],
            $quest_pwsts[0]['quest_en'],
            $quest_pwsts[1]['quest_en'],
            $quest_pwsts[2]['quest_en'],
            $quest_pwsts[3]['quest_en'],
            $quest_pwsts[4]['quest_en'],
            'Image1',
            'Image2',
            'Image3',
            'Actively Managed',
            'Comments',
        ];
    }

    public function title(): string
    {
        return 'SI';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestCol = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $columns = ['BA', 'BB', 'BC']; // target columns

                foreach ($columns as $column) {
                    for ($row = 2; $row <= $highestRow; $row++) { // assuming row 1 is header
                        $cell = $sheet->getCell("{$column}{$row}");

                        if ($cell->getValue()) {
                            $sheet->getStyle("{$column}{$row}")->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => '0000FF'],
                                    'underline' => 'single',
                                ],
                            ]);
                        }
                    }
                }

                $sheet->setAutoFilter("A1:{$highestCol}{$highestRow}");

                // ✅ Left-align all cells (A1 → last cell)
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


                $row = 2;
                foreach ($this->inspections as $inspection) {
                    $infraId = $inspection['infrastructure_id'];

                    if ($infraId) {
                        $this->inspectionRowMap[$infraId] = $row;

                        // 🔗 Infrastructure ID (Column B) → Orders sheet
                        if (isset($this->orderRowMap[$infraId])) {
                            $targetRow = $this->orderRowMap[$infraId];
                            $link = "#'Infrastructures'!A{$targetRow}";

                            // re-set value + hyperlink
                            $sheet->setCellValue("B{$row}", $infraId);
                            $sheet->getCell("B{$row}")->getHyperlink()->setUrl($link);
                            $sheet->getStyle("B{$row}")->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => '0000FF'],
                                    'underline' => 'single',
                                ],
                            ]);
                        }
                    }

                    // ✅ Check M–AZ columns (11–46)
                    for ($col = 13; $col <= 52; $col++) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                        $checkCell = $colLetter . $row;
                        $value = $sheet->getCell($checkCell)->getValue();

                        if (strtoupper(trim($value)) === 'YES') {
                            $sheet->getStyle($checkCell)->applyFromArray([
                                'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true], // Red
                            ]);
                        }
                    }

                    $row++;
                }
            },
        ];
    }
}



