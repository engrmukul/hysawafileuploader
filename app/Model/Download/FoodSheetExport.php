<?php

namespace App\Model\Download;

use App\Model\SPRepairRen;
use App\Model\SPRepairType;
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
            ->whereHas('SpInfrastructure.school', function ($query2) {
                $query2->where('sp_school.distid', '7');
            })
            ->whereHas('SpInfrastructure', function ($query2) {
                $query2->whereNotNull('sp_infrastructure.ren_om_id')
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

                    'rtype1'  => $item->rtype1 ?? '',
                    'rtype2'  => $item->rtype2 ?? '',
                    'rtype3'  => $item->rtype3 ?? '',
                    'rtype4'  => $item->rtype4 ?? '',
                    'rtype5'  => $item->rtype5 ?? '',
                    'rtype6'  => $item->rtype6 ?? '',
                    'rtype7'  => $item->rtype7 ?? '',
                    'rtype8'  => $item->rtype8 ?? '',
                    'rtype9'  => $item->rtype9 ?? '',
                    'rtype10' => $item->rtype10 ?? '',
                    'rtype11' => $item->rtype11 ?? '',
                    'rtype12' => $item->rtype12 ?? '',
                    'rtype13' => $item->rtype13 ?? '',
                    'rtype14' => $item->rtype14 ?? '',
                    'rtype15' => $item->rtype15 ?? '',
                    'rtype16' => $item->rtype16 ?? '',
                    'rtype17' => $item->rtype17 ?? '',
                    'rtype18' => $item->rtype18 ?? '',
                    'rtype19' => $item->rtype19 ?? '',
                    'rtype20' => $item->rtype20 ?? '',
                    'rtype21' => $item->rtype21 ?? '',
                    'rtype22' => $item->rtype22 ?? '',
                    'rtype23' => $item->rtype23 ?? '',
                    'rtype24' => $item->rtype24 ?? '',
                    'rtype25' => $item->rtype25 ?? '',
                    'rtype26' => $item->rtype26 ?? '',
                    'rtype27' => $item->rtype27 ?? '',
                    'rtype28' => $item->rtype28 ?? '',
                    'rtype29' => $item->rtype29 ?? '',
                    'rtype30' => $item->rtype30 ?? '',
                    'rtype31' => $item->rtype31 ?? '',
                    'rtype32' => $item->rtype32 ?? '',
                    'rtype33' => $item->rtype33 ?? '',
                    'rtype34' => $item->rtype34 ?? '',
                    'rtype35' => $item->rtype35 ?? '',
                    'rtype36' => $item->rtype36 ?? '',
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
                    $row++;
                }
            },
        ];
    }
}

