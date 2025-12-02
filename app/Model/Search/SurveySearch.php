<?php

namespace App\Model\Search;

use Illuminate\Http\Request;
use DB;

class SurveySearch
{
  private $datas;
  private $data_array;
  private $pagination;

  public function __construct($data_array, $pagination = false)
  {
    $this->data_array = $data_array;
    $this->datas;
    $this->pagination = $pagination;
    $this->process();
  }

  private function process()
  {
    $data_array = $this->data_array;

    $q =  DB::table('mob_survey')

        ->leftjoin('mob_survey_action', 'mob_survey.action_id', '=', 'mob_survey_action.id')

        ->select('mob_survey.*', 'mob_survey_action.*', 'mob_survey.id as survey_id')

        ->orderBy('date', 'DESC')

        ->where(function($query) use($data_array) {

      $distid = $data_array[0];
      $upid = $data_array[1];
      $unid = $data_array[2];
      $starting_date = $data_array[3];
      $ending_date = $data_array[4];
      $col_name = $data_array[5];
      $col_val = $data_array[6];

      if(!empty($col_name) && !empty($col_val))
      {
          if($col_name == 'maint_status'){
              $query->where($col_name, '!=', null);
          } else if($col_name == 'veri_status'){
              if($col_val == 'yes'){
                  $query->where($col_name, '!=', null);
              } else {
                  $query->where($col_name, '=', $col_val);
              }
          } else {
              $query->where($col_name, '=', $col_val);
          }
      }

      if($starting_date != "" && $ending_date != "")
      {
        $query->whereDate('date', '>=', $starting_date)->whereDate('date', '<=', $ending_date);
      }

      if(!empty($distid) && $distid != 'all' && $distid != null)
      {
        $query->where('distid', '=', $distid);
      }

      if(!empty($upid) && $upid != 'all' && $upid != null)
      {
        $query->where('upid', '=', $upid);
      }

      if(!empty($unid) && $unid != 'all' && $unid != null)
      {
        $query->where('unid', '=', $unid);
      }

    });
    //->where('created_by', '=', auth()->user()->id)
    //->groupBy('id');

    if($this->pagination){
      $this->datas = $q->paginate(15);
    }else{
      $this->datas = $q->get();
    }
  }

  public function get()
  {
    return $this->datas;
  }
}
