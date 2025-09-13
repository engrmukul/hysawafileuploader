<?php

namespace App\Model\Download;

use App\Model\SPWQStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class MedicineSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $statuses;
    protected $orderRowMap;

    public $inspectionRowMap = []; // infra_id → row

    public function __construct($orderRowMap)
    {
        $this->orderRowMap = $orderRowMap;

        $this->statuses = SPWQStatus::with([
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
                $query2->whereNotNull('sp_infrastructure.wq_status_id')
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
                return [
                    'si_id'             => $item->id,
                    'infrastructure_id' => $item->spinfrastructure->id,
                    'water_id'         => $item->spinfrastructure->water_id ?? '',
                    'water_type'       => $water_type ?? '',
                    'sch_name_en'      => $item->spinfrastructure->school->sch_name_en ?? '',
                    'institution_id'   => $item->spinfrastructure->school->institution_id ?? '',
                    'sch_type_edu'     => $item->spinfrastructure->school->sch_type_edu ?? '',
                    'union_name'       => isset($item->spinfrastructure->school->union->unname) ? ucfirst(strtolower($item->spinfrastructure->school->union->unname)) : '',
                    'upazila_name'     => isset($item->spinfrastructure->school->upazila->upname) ? ucfirst(strtolower($item->spinfrastructure->school->upazila->upname)) : '',
                    'district_name'    => isset($item->spinfrastructure->school->district->distname) ? ucfirst(strtolower($item->spinfrastructure->school->district->distname)) : '',
                    'is_past_wq'       => $item->is_past_wq ?? '',
                    'wq_when'          => $item->wq_when ?? '',
                    'wq_who'           => $item->wq_who ?? '',
                    'is_rep'           => $item->is_rep ?? '',
                    'is_ars'           => $item->is_ars ?? '',
                    'is_cl'            => $item->is_cl ?? '',
                    'is_fe'            => $item->is_fe ?? '',
                    'is_agency'        => $item->is_agency ?? '',
                    'agency_nm'        => $item->agency_nm ?? '',
                    'agency_freq'      => $item->agency_freq ?? '',
                    'vul_ann_flood'    => $item->vul_ann_flood ?? '',
                    'vul_storm'        => $item->vul_storm ?? '',
                    'vul_dec_water'    => $item->vul_dec_water ?? '',
                    'vul_tid_flood'    => $item->vul_tid_flood ?? '',
                    'comm_reliance'    => $item->comm_reliance ?? '',
                    'is_manged'        => $is_manged ?? '',
                    'created_at'       => isset($item->created_at) ? date('d-m-Y', strtotime($item->created_at)) : '',

                ];
            });
    }

    public function prepareRowMap()
    {
        $row = 2; // heading row = 1
        foreach ($this->statuses as $inspection) {
            $infraId = $inspection['infrastructure_id'] ?? "";
            $this->inspectionRowMap[$infraId] = $row;
            $row++;
        }
    }

    public function collection()
    {
        return $this->statuses;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Infras. Serial',
            'Infrastructure ID',
            'Infrastructure Type',
            'Institution Name',
            'Institution ID',
            'Institution Type',
            'Union',
            'Upazila',
            'District',
            'Do you remember any/last WQ Test?',
            'When?',
            'Who?',
            'Any report available?',
            'High arsenic',
            'High salinity',
            'High iron',
            'Regular agency test?',
            'Which agency?',
            'Frequency?',
            'Annual flooding',
            'storm inundation',
            'decline in water table',
            'Tidal flooding',
            'Community reliance during climate events?',
            'Actively Managed',
            'Submission Time',
        ];
    }

    public function title(): string
    {
        return 'WQ Status'; // tab name
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
                foreach ($this->statuses as $status) {
                    $cell = "B{$row}"; // Infrastructure ID column
                    $infraId = $status['infrastructure_id'];

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

