<?php

namespace App\Model\Download;

use App\Model\SPSanAnswerObs;
use App\Model\SPSanQuestObs;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class KidsSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $answers;
    protected $orderRowMap;       // Orders sheet infra_id → row
    public $inspectionRowMap = []; // Kids sheet infra_id → row (for Orders sheet)

    public function __construct($orderRowMap = [])
    {
        $this->orderRowMap = $orderRowMap;

        $this->answers = SPSanAnswerObs::with('SPSanInspectionV2', 'SPSanInspectionV2.infrastructure.school', 'SPSanInspectionV2.infrastructure.school.district',
            'SPSanInspectionV2.infrastructure.school.upazila', 'SPSanInspectionV2.infrastructure.school.union')
            ->whereHas('SPSanInspectionV2.infrastructure.school', function ($q) {
                $q->where('sp_school.distid', 7);
            })
            ->whereHas('SPSanInspectionV2', function ($q) {
                $q->orderBy('sp_san_inspection_v2.infrastructure_id');
            })
            ->get()
            ->map(function ($item) {
                if($item->SpSanInspectionV2->infrastructure->is_active == "1" || $item->SpSanInspectionV2->infrastructure->is_active == "3"){
                    $is_manged = 'Yes';
                } else {
                    $is_manged = 'No';
                }

                if ($item->SpSanInspectionV2->infrastructure->tech_type == 'DTW') {
                    $water_type = 'Deep Tubewell';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'STW') {
                    $water_type = 'Shallow Tubewell';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'RWH') {
                    $water_type = 'Rainwater Harvesting System';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'MAR') {
                    $water_type = 'Managed Aquifer Recharge';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'PWS') {
                    $water_type = 'Piped Water';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'PSF') {
                    $water_type = 'Pond Sand Filter';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'SWDU') {
                    $water_type = 'Solar Water Desalination Unit';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'RO') {
                    $water_type = 'Reverse Osmosis';
                } else if ($item->SpSanInspectionV2->infrastructure->tech_type == 'AIRP') {
                    $water_type = 'Arsenic-Iron Removal Plant';
                }  else {
                    $water_type = 'Unknown';
                }
                return [
                    'si_id' => $item->id,
                    'infrastructure_id' => $item->SPSanInspectionV2->infrastructure_id ?? null,
                    'water_id'         => $item->SpSanInspectionV2['water_id'] ?? '',
                    'water_type'       => $water_type ?? '',
                    'sch_name_en'      => $item->SpSanInspectionV2->infrastructure->school->sch_name_en ?? '',
                    'sch_type_edu'     => $item->SpSanInspectionV2->infrastructure->school->sch_type_edu ?? '',
                    'union_name'       => isset($item->SpSanInspectionV2->infrastructure->school->union->unname) ? ucfirst(strtolower($item->SpSanInspectionV2->infrastructure->school->union->unname)) : '',
                    'upazila_name'     => isset($item->SpSanInspectionV2->infrastructure->school->upazila->upname) ? ucfirst(strtolower($item->SpSanInspectionV2->infrastructure->school->upazila->upname)) : '',
                    'district_name'    => isset($item->SpSanInspectionV2->infrastructure->school->district->distname) ? ucfirst(strtolower($item->SpSanInspectionV2->infrastructure->school->district->distname)) : '',
                    'inspection_date' => \Carbon\Carbon::parse($item->SpSanInspectionV2->inspection_date)->format('Y-m-d') ?? '',
                    'wat_user_q1'      => $item->wat_user_q1 ?? '',
                    'wat_user_q2'      => $item->wat_user_q2 ?? '',
                    'wat_user_q3'      => $item->wat_user_q3 ?? '',
                    'wat_user_q4'      => $item->wat_user_q4 ?? '',
                    'wat_user_q5'      => $item->wat_user_q5 ?? '',
                    'wat_user_q6'      => $item->wat_user_q6 ?? '',
                    'phy_obs_q1'       => $item->phy_obs_q1 ?? '',
                    'phy_obs_q2'       => $item->phy_obs_q2 ?? '',
                    'phy_obs_q3'       => $item->phy_obs_q3 ?? '',
                    'is_manged'        => $is_manged ?? '',
                ];
            });
    }

    public function collection()
    {
        return $this->answers;
    }

    public function headings(): array
    {
        $quest_phy = SPSanQuestObs::all();

        return [
            'Entry ID',
            'Infras. Serial',
            'Infrastructure ID',
            'Infrastructure Type',
            'Institution Name',
            'Institution Type',
            'Union',
            'Upazila',
            'District',
            'Inspection Date',
            $quest_phy[0]['quest_en'],
            $quest_phy[1]['quest_en'],
            $quest_phy[2]['quest_en'],
            $quest_phy[3]['quest_en'],
            $quest_phy[4]['quest_en'],
            $quest_phy[5]['quest_en'],
            $quest_phy[6]['quest_en'],
            $quest_phy[7]['quest_en'],
            $quest_phy[8]['quest_en'],
            'Actively Managed',
        ];
    }

    public function title(): string
    {
        return 'Physical';
    }

    public function prepareRowMap()
    {
        $row = 2;
        foreach ($this->answers as $answer) {
            $infraId = $answer['infrastructure_id'] ?? "";
            if ($infraId) {
                $this->inspectionRowMap[$infraId] = $row;
            }
            $row++;
        }
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

                $row = 2;
                foreach ($this->answers as $answer) {
                    $infraId = $answer['infrastructure_id'];
                    $cell = "B{$row}"; // Infrastructure ID column

                    if ($infraId) {
                        // Save Kids row map for Orders sheet
                        $this->inspectionRowMap[$infraId] = $row;

                        // Link back to Infrastructures sheet
                        if (isset($this->orderRowMap[$infraId])) {
                            $targetRow = $this->orderRowMap[$infraId];
                            $link = "#'Infrastructures'!A{$targetRow}";
                            $sheet->getCell($cell)->getHyperlink()->setUrl($link);
                            $sheet->getStyle($cell)->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => '0000FF'],
                                    'underline' => 'single',
                                ],
                            ]);
                        }
                    }
                    $row++;
                }
            },
        ];
    }
}

