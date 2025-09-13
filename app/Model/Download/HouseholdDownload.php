<?php

namespace App\Model\Download;

use Illuminate\Http\Request;

class HouseholdDownload
{
  private $waters;
  private $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
    $this->process();
  }

  private function process()
  {
    $request = $this->request;

    $this->waters = \DB::table('household')->where( function($query) use ($request){

      $starting_date = $request->input('starting_date');
      $ending_date = $request->input('ending_date');
      $created_by = $request->input('created_by');
      $ward_no = $request->input('ward_no');
      $village = $request->input('village');

      $region_id = $request->input('region_id');
      $proj_id = $request->input('proj_id');
      $dist_id = $request->input('dist_id');
      $upid = $request->input('upid');
      $unid = $request->input('unid');
      $app_status     =  $request->input('app_status');
      $imp_status     = $request->input('imp_status');

      
      if(!empty($app_status))
        {
          $query->where('app_status', $app_status);
        }

        if(!empty($imp_status))
        {
          $query->where('imp_status', $imp_status);
        }
      if(!empty($region_id))
      {
        $query->where('region_id', $region_id);
      }

      if(!empty($proj_id))
      {
        $query->where('proj_id', $proj_id);
      }

      if(!empty($dist_id))
      {
        $query->where('dist_id', $dist_id);
      }

      if(!empty($upid))
      {
        $query->where('upid', $upid);
      }

      if(!empty($unid))
      {
        $query->where('unid', $unid);
      }


      if(!empty($starting_date))
      {
        $query->where('app_date', '=', $starting_date);
      }

      if(!empty($ending_date))
      {
        $query->where('app_date', '<=', $ending_date);
      }

      if(!empty($created_by))
      {
        $query->where('created_by', $created_by);
      }

      if(!empty($village))
      {
        $query->where('village', 'like', "%$village%");
      }

    })->get();

  }

  public function download()
  {
    $rows = $this->waters;
    \Excel::create('Household Latrine Report '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
          'id',
          'region_id',
          'proj_id',
          'dist_id',
          'upid',
          'unid',
          'cdfno',
          'village',

          'hh_name',
          'father_husband',
          'age',
          'occupation',
          'mobile',
          'economic_status',
          'social_safetynet',
          'male',
          'female',
          'children',
          'disable',
          'ownership_type',
          'latrine_type',
          'latrine_details',
          'total_cost',
          'contribute_amount',
          
          'latitude	',
          'longitude',
          'app_date',
          'app_status',
          'approve_id',
          'imp_status',
          'created_by',
          'updated_by',
          'created_at',
          'updated_at'


          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $sheet->row($rowIndex, [
            $row->id,
            $row->region_id,
            $row->proj_id,
            $row->dist_id,
            $row->upid,
            $row->unid,
            $row->cdfno,
            $row->village,

            $row->hh_name,
            $row->father_husband,
            $row->age,
            $row->occupation,
            $row->mobile,
            $row->economic_status,
            $row->social_safetynet,
            $row->male,
            $row->female,
            $row->children,
            $row->disable,
            $row->ownership_type,
            $row->latrine_type,
            $row->latrine_details,
            $row->total_cost,
            $row->contribute_amount,
            $row->latitude,
            $row->longitude,
            $row->app_date,
            $row->app_status,
            $row->approve_id,
            $row->imp_status,
            $row->created_by,
            $row->updated_by,
            $row->created_at,
            $row->updated_at
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');;
  }
}
