<?php

namespace App\Model\Download;

use App\Model\SPSchool;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class UsersSheetExport implements FromCollection, WithHeadings, WithEvents, WithTitle
{
    protected $users;
    protected $userOrderRows;
    public $userRowMap = []; // school_id → row

    public function __construct($userOrderRows)
    {
        $this->users = SPSchool::with(['district','upazila','union', 'assesor'])
            ->where('distid', 7) // optional filter
            ->orderBy('id')
            ->get()
            ->map(function ($item) {
            return [
                'id' => $item->id,
                'institution_id' => $item->institution_id,
                'sch_name_en' => $item->sch_name_en ?? null,
                'institution_type' => $item->sch_type_edu ?? '',
                'estab_year' => $item->estab_year  ?? '',
                'owner_type' => $item->owner_type  ?? '',
                'union' => ucfirst(strtolower($item->union->unname)),
                'upazila' => ucfirst(strtolower($item->upazila->upname)),
                'district' => ucfirst(strtolower($item->district->distname)),
                'village' => $item->vill ?? '',
                'boy_student'     => $item->boy_student     ?? '',
                'girl_student'    => $item->girl_student    ?? '',
                'disabled_boys'   => $item->disabled_boys   ?? '',
                'disabled_girls'  => $item->disabled_girls  ?? '',
                'tot_student'     => $item->tot_student     ?? '',
                'male_staff'      => $item->male_staff      ?? '',
                'female_staff'    => $item->female_staff    ?? '',
                'tot_staff'       => $item->tot_staff       ?? '',
                'water_points'    => $item->water_counts    ?? '',
                'drinking_sources' => $item->drinking_counts ?? '',
                'nearby_families' => $item->nearby_families ?? '',
                'contact_name'    => $item->contact_name    ?? '',
                'contact_position'=> $item->contact_position?? '',
                'contact_phone'   => $item->contact_phone   ?? '',
                'headmaster_chcp' => $item->headmaster_chcp ?? '',
                'head_phone'      => $item->head_phone      ?? '',
                'lat'             => $item->lat             ?? '',
                'lon'             => $item->lon             ?? '',
                'base_date'             => $item->base_date    ?? '',
                'remark'             => $item->remark   ?? '',
                'assesor'             => $item->assesor->name   ?? '',
                'image' => '=HYPERLINK("http://hysawa.com/mis/public/upload/sp_satkhira_inst/'.$item->img9.'", "Image Link")'  ?? '',
            ];
    });

        $this->userOrderRows = $userOrderRows;
    }

    public function collection()
    {
        return $this->users;
    }

    public function prepareRowMap()
    {
        $row = 2;
        foreach ($this->users as $user) {
            $this->userRowMap[$user['id']] = $row;
            $row++;
        }
    }

    public function headings(): array
    {
        return ['ID',
            'Institution ID',
            'Institution Name',
            'Establishment Year',
            'Ownership Type',
            'Institution Type',
            'Union',
            'Upazila',
            'District',
            'Village',
            'Boys Registered',
            'Girls Registered',
            'Disabled boys',
            'Disabled girls',
            'Total Students',
            'Male Staff',
            'Female Staff',
            'Total Staffs',
            'Waterpoints (Inside premises)',
            'Drinking Water Sources',
            'Nearby Family Users',
            'Respondent Name',
            'Respondent Designation',
            'Respondent Mobile',
            'Headmaster/ CHCP',
            'Headmaster/ CHCP Mobile',
            'Latitude',
            'Longitude',
            'Baseline Date',
            'Comments',
            'Baseline Assessor',
            'Image'];
    }

    public function title(): string
    {
        return 'Institutions';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ✅ Apply AutoFilter
                $highestCol = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $column = 'AF'; // target column

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

                foreach ($this->users as $index => $user) {
                    $row = $index + 2; // row 1 = heading
                    $cell = "A{$row}"; // ID column

                    if (isset($this->userOrderRows[$user['id']])) {
                        $targetRow = $this->userOrderRows[$user['id']];
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
            },
        ];
    }
}

