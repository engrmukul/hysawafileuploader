<?php

namespace App\Model\Download\Superadmin\User;

use App\User;
use Illuminate\Http\Request;

class UserDownload
{
  public function download(Request $request)
  {
     $rows = User::with(
        'roles',
        'region',
        'project',
        'district',
        'upazila',
        'union')->where(function($q) use ($request){

          if($request->has('role_id') && $request->role_id != "")
          {
            $role_id = $request->role_id;
              $q->whereHas('roles', function($query2) use($role_id)
            {
              $query2->where('roles.id', $role_id);
            });

          }

          if($request->has('region_id') && $request->region_id != "")
          {
              $q->where('region_id', $request->region_id);
          }

          if($request->has('project_id') && $request->project_id != "")
          {
              $q->where('proj_id', $request->project_id);
          }

          if($request->has('district_id') && $request->district_id != "")
          {
              $q->where('distid', $request->district_id);
          }

          if($request->has('upazila_id') && $request->upazila_id != "")
          {
              $q->where('upid', $request->upazila_id);
          }

          if($request->has('union_id') && $request->union_id != "")
          {
              $q->where('unid', $request->union_id);
          }

          if($request->has('name') && $request->name != "")
          {
              $q->where('name', 'LIKE', '%'.$request->name.'%');
          }
        })->get();

    \Excel::create('User-'.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {
        $sheet->setOrientation('landscape');
        $sheet->row(1, array(
            'Name',
            'Email',
            'Role',
            'Region',

            'Project',
            'District',
            'Upazila',
            'Union',

            'Status'
          )
        );

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $role = "";
          if($row->roles()->count())
          {
            $role = $row->roles->first()->display_name;
          }

          $region = "";
          if($row->region_id != "")
          {
            $region = $row->region->region_name;
          }

          $project = "";
          if($row->proj_id != "")
          {
            $project = $row->project->project;
          }

          $district = "";
          if($row->distid != "")
          {
            $district = $row->district->distname;
          }

          $upazila = "";
          if($row->upid != "" && isset($row->upazila->upname))
          {
            $upazila = $row->upazila->upname;
          }

          $union = "";
          if($row->unid != "" && isset($row->union->unname))
          {
            $union = $row->union->unname;
          }

          $sheet->row($rowIndex, [
            $row->name ,
            $row->email ,
            $role ,
            $region ,

            $project ,
            $district ,
            $upazila ,
            $union ,

            $row->status ,

          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}
