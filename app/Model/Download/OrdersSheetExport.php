<?php

namespace App\Model\Download;

use App\Model\SPInfrastructure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class OrdersSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $orders;
    public $orderRowMap = []; // infrastructure_id → row
    protected $categorySheets = []; // ['Dress' => DressSheetExport instance]


    public function __construct()
    {
        $this->orders = SPInfrastructure::with([
            'school',
            'school.district',
            'school.upazila',
            'school.union',
        ])
            ->whereHas('school', function ($q) {
                return $q->where('sp_school.distid', 7);
            })
            ->orderBy('sp_infrastructure.id')
            ->get()
            ->map(function ($item) {
                if ($item->tech_type == 'DTW') {
                    $water_type = 'Deep Tubewell';
                } else if ($item->tech_type == 'STW') {
                    $water_type = 'Shallow Tubewell';
                } else if ($item->tech_type == 'RWH') {
                    $water_type = 'Rainwater Harvesting System';
                } else if ($item->tech_type == 'MAR') {
                    $water_type = 'Managed Aquifer Recharge';
                } else if ($item->tech_type == 'PWS') {
                    $water_type = 'Piped Water';
                } else if ($item->tech_type == 'PSF') {
                    $water_type = 'Pond Sand Filter';
                } else if ($item->tech_type == 'SWDU') {
                    $water_type = 'Solar Water Desalination Unit';
                } else if ($item->tech_type == 'RO') {
                    $water_type = 'Reverse Osmosis';
                } else if ($item->tech_type == 'AIRP') {
                    $water_type = 'Arsenic-Iron Removal Plant';
                }  else {
                    $water_type = 'Unknown';
                }

                if($item->is_active == "1" || $item->is_active == "3"){
                    $is_manged = 'Yes';
                } else {
                    $is_manged = 'No';
                }
                return [
                    'id' => $item->id,
                    'school_id' => $item->school->id ?? null,
                    'infrastructure_id' => $item->water_id ?? '',
                    'SI' =>  '',
                    'Sampling' =>  '',
                    'Technical' => '',
                    'Physical' =>  '',
                    'WQ Status' =>  '',
                    'Infrastructure Type' =>  $water_type        ?? '',
                    'sch_name_en'        => $item->school->sch_name_en        ?? '',
                    'institution_id'     => $item->school->institution_id     ?? '',
                    'sch_type_edu'       => $item->school->sch_type_edu       ?? '',
                    'unname'             => ucfirst(strtolower($item->school->union->unname   ?? '')),
                    'upname'             => ucfirst(strtolower($item->school->upazila->upname   ?? '')),
                    'distname'           => ucfirst(strtolower($item->school->district->distname  ?? '')),
                    'functional_status'  => $item->functional_status  ?? '',
                    'non_func_status'    => $item->non_func_status    ?? '',
                    'non_func_days'      => $item->non_func_days      ?? '',
                    'drinking_use'       => $item->drinking_use       ?? '',
                    'non_drink_reason'   => $item->non_drink_reason   ?? '',
                    'run_year_round'     => $item->run_year_round     ?? '',
                    'run_year_reason'    => $item->run_year_reason    ?? '',
                    'install_year'       => $item->install_year       ?? '',
                    'install_by'         => $item->install_by         ?? '',
                    'pumping'            => $item->pumping            ?? '',
                    'depth'              => $item->depth              ?? '',
                    'tanks_count'        => $item->tanks_count        ?? '',
                    'tank_material'      => $item->tank_material      ?? '',
                    'tank_capacity'      => $item->tank_capacity      ?? '',
                    'tank_distance'      => $item->tank_distance      ?? '',
                    'water_hours'        => $item->water_hours        ?? '',
                    'catchment_area'     => $item->catchment_area     ?? '',
                    'catchment_material' => $item->catchment_material ?? '',
                    'capacity_liter'     => $item->capacity_liter     ?? '',
                    'is_om_req'          => $item->is_om_req          ?? '',
                    'is_manged'          => $is_manged                ?? '',
                    'comments'           => $item->comments           ?? '',
                    'image' => '=HYPERLINK("http://hysawa.com/mis/public/upload/sp_satkhira_infras/'.$item->image.'", "Image Link")'  ?? '',
                ];
            });
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Inst. Serial',
            'Infrastructure ID',
            'SI',
            'Sampling',   // bi-directional link
            'Technical',
            'Physical',
            'WQ Status',
            'Waterpoint Type',
            'Institution Name',
            'Institution ID',
            'Institution Type',
            'Union',
            'Upazila',
            'District',
            'Functional Status',
            'non_func_status',
            'non_func_days',
            'Drinking Use',
            'non_drinking_reason',
            'run_year_round?',
            'run_year_round_no_reason',
            'Install Year',
            'Installed By',
            'Pumping mechanism',
            'Depth (ft)',
            'No. of Tanks',
            'Tank material',
            'Tank capacity (ltr)',
            'Tank Distance',
            'Water_availability_hours',
            'Catchment Area (m2)',
            'Catchment Material',
            'Production Capacity (ltr/hr)',
            'Repair/ Renovation Required?',
            'Actively Managed',
            'Comments',
            'Image'
        ];
    }

    public function prepareRowMap()
    {
        $row = 2;
        foreach ($this->orders as $order) {
            $this->orderRowMap[$order['id']] = $row; // array access
            $row++;
        }
    }

    public function title(): string
    {
        return 'Infrastructures';
    }

    public function setCategorySheets(array $sheets)
    {
        $this->categorySheets = $sheets;
    }

    public function getFirstOrderRows(): array
    {
        $map = [];
        $row = 2;
        foreach ($this->orders as $order) {
            if (!isset($map[$order['school_id']])) {
                $map[$order['school_id']] = $row;
            }
            $row++;
        }
        return $map;
    }

    public function getOrderRowMap(): array
    {
        $map = [];
        $row = 2;
        foreach ($this->orders as $order) {
            $map[$order['id']] = $row;
            $row++;
        }
        return $map;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestCol = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $column = 'AL'; // target column

                for ($row = 2; $row <= $highestRow; $row++) { // assuming row 1 is header
                    $cell = $sheet->getCell("{$column}{$row}");

                    // If the cell has a hyperlink, or you want to force style
                    if ($cell->getValue()) {
                        $sheet->getStyle("{$column}{$row}")->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => '0000FF'],
                                'underline' => 'single',
                            ],
                        ]);
                    }
                }

                $sheet->setAutoFilter("A1:{$highestCol}{$highestRow}");

                // ✅ Left-align all cells (A1 → last cell)
                $sheet->getStyle("A1:{$highestCol}{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $row = 2;
                foreach($this->orders as $order) {
                    $infraId = $order['id'];
                    $this->orderRowMap[$infraId] = $row;
                    $schoolId = $order['school_id'] ?? null; // safe fallback

                    /** 🔗 School ID → Institutions sheet */
                    if ($schoolId && isset($this->categorySheets['Users']->userRowMap[$schoolId])) {
                        $targetRow = $this->categorySheets['Users']->userRowMap[$schoolId];
                        $link = "#'Institutions'!A{$targetRow}";
                        $sheet->getCell("B{$row}")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("B{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                    }

                    // Dress(SI) hyperlink → SI sheet, exact row
                    if(isset($this->categorySheets['Dress'])
                        && isset($this->categorySheets['Dress']->inspectionRowMap[$infraId])) {

                        $targetRow = $this->categorySheets['Dress']->inspectionRowMap[$infraId];
                        $link = "#'SI'!B{$targetRow}"; // jump to SI tab, B column
                        $sheet->getCell("D{$row}")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("D{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                        $sheet->getCell("D{$row}")->setValue('SI Data');
                    } else {
                        // fallback → top of SI sheet
                        $sheet->getCell("D{$row}")->setValue('Not Found');
                        $sheet->getStyle("D{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => 'FF0000']],
                        ]);
                        $sheet->getCell("D{$row}")->getHyperlink()->setUrl("#'SI'!A1");
                    }

                    if(isset($this->categorySheets['Electronics'])
                        && isset($this->categorySheets['Electronics']->electronicsRowMap[$infraId])) {

                        $targetRow = $this->categorySheets['Electronics']->electronicsRowMap[$infraId];
                        $link = "#'Sampling'!B{$targetRow}"; // jump to SI tab, B column
                        $sheet->getCell("E{$row}")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("E{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                        $sheet->getCell("E{$row}")->setValue('Sampling Data');
                    } else {
                        // fallback → top of SI sheet
                        $sheet->getCell("E{$row}")->setValue('Not Found');
                        $sheet->getStyle("E{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => 'FF0000']],
                        ]);
                        $sheet->getCell("E{$row}")->getHyperlink()->setUrl("#'Sampling'!A1");
                    }

                    if(isset($this->categorySheets['Kids'])
                        && isset($this->categorySheets['Kids']->inspectionRowMap[$infraId])) {

                        $targetRow = $this->categorySheets['Kids']->inspectionRowMap[$infraId];
                        $link = "#'Physical'!B{$targetRow}"; // jump to SI tab, B column
                        $sheet->getCell("G{$row}")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("G{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                        $sheet->getCell("G{$row}")->setValue('Phy. Obs.');
                    } else {
                        // fallback → top of SI sheet
                        $sheet->getCell("G{$row}")->setValue('Not Found');
                        $sheet->getStyle("G{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => 'FF0000']],
                        ]);
                        $sheet->getCell("G{$row}")->getHyperlink()->setUrl("#'Physical'!A1");
                    }

// Food (Technical) hyperlink → Technical sheet
                    if (isset($this->categorySheets['Food'])
                        && isset($this->categorySheets['Food']->inspectionRowMap[$infraId])) {

                        $targetRow = $this->categorySheets['Food']->inspectionRowMap[$infraId];
                        $link = "#'Technical'!B{$targetRow}"; // jump into Food tab at correct row
                        $sheet->getCell("F{$row}")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("F{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                        $sheet->getCell("F{$row}")->setValue('Tech. Data');
                    } else {
                        // fallback if not found
                        $sheet->getCell("F{$row}")->setValue('Not Found');
                        $sheet->getStyle("F{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => 'FF0000']],
                        ]);
                        $sheet->getCell("F{$row}")->getHyperlink()->setUrl("#'Technical'!A1");
                    }


                    if(isset($this->categorySheets['Medicine'])
                        && isset($this->categorySheets['Medicine']->inspectionRowMap[$infraId])) {

                        $targetRow = $this->categorySheets['Medicine']->inspectionRowMap[$infraId];
                        $link = "#'WQ Status'!B{$targetRow}"; // jump to SI tab, B column
                        $sheet->getCell("H{$row}")->getHyperlink()->setUrl($link);
                        $sheet->getStyle("H{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '0000FF'], 'underline' => 'single'],
                        ]);
                        $sheet->getCell("H{$row}")->setValue('WQ Stat.');
                    } else {
                        // fallback → top of SI sheet
                        $sheet->getCell("H{$row}")->setValue('Not Found');
                        $sheet->getStyle("H{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => 'FF0000']],
                        ]);
                        $sheet->getCell("H{$row}")->getHyperlink()->setUrl("#'WQ Status'!A1");
                    }

                    $row++;
                }
            },
        ];
    }
}



