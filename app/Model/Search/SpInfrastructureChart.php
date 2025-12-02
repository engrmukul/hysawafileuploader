<?php

namespace App\Model\Search;

use App\Model\SPSanitaryInspection;
use DB;

class SpInfrastructureChart
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

        $q =  DB::table('sp_infrastructure')

            ->select(array('sp_infrastructure.*', DB::raw('count(*) as group_total')))

            ->where('is_active', '1')

            ->groupBy('tech_type');

            $this->datas = $q->get();
    }

    public function get()
    {
        return $this->datas;
    }
}
