<?php

namespace App\Model\Search;

use App\Model\SPSanitaryInspection;
use DB;

class SpSanitaryInspectionChart
{
    private $datas;
    private $pagination;

    public function __construct($pagination = false)
    {
        $this->datas;
        $this->pagination = $pagination;
        $this->process();
    }

    private function process()
    {
        $previous_year = date("Y",strtotime("-1 year"));

        $q = DB::table('sp_sanitary_inspection')

                ->leftjoin('sp_infrastructure', 'sp_sanitary_inspection.infrastructure_id', '=', 'sp_infrastructure.id')

                ->select(array('sp_infrastructure.tech_type', 'sp_sanitary_inspection.quarter', 'sp_sanitary_inspection.year',
                    DB::raw('count(*) as group_total')))

                ->groupBy(['sp_infrastructure.tech_type', 'quarter'])

                ->where('sp_sanitary_inspection.infrastructure_id', '!=', NULL)

                ->where('sp_sanitary_inspection.year', $previous_year)

                ->where('sp_infrastructure.is_active', '1');

            $this->datas = $q->get();
    }

    public function get()
    {
        return $this->datas;
    }
}
