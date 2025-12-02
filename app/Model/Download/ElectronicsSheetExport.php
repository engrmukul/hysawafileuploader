<?php

namespace App\Model\Download;

use App\Model\SPSampleCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ElectronicsSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $samples;
    public $orderRowMap;            // Orders → row
    public $electronicsRowMap = []; // Electronics → row (for Orders to use)

    public function __construct($orderRowMap = [])
    {
        $this->orderRowMap = $orderRowMap;

        $this->samples = SPSampleCollection::with([
            'infrastructure',
            'infrastructure.school',
            'infrastructure.school.district',
            'infrastructure.school.upazila',
            'infrastructure.school.union',
        ])
            ->whereHas('infrastructure.school', function ($q) {
                $q->where('sp_school.distid', 7);
            })
            ->orderBy('infrastructure_id')
            ->get()
            ->map(function ($item) {
                if($item->infrastructure->is_active == "1" || $item->infrastructure->is_active == "3"){
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
                    'sample_sl'         => $item->id,
                    'infrastructure_id' => $item->infrastructure_id ?? null,
                    'sample_id'      => $item->sample_id ?? '',
                    'sample_no'      => $item->sample_no ?? '',
                    'sample_cat'     => $item->sample_cat ?? '',
                    'water_id'       => $item->water_id ?? '',
                    'water_type'       => $water_type ?? '',
                    'sch_name_en'    => $item->infrastructure->school->sch_name_en ?? '',
                    'sch_type_edu'   => $item->infrastructure->school->sch_type_edu ?? '',
                    ucfirst(strtolower($item->infrastructure->school->union->unname)) ?? '',
                    ucfirst(strtolower($item->infrastructure->school->upazila->upname)) ?? '',
                    ucfirst(strtolower($item->infrastructure->school->district->distname)) ?? '',
                    'ec'             => $item->ec ?? '',
                    'ph'             => $item->ph ?? '',
                    'temp'           => $item->temp ?? '',
                    'weather'        => $item->weather ?? '',
                    'color'          => $item->color ?? '',
                    'decon_process'  => $item->decon_process ?? '',
                    'sample_date'    => $item->sample_date ? \Carbon\Carbon::parse($item->sample_date)->format('Y-m-d') : '',
                    'test_date'    => $item->phy_test_date ? \Carbon\Carbon::parse($item->phy_test_date)->format('Y-m-d') : '',
                    'start_time'     => $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('H:i') : '',
                    'sample_time'    => $item->sample_time ? \Carbon\Carbon::parse($item->sample_time)->format('H:i') : '',
                    'elevation'      => $item->elevation ?? '',
                    'lat'            => $item->lat ?? '',
                    'lon'            => $item->lon ?? '',
                    'is_active'       => $is_manged ?? '',
                    'comments'       => $item->comments ?? '',
                ];
            });
    }

    // ✅ must use array keys, not object properties
    public function prepareRowMap(): void
    {
        $row = 2; // heading row = 1
        foreach ($this->samples as $sample) {
            $infraId = $sample['infrastructure_id'] ?? null;
            if ($infraId) {
                $this->electronicsRowMap[$infraId] = $row;
            }
            $row++;
        }
    }

    public function collection()
    {
        return $this->samples;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Infras. Serial',
            'Sample ID',
            'Sample Type',
            'Sample Cat',
            'Infrastructure ID',
            'Infrastructure Type',
            'Institution Name',
            'Institution Type',
            'Union',
            'Upazila',
            'District',
            'EC (uS/cm)',
            'pH',
            'Temp (Deg.C)',
            'Weather',
            'Color',
            'Decontamination',
            'Sampling Date',
            'Test Date',
            'Start Time',
            'Sampling Time',
            'Elevation',
            'Lat',
            'Lon',
            'Actively Managed',
             'Comments',
        ];
    }

    public function title(): string
    {
        return 'Sampling';
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
                foreach ($this->samples as $sample) {
                    $infraId = $sample['infrastructure_id'];
                    $cell = "B{$row}"; // Infrastructure ID col

                    if ($infraId) {
                        // 🔗 Keep row mapping in case OrdersSheetExport checks again
                        $this->electronicsRowMap[$infraId] = $row;

                        // 🔗 Link back to Orders sheet
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

