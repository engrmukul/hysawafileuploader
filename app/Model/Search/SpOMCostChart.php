<?php

namespace App\Model\Search;

use App\Model\SPSanitaryInspection;
use DB;

class SpOMCostChart
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

        $q = DB::table('sp_om')

                ->leftjoin('sp_infrastructure', 'sp_om.infrastructure_id', '=', 'sp_infrastructure.id')

                ->select(array('sp_infrastructure.tech_type', 'sp_infrastructure.is_active', 'sp_om.quarter',
                    'sp_om.year', DB::raw('sum(sp_om.total_cost) as group_total')))

                ->groupBy(['sp_om.quarter', 'sp_infrastructure.tech_type'])

                ->where('sp_om.infrastructure_id', '!=', NULL)

                ->where('sp_infrastructure.is_active', '1');

            $this->datas = $q->get();
    }

    public function get()
    {
        return $this->datas;
    }
}
