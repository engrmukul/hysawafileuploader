<?php

namespace App\Model\Search;

use App\Model\SPVolumetric;
use Illuminate\Http\Request;

class SpVolumetricSearch
{
    private $datas;
    private $request;
    private $pagination;

    public function __construct(Request $request, $pagination = false)
    {
        $this->request = $request;
        $this->datas;
        $this->pagination = $pagination;
        $this->process();
    }

    private function process()
    {
        $request = $this->request;

        $q = SPVolumetric::with('infrastructure', 'infrastructure.school')

            ->where('infrastructure_id', '!=', NULL)

            ->where(function($query) use($request) {

                $upid = $request->upazila_id;
                $unid = $request->union_id;
                $institution_type = $request->institution_type;
                $tech_type = $request->tech_type;

                if(!empty($upid) && $upid != 'all' && $upid != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($upid)
                    {
                        $query2->where('sp_school.upid', $upid);
                    });
                }

                if(!empty($unid) && $unid != 'all' && $unid != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($unid)
                    {
                        $query2->where('sp_school.unid', $unid);
                    });
                }

                if(!empty($institution_type) && $institution_type != 'all' && $institution_type != null)
                {
                    $query->whereHas('infrastructure.school', function($query2) use($institution_type)
                    {
                        $query2->where('sp_school.sch_type_edu', $institution_type);
                    });
                }

                if(!empty($tech_type) && $tech_type != 'all' && $tech_type != null)
                {
                    $query->whereHas('infrastructure', function($query2) use($tech_type)
                    {
                        $query2->where('sp_infrastructure.tech_type', $tech_type);
                    });
                }

            })
        //->where('created_by', '=', auth()->user()->id)
        //->groupBy('id');
        ->whereHas('infrastructure', function($query2)
        {
            $query2->where('sp_infrastructure.is_active', '1');
        })
        ->orderBy('reading_date', 'desc');

        if($this->pagination){
            $this->datas = $q->paginate(20);
        }else{
            $this->datas = $q->get();
        }
    }

    public function get()
    {
        return $this->datas;
    }
}
