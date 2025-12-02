<?php

namespace App\Model\Download;

use App\Model\SPRepairRen;
use App\Model\SPRepairType;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FoodSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $repairs;
    protected $orderRowMap;

    public $inspectionRowMap = []; // infra_id → row

    public function __construct($orderRowMap = [])
    {
        $this->orderRowMap = $orderRowMap;

        $this->repairs = SPRepairRen::with([
            'SpInfrastructure',
            'SpInfrastructure.school',
            'SpInfrastructure.school.district',
            'SpInfrastructure.school.upazila',
            'SpInfrastructure.school.union'
        ])
            ->where(DB::raw('GREATEST(
        rtype1, rtype2, rtype3, rtype4, rtype5, rtype6, rtype7, rtype8,
        rtype9, rtype10, rtype11, rtype12, rtype13, rtype14, rtype15, rtype16,
        rtype17, rtype18, rtype19, rtype20, rtype21, rtype22, rtype23, rtype24,
        rtype25, rtype26, rtype27, rtype28, rtype29, rtype30, rtype31, rtype32,
        rtype33, rtype34, rtype35, rtype36
    )'), '!=', 0)
            ->whereHas('SpInfrastructure.school', function ($query2) {
                $query2->where('sp_school.distid', '7');
            })
            ->whereHas('SpInfrastructure', function ($query2) {
                $query2->whereNotNull('sp_infrastructure.ren_om_id')
                ->where('sp_infrastructure.is_active', '3')
                ->orderBy('sp_infrastructure.id');
            })

            ->get()
            ->map(function ($item) {
                if($item->spinfrastructure->is_active == "1" || $item->spinfrastructure->is_active == "3"){
                    $is_manged = 'Yes';
                } else {
                    $is_manged = 'No';
                }

                if ($item->spinfrastructure->tech_type == 'DTW') {
                    $water_type = 'Deep Tubewell';
                } else if ($item->spinfrastructure->tech_type == 'STW') {
                    $water_type = 'Shallow Tubewell';
                } else if ($item->spinfrastructure->tech_type == 'RWH') {
                    $water_type = 'Rainwater Harvesting System';
                } else if ($item->spinfrastructure->tech_type == 'MAR') {
                    $water_type = 'Managed Aquifer Recharge';
                } else if ($item->spinfrastructure->tech_type == 'PWS') {
                    $water_type = 'Piped Water';
                } else if ($item->spinfrastructure->tech_type == 'PSF') {
                    $water_type = 'Pond Sand Filter';
                } else if ($item->spinfrastructure->tech_type == 'SWDU') {
                    $water_type = 'Solar Water Desalination Unit';
                } else if ($item->spinfrastructure->tech_type == 'RO') {
                    $water_type = 'Reverse Osmosis';
                } else if ($item->spinfrastructure->tech_type == 'AIRP') {
                    $water_type = 'Arsenic-Iron Removal Plant';
                }  else {
                    $water_type = 'Unknown';
                }

                //Cost calculation
                $total_cost = 0;
                $repair_types = SPRepairType::all();
                for ($i = 0; $i < count($repair_types); $i++) {
                    $rtype = 'rtype' . ($i + 1);

                    if (!empty($item->$rtype) && $item->$rtype == 1) {
                        $total_cost += (int) ($repair_types[$i]->cost ?? 0);
                    }
                }

                return [
                    'si_id'             => $item->id,
                    'infrastructure_id' => $item->spinfrastructure->id,
                    'water_id'       => $item->spinfrastructure->water_id ?? '',
                    'water_type'     => $water_type ?? '',
                    'sch_name_en'    => $item->spinfrastructure->school->sch_name_en ?? '',
                    'institution_id' => $item->spinfrastructure->school->institution_id ?? '',
                    'sch_type_edu'   => $item->spinfrastructure->school->sch_type_edu ?? '',
                    'union_name'     => ucfirst(strtolower($item->spinfrastructure->school->union->unname)) ?? '',
                    'upazila_name'   => ucfirst(strtolower($item->spinfrastructure->school->upazila->upname)) ?? '',
                    'district_name'  => ucfirst(strtolower($item->spinfrastructure->school->district->distname)) ?? '',

                    'rtype1'  => Helper::normalizeRtype($item->rtype1 ?? null),
                    'rtype2'  => Helper::normalizeRtype($item->rtype2 ?? null),
                    'rtype3'  => Helper::normalizeRtype($item->rtype3 ?? null),
                    'rtype4'  => Helper::normalizeRtype($item->rtype4 ?? null),
                    'rtype5'  => Helper::normalizeRtype($item->rtype5 ?? null),
                    'rtype6'  => Helper::normalizeRtype($item->rtype6 ?? null),
                    'rtype7'  => Helper::normalizeRtype($item->rtype7 ?? null),
                    'rtype8'  => Helper::normalizeRtype($item->rtype8 ?? null),
                    'rtype9'  => Helper::normalizeRtype($item->rtype9 ?? null),
                    'rtype10' => Helper::normalizeRtype($item->rtype10 ?? null),
                    'rtype11' => Helper::normalizeRtype($item->rtype11 ?? null),
                    'rtype12' => Helper::normalizeRtype($item->rtype12 ?? null),
                    'rtype13' => Helper::normalizeRtype($item->rtype13 ?? null),
                    'rtype14' => Helper::normalizeRtype($item->rtype14 ?? null),
                    'rtype15' => Helper::normalizeRtype($item->rtype15 ?? null),
                    'rtype16' => Helper::normalizeRtype($item->rtype16 ?? null),
                    'rtype17' => Helper::normalizeRtype($item->rtype17 ?? null),
                    'rtype18' => Helper::normalizeRtype($item->rtype18 ?? null),
                    'rtype19' => Helper::normalizeRtype($item->rtype19 ?? null),
                    'rtype20' => Helper::normalizeRtype($item->rtype20 ?? null),
                    'rtype21' => Helper::normalizeRtype($item->rtype21 ?? null),
                    'rtype22' => Helper::normalizeRtype($item->rtype22 ?? null),
                    'rtype23' => Helper::normalizeRtype($item->rtype23 ?? null),
                    'rtype24' => Helper::normalizeRtype($item->rtype24 ?? null),
                    'rtype25' => Helper::normalizeRtype($item->rtype25 ?? null),
                    'rtype26' => Helper::normalizeRtype($item->rtype26 ?? null),
                    'rtype27' => Helper::normalizeRtype($item->rtype27 ?? null),
                    'rtype28' => Helper::normalizeRtype($item->rtype28 ?? null),
                    'rtype29' => Helper::normalizeRtype($item->rtype29 ?? null),
                    'rtype30' => Helper::normalizeRtype($item->rtype30 ?? null),
                    'rtype31' => Helper::normalizeRtype($item->rtype31 ?? null),
                    'rtype32' => Helper::normalizeRtype($item->rtype32 ?? null),
                    'rtype33' => Helper::normalizeRtype($item->rtype33 ?? null),
                    'rtype34' => Helper::normalizeRtype($item->rtype34 ?? null),
                    'rtype35' => Helper::normalizeRtype($item->rtype35 ?? null),
                    'rtype36' => Helper::normalizeRtype($item->rtype36 ?? null),
                    (int)$total_cost ?? '',
                    $is_manged ?? '',
                ];
            });
    }

    public function prepareRowMap()
    {
        $row = 2;
        foreach ($this->repairs as $inspection) {
            $infraId = $inspection['infrastructure_id'] ?? null;
            if ($infraId) {
                $this->inspectionRowMap[$infraId] = $row;
            }
            $row++;
        }
    }

    public function collection()
    {
        return $this->repairs;
    }

    public function headings(): array
    {
        $repair_types = SPRepairType::all();
        return [
            'OM ID',
            'Infras. Serial',
            'Infrastructure ID',
            'Infrastructure Type',
            'Institution Name',
            'Institution ID',
            'Institution Type',
            'Union',
            'Upazila',
            'District',
            $repair_types[0]->rdescription,
            $repair_types[1]->rdescription,
            $repair_types[2]->rdescription,
            $repair_types[3]->rdescription,
            $repair_types[4]->rdescription,
            $repair_types[5]->rdescription,
            $repair_types[6]->rdescription,
            $repair_types[7]->rdescription,
            $repair_types[8]->rdescription,
            $repair_types[9]->rdescription,
            $repair_types[10]->rdescription,
            $repair_types[11]->rdescription,
            $repair_types[12]->rdescription,
            $repair_types[13]->rdescription,
            $repair_types[14]->rdescription,
            $repair_types[15]->rdescription,
            $repair_types[16]->rdescription,
            $repair_types[17]->rdescription,
            $repair_types[18]->rdescription,
            $repair_types[19]->rdescription,
            $repair_types[20]->rdescription,
            $repair_types[21]->rdescription,
            $repair_types[22]->rdescription,
            $repair_types[23]->rdescription,
            $repair_types[24]->rdescription,
            $repair_types[25]->rdescription,
            $repair_types[26]->rdescription,
            $repair_types[27]->rdescription,
            $repair_types[28]->rdescription,
            $repair_types[29]->rdescription,
            $repair_types[30]->rdescription,
            $repair_types[31]->rdescription,
            $repair_types[32]->rdescription,
            $repair_types[33]->rdescription,
            $repair_types[34]->rdescription,
            $repair_types[35]->rdescription,
            'Estimated Cost',
            'Actively Managed',
        ];
    }

    public function title(): string
    {
        return 'Technical'; // the tab name
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestCol = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $sheet->setAutoFilter("A1:{$highestCol}{$highestRow}");

                // ✅ Left-align all cells (A1 → last cell)
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Add back-link from Infrastructure ID → Orders Summary row
                $row = 2;
                foreach ($this->repairs as $repair) {
                    $cell = "B{$row}"; // Infrastructure ID column
                    $infraId = $repair['infrastructure_id'];

                    if (isset($this->orderRowMap[$infraId])) {
                        $targetRow = $this->orderRowMap[$infraId];
                        $link = "#'Infrastructures'!A{$targetRow}";
                        $sheet->getCell($cell)->getHyperlink()->setUrl($link);
                        $sheet->getStyle($cell)->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                    }
                    // ✅ Check K–AT columns (11–46)
                    for ($col = 11; $col <= 46; $col++) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                        $checkCell = $colLetter . $row;
                        $value = $sheet->getCell($checkCell)->getValue();

                        if (strtoupper(trim($value)) === 'YES') {
                            $sheet->getStyle($checkCell)->applyFromArray([
                                'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true], // Red
                            ]);
                        }
                    }
                    // ✅ AU column (47th column) → Bold text
                    $sheet->getStyle("AU{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                    ]);
                    $row++;
                }
            },
        ];
    }
}

