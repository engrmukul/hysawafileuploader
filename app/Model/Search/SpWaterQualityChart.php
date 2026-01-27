<?php

namespace App\Model\Search;

use App\Model\SPSanitaryInspection;
use DB;

class SpWaterQualityChart
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

        $q = DB::table('sp_water_quality')

                ->leftjoin('sp_infrastructure', 'sp_water_quality.infrastructure_id', '=', 'sp_infrastructure.id')

                ->select(array('sp_infrastructure.tech_type', 'sp_water_quality.quarter', 'sp_infrastructure.is_active',
                    'sp_water_quality.year', 'sp_water_quality.risk_level', DB::raw('count(*) as group_total')))

                ->groupBy(['sp_infrastructure.tech_type', 'risk_level'])

                ->where('sp_water_quality.infrastructure_id', '!=', NULL)

                ->where('sp_infrastructure.is_active', '1');

            $this->datas = $q->get();
    }

    public function get()
    {
        return $this->datas;
    }
}
