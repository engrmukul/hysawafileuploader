<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\AfterSheet;

class AllInstitutionExportKhl implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell,
    WithTitle

{

    private $sl = 0;

    public function startCell(): string
    {
        return 'A3';
    }

    public function collection()
    {
        return DB::table('sp_school')
            ->leftJoin('fdistrict', 'sp_school.distid', '=', 'fdistrict.id')
            ->leftJoin('fupazila', 'sp_school.upid', '=', 'fupazila.id')
            ->leftJoin('funion', 'sp_school.unid', '=', 'funion.id')
            ->leftJoin('sp_infrastructure', function ($join) {
                $join->on('sp_school.id', '=', 'sp_infrastructure.school_id')
                    ->where('sp_infrastructure.is_active', 1);
            })
            ->where('sp_school.distid', 6)
            ->orderBy('sp_school.distid', 'ASC')
            ->orderBy('sp_school.institution_id', 'ASC')
            ->select(
                'sp_school.institution_id',
                'fdistrict.distname',
                'fupazila.upname',
                'funion.unname',
                'sp_school.vill',
                'sp_school.sch_name_en',
                'sp_school.estab_year',
                'sp_school.owner_type',
                'sp_school.sch_type_edu',
                'sp_school.boy_student',
                'sp_school.girl_student',
                'sp_school.tot_student',
                'sp_school.male_staff',
                'sp_school.female_staff',
                'sp_school.tot_staff',
                'sp_school.daily_visitor',
                'sp_school.catchm_patients',
                'sp_school.water_counts',
                'sp_school.drinking_counts',
                'sp_school.func_counts',
                'sp_school.non_func_counts',
                'sp_school.under_const_counts',
                'sp_school.onboard_date as school_onboard',
                'sp_school.is_active',
                'sp_infrastructure.water_id',
                'sp_infrastructure.tech_type',
                'sp_infrastructure.install_year',
                'sp_infrastructure.install_by',
                'sp_infrastructure.onboard_date as infra_onboard',
                'sp_school.contact_name',
                'sp_school.contact_position',
                'sp_school.contact_phone',
                'sp_school.headmaster_chcp',
                'sp_school.head_phone',
                'sp_school.lat',
                'sp_school.lon'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'SL',
            'Institution ID',
            'District',
            'Upazila',
            'Union',
            'Village',
            'Institution Name',
            'Establish Year',
            'Owner Type',
            'School Type',
            'Boys',
            'Girls',
            'Total Students',
            'Male Staff',
            'Female Staff',
            'Total Staff',
            'Daily Visitor',
            'Catchment Patients',
            'Water Points',
            'Drinking Points',
            'Functional',
            'Non Functional',
            'Under Construction',
            'Institution Onboard',
            'Water ID',
            'Technology',
            'Install Year',
            'Installed By',
            'Waterpoint Onboard',
            'Respondent Name',
            'Respondent Position',
            'Respondent Phone',
            'Headmaster/CHCP',
            'Headmaster Phone',
            'Latitude',
            'Longitude',
            'Actively Managed?'
        ];
    }

    public function map($row): array
    {
        $this->sl++;

        return [
            $this->sl,
            $row->institution_id,
            $row->distname,
            $row->upname,
            $row->unname,
            $row->vill,
            $row->sch_name_en,
            $row->estab_year,
            $row->owner_type,
            $row->sch_type_edu,
            $row->boy_student,
            $row->girl_student,
            $row->tot_student,
            $row->male_staff,
            $row->female_staff,
            $row->tot_staff,
            $row->daily_visitor,
            $row->catchm_patients,
            $row->water_counts,
            $row->drinking_counts,
            $row->func_counts,
            $row->non_func_counts,
            $row->under_const_counts,
            $row->school_onboard,
            $row->water_id,
            $row->tech_type,
            $row->install_year,
            $row->install_by,
            $row->infra_onboard,
            $row->contact_name,
            $row->contact_position,
            $row->contact_phone,
            $row->headmaster_chcp,
            $row->head_phone,
            $row->lat,
            $row->lon,
            $row->is_active == 1 ? 'Yes' : 'No'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;
                $lastColumn = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                /* Title */
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'SafePani All Institutions List');

                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                /* Header Styling */
                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
                    'font' => ['bold' => true],
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
                ]);

                /* Auto Filter */
                $sheet->setAutoFilter("A3:{$lastColumn}3");

                /* Borders */
                $sheet->getStyle("A3:{$lastColumn}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                /* Conditional Formatting: Actively Managed */
                for ($i = 4; $i <= $lastRow; $i++) {
                    $cell = $sheet->getCell("{$lastColumn}{$i}")->getValue();

                    if ($cell === 'Yes') {
                        $sheet->getStyle("{$lastColumn}{$i}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['argb' => '008000']]
                        ]);
                    } else {
                        $sheet->getStyle("{$lastColumn}{$i}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['argb' => 'FF0000']]
                        ]);
                    }
                }

                /* Freeze header row */
                $sheet->freezePane('A4');

                /* Vertical alignment */
                $sheet->getStyle("A3:{$lastColumn}{$lastRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                /* Center align last column (Actively Managed?) */
                $sheet->getStyle("{$lastColumn}4:{$lastColumn}{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getColumnDimension('F')->setWidth(14); // Village
                $sheet->getColumnDimension('G')->setWidth(20); // Institution Name
            }
        ];
    }

    public function title(): string
    {
        return 'All Institutions - Khulna';
    }
}
