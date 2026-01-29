<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WeeklyWqSiReportExport implements FromCollection, WithEvents, WithTitle
{
    protected $sampleData;
    protected $totals;
    protected $statusRows;
    protected $reportPeriod;

    public function __construct(array $sampleData, array $totals, array $statusRows, string $reportPeriod = '')
    {
        $this->sampleData = $sampleData;
        $this->totals = $totals;
        $this->statusRows = $statusRows;
        $this->reportPeriod = $reportPeriod;
    }

    public function collection(): Collection
    {
        $data = [];

        // Add empty rows for title area (rows 1-5)
        for ($i = 0; $i < 5; $i++) {
            $data[] = array_fill(0, 40, '');
        }

        // Add empty rows for header area (rows 6-8)
        for ($i = 0; $i < 3; $i++) {
            $data[] = array_fill(0, 40, '');
        }

        // Data rows
        $sl = 1;
        foreach ($this->sampleData as $row) {
            $data[] = [
                $sl++,
                $row['name'],
                $row['wp'],
                $row['si'],
                $row['bf'],
                $row['bd'],
                $row['ec'],
                $row['dis'],
                $row['si_dtw'],
                $row['si_stw'],
                $row['si_rwh'],
                $row['si_ro'],
                $row['si_pws'],
                $row['bf_dtw'],
                $row['bf_stw'],
                $row['bf_rwh'],
                $row['bf_ro'],
                $row['bf_pws'],
                $row['bs_s1'],
                $row['bs_s2'],
                $row['bs_fb'],
                $row['bs_fu'],
                $row['ecd_dtw'],
                $row['ecd_stw'],
                $row['ecd_rwh'],
                $row['ecd_ro'],
                $row['ecd_pws'],
                $row['dis_dtw'],
                $row['dis_stw'],
                $row['dis_rwh'],
                $row['dis_ro'],
                $row['dis_pws'],
                $row['chem_dtw'],
                $row['chem_stw'],
                $row['chem_rwhf'],
                $row['chem_pws'],
                $row['cs_s1'],
                $row['cs_s2'],
                $row['cs_fb'],
                $row['cs_fu'],
            ];
        }

        // Total Progress row
        $data[] = [
            '',
            'Total Progress',
            $this->totals['wp'] ?? 0,
            $this->totals['si'] ?? 0,
            $this->totals['bf'] ?? 0,
            $this->totals['bd'] ?? 0,
            $this->totals['ec'] ?? 0,
            $this->totals['dis'] ?? 0,
            $this->totals['si_dtw'] ?? 0,
            $this->totals['si_stw'] ?? 0,
            $this->totals['si_rwh'] ?? 0,
            $this->totals['si_ro'] ?? 0,
            $this->totals['si_pws'] ?? 0,
            $this->totals['bf_dtw'] ?? 0,
            $this->totals['bf_stw'] ?? 0,
            $this->totals['bf_rwh'] ?? 0,
            $this->totals['bf_ro'] ?? 0,
            $this->totals['bf_pws'] ?? 0,
            $this->totals['bs_s1'] ?? 0,
            $this->totals['bs_s2'] ?? 0,
            $this->totals['bs_fb'] ?? 0,
            $this->totals['bs_fu'] ?? 0,
            $this->totals['ecd_dtw'] ?? 0,
            $this->totals['ecd_stw'] ?? 0,
            $this->totals['ecd_rwh'] ?? 0,
            $this->totals['ecd_ro'] ?? 0,
            $this->totals['ecd_pws'] ?? 0,
            $this->totals['dis_dtw'] ?? 0,
            $this->totals['dis_stw'] ?? 0,
            $this->totals['dis_rwh'] ?? 0,
            $this->totals['dis_ro'] ?? 0,
            $this->totals['dis_pws'] ?? 0,
            $this->totals['chem_dtw'] ?? 0,
            $this->totals['chem_stw'] ?? 0,
            $this->totals['chem_rwhf'] ?? 0,
            $this->totals['chem_pws'] ?? 0,
            $this->totals['cs_s1'] ?? 0,
            $this->totals['cs_s2'] ?? 0,
            $this->totals['cs_fb'] ?? 0,
            $this->totals['cs_fu'] ?? 0,
        ];

        // Status rows (Q1, Q2, Q3, Q4, etc.)
        foreach ($this->statusRows as $label => $s) {
            $data[] = [
                '',
                $label,
                $s['wp'] ?? 0,
                $s['si'] ?? 0,
                $s['bf'] ?? 0,
                $s['bd'] ?? 0,
                $s['ec'] ?? 0,
                $s['dis'] ?? 0,
                $s['si_dtw'] ?? 0,
                $s['si_stw'] ?? 0,
                $s['si_rwh'] ?? 0,
                $s['si_ro'] ?? 0,
                $s['si_pws'] ?? 0,
                $s['bf_dtw'] ?? 0,
                $s['bf_stw'] ?? 0,
                $s['bf_rwh'] ?? 0,
                $s['bf_ro'] ?? 0,
                $s['bf_pws'] ?? 0,
                $s['bs_s1'] ?? 0,
                $s['bs_s2'] ?? 0,
                $s['bs_fb'] ?? 0,
                $s['bs_fu'] ?? 0,
                $s['ecd_dtw'] ?? 0,
                $s['ecd_stw'] ?? 0,
                $s['ecd_rwh'] ?? 0,
                $s['ecd_ro'] ?? 0,
                $s['ecd_pws'] ?? 0,
                $s['dis_dtw'] ?? 0,
                $s['dis_stw'] ?? 0,
                $s['dis_rwh'] ?? 0,
                $s['dis_ro'] ?? 0,
                $s['dis_pws'] ?? 0,
                $s['chem_dtw'] ?? 0,
                $s['chem_stw'] ?? 0,
                $s['chem_rwhf'] ?? 0,
                $s['chem_pws'] ?? 0,
                $s['cs_s1'] ?? 0,
                $s['cs_s2'] ?? 0,
                $s['cs_fb'] ?? 0,
                $s['cs_fu'] ?? 0,
            ];
        }

        return collect($data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Column mapping: A=1, B=2, ..., AN=40
                // A: Sl, B: Upazila
                // C-H: Total columns (6 cols)
                // I-M: SI Type of Technology (5 cols: DTW,STW,RWH,RO,PWS)
                // N-R: BF Type of Technology (5 cols)
                // S-V: Bacteriological Sampling (4 cols)
                // W-AA: E. coli Detected (5 cols)
                // AB-AF: Disinfection (5 cols)
                // AG-AJ: Chem Type of Technology (4 cols)
                // AK-AN: Chemical Sampling (4 cols)

                // Title rows (1-4)
                $sheet->mergeCells('A1:AN1');
                $sheet->setCellValue('A1', 'HYSAWA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->mergeCells('A2:AN2');
                $sheet->setCellValue('A2', 'SafePani District Project');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->mergeCells('A3:AN3');
                $sheet->setCellValue('A3', 'Weekly Update, Water Quality');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->mergeCells('A4:AN4');
                $reportPeriodText = $this->reportPeriod ?: 'Cumulative Data (All Time)';
                $sheet->setCellValue('A4', 'Reporting Period: ' . $reportPeriodText);
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Row 5 is empty separator

                // Header Row 1 (Row 6) - Main Groups
                $sheet->mergeCells('A6:A8'); // Sl
                $sheet->setCellValue('A6', 'Sl');

                $sheet->mergeCells('B6:B8'); // Upazila
                $sheet->setCellValue('B6', 'Upazila');

                $sheet->mergeCells('C6:C8'); // Total Water Points
                $sheet->setCellValue('C6', 'Total Water Points');

                $sheet->mergeCells('D6:D8'); // Total SI
                $sheet->setCellValue('D6', 'Total Sanitary Inspection (Cumulative)');

                $sheet->mergeCells('E6:E8'); // Total Bact Sampling
                $sheet->setCellValue('E6', 'Total Bacteriological Sampling (Cumulative)');

                $sheet->mergeCells('F6:F8'); // Total Chem Sampling
                $sheet->setCellValue('F6', 'Total Chemical Sampling (Cumulative)');

                $sheet->mergeCells('G6:G8'); // Total E. coli Detected
                $sheet->setCellValue('G6', 'Total E. coli Detected (Cumulative)');

                $sheet->mergeCells('H6:H8'); // Total Disinfection
                $sheet->setCellValue('H6', 'Total Disinfection (Cumulative)');

                // STATUS OF SANITARY INSPECTION (cols I-M, row 6-7)
                $sheet->mergeCells('I6:M6');
                $sheet->setCellValue('I6', 'STATUS OF SANITARY INSPECTION');

                $sheet->mergeCells('I7:M7');
                $sheet->setCellValue('I7', 'Type of Technology Inspected');

                // Row 8 for SI tech columns
                $sheet->setCellValue('I8', 'DTW');
                $sheet->setCellValue('J8', 'STW');
                $sheet->setCellValue('K8', 'RWH');
                $sheet->setCellValue('L8', 'RO');
                $sheet->setCellValue('M8', 'PWS');

                // TYPE OF TECHNOLOGY SAMPLED FOR BACTERIOLOGICAL ANALYSIS (cols N-R, row 6)
                $sheet->mergeCells('N6:R7');
                $sheet->setCellValue('N6', 'TYPE OF TECHNOLOGY SAMPLED FOR BACTERIOLOGICAL ANALYSIS');

                // Row 8 for BF tech columns
                $sheet->setCellValue('N8', 'DTW');
                $sheet->setCellValue('O8', 'STW');
                $sheet->setCellValue('P8', 'RWH');
                $sheet->setCellValue('Q8', 'RO');
                $sheet->setCellValue('R8', 'PWS');

                // STATUS OF SAMPLING, E. COLI DETECTION AND DISINFECTION (cols S-AF, row 6)
                $sheet->mergeCells('S6:AF6');
                $sheet->setCellValue('S6', 'STATUS OF SAMPLING, E. COLI DETECTION AND DISINFECTION OF THIS WEEK');

                // Bacteriological Sampling (cols S-V, row 7)
                $sheet->mergeCells('S7:V7');
                $sheet->setCellValue('S7', 'Bacteriological Sampling');
                $sheet->setCellValue('S8', 'Sample 1');
                $sheet->setCellValue('T8', 'Sample 2 (Duplicate)');
                $sheet->setCellValue('U8', 'Field Blank');
                $sheet->setCellValue('V8', 'Bacteriological Follow-up');

                // E. coli Detected (cols W-AA, row 7)
                $sheet->mergeCells('W7:AA7');
                $sheet->setCellValue('W7', 'E. coli Detected');
                $sheet->setCellValue('W8', 'DTW');
                $sheet->setCellValue('X8', 'STW');
                $sheet->setCellValue('Y8', 'RWH');
                $sheet->setCellValue('Z8', 'RO');
                $sheet->setCellValue('AA8', 'PWS');

                // Disinfection (cols AB-AF, row 7)
                $sheet->mergeCells('AB7:AF7');
                $sheet->setCellValue('AB7', 'Disinfection');
                $sheet->setCellValue('AB8', 'DTW');
                $sheet->setCellValue('AC8', 'STW');
                $sheet->setCellValue('AD8', 'RWH');
                $sheet->setCellValue('AE8', 'RO');
                $sheet->setCellValue('AF8', 'PWS');

                // TYPE OF TECHNOLOGY SAMPLED FOR CHEMICAL ANALYSIS (cols AG-AJ, row 6)
                $sheet->mergeCells('AG6:AJ7');
                $sheet->setCellValue('AG6', 'TYPE OF TECHNOLOGY SAMPLED FOR CHEMICAL ANALYSIS');
                $sheet->setCellValue('AG8', 'DTW');
                $sheet->setCellValue('AH8', 'STW');
                $sheet->setCellValue('AI8', 'RWH with Filter');
                $sheet->setCellValue('AJ8', 'PWS');

                // STATUS OF SAMPLING DETECTION OF THIS WEEK (cols AK-AN, row 6)
                $sheet->mergeCells('AK6:AN6');
                $sheet->setCellValue('AK6', 'STATUS OF SAMPLING DETECTION OF THIS WEEK');

                // Chemical Sampling (cols AK-AN, row 7)
                $sheet->mergeCells('AK7:AN7');
                $sheet->setCellValue('AK7', 'Chemical Sampling');
                $sheet->setCellValue('AK8', 'Sample 1');
                $sheet->setCellValue('AL8', 'Sample 2 (Duplicate)');
                $sheet->setCellValue('AM8', 'Field Blank');
                $sheet->setCellValue('AN8', 'Chemical Follow-up');

                // Style header rows (6-8)
                $headerStyle = [
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'D9EDF7']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];

                $sheet->getStyle('A6:AN8')->applyFromArray($headerStyle);

                // Style main group headers with different color
                $mainHeaderStyle = [
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'B8CCE4']
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];

                $sheet->getStyle('I6:M6')->applyFromArray($mainHeaderStyle);
                $sheet->getStyle('N6:R6')->applyFromArray($mainHeaderStyle);
                $sheet->getStyle('S6:AF6')->applyFromArray($mainHeaderStyle);
                $sheet->getStyle('AG6:AJ6')->applyFromArray($mainHeaderStyle);
                $sheet->getStyle('AK6:AN6')->applyFromArray($mainHeaderStyle);

                // Style data area (from row 9 to last row)
                $dataStyle = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ];

                $sheet->getStyle("A9:AN{$lastRow}")->applyFromArray($dataStyle);

                // Left align Upazila column
                $sheet->getStyle("B9:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Style Total Progress row (row after data rows)
                $totalRowIndex = 9 + count($this->sampleData);
                $sheet->getStyle("A{$totalRowIndex}:AN{$totalRowIndex}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFCC']
                    ]
                ]);

                // Style status rows (Q1, Q2, etc.)
                $statusStartRow = $totalRowIndex + 1;
                if (count($this->statusRows) > 0) {
                    $statusEndRow = $statusStartRow + count($this->statusRows) - 1;
                    $sheet->getStyle("A{$statusStartRow}:AN{$statusEndRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'E2EFDA']
                        ]
                    ]);
                }

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(15);
                for ($col = 'C'; $col <= 'H'; $col++) {
                    $sheet->getColumnDimension($col)->setWidth(12);
                }
                for ($col = 'I'; $col <= 'Z'; $col++) {
                    $sheet->getColumnDimension($col)->setWidth(10);
                }
                $sheet->getColumnDimension('AA')->setWidth(10);
                foreach (['AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'] as $col) {
                    $sheet->getColumnDimension($col)->setWidth(10);
                }

                // Set row heights for header rows
                $sheet->getRowDimension(6)->setRowHeight(30);
                $sheet->getRowDimension(7)->setRowHeight(25);
                $sheet->getRowDimension(8)->setRowHeight(40);

                // Freeze panes (freeze headers)
                $sheet->freezePane('C9');
            }
        ];
    }

    public function title(): string
    {
        return 'Weekly WQ SI Report';
    }
}
