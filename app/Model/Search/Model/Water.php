<?php

namespace App\Model\Search\Model;

use App\Model\Search\Request\WaterSearchRequest;
use Illuminate\Http\Request;
use Auth;

class Water
{
  private $waters;
  private $request;

  public function __construct(Request $request)
  {

    $waterSearchRequest = new WaterSearchRequest;
    $waterSearchRequest->starting_date = $request->input('starting_date');
    $waterSearchRequest->ending_date = $request->input('ending_date');
    $waterSearchRequest->created_by = $request->input('created_by');
    $waterSearchRequest->village = $request->input('village');
    $waterSearchRequest->water_id = $request->input('water_id');
    $waterSearchRequest->approve_id = $request->input('approve_id');
    $waterSearchRequest->unid = $request->input('unid');
    $waterSearchRequest->proj_id = $request->input('proj_id');
    $waterSearchRequest->region_id = $request->input('region_id');

    $waterSearchRequest->imp_status = $request->input('imp_status');
    $waterSearchRequest->app_status = $request->input('app_status');
    $waterSearchRequest->tech_type = $request->input('tech_type');

    $this->request = $waterSearchRequest;
    $this->process();
  }

  private function process()
  {

    
    $request = $this->request;

    $this->waters = \DB::table('tbl_water')->where( function($query) use($request) {
        $region_id      = $request->region_id;
        $starting_date  = $request->starting_date;
        $ending_date    = $request->ending_date;
        $created_by     = $request->created_by;
        $distid         = $request->distid;
        $upid           = $request->upid;
        $unid           = $request->unid;
        $proj_id       = $request->proj_id;
        $region_id     = $request->region_id;
        $village        = $request->village;
        $approve_id        = $request->approve_id;
        $water_id        = $request->water_id;
        $app_status     = $request->app_status;
        $imp_status     = $request->imp_status;
        $tech_type     = $request->tech_type;
        $Tw_no          = $request->Tw_no;
        $CDF_no         = $request->CDF_no;
        

        $request->date_type = "App_date";
        
      //  dd($distid);
        if(!empty($region_id))
        {
          $query->where('region.region_id', '=', $region_id);
        }

//        if($request->date_type != "")
//        {
//          if(!empty($starting_date))
//          {
//            $query->where("tbl_water.".$request->date_type, '>=', $starting_date);
//          }
//
//          if(!empty($ending_date))
//          {
//            $query->where("tbl_water.".$request->date_type, '<=', $ending_date);
//          }
//        }

        //Searching by approval date ($starting date)
        if(!empty($starting_date))
        {
            $query->where("tbl_water.".$request->date_type, $starting_date);
        }

        if(!empty($created_by))
        {
          $query->where('tbl_water.created_by', $created_by);
        }

        if(!empty($distid))
        {
          $query->where('tbl_water.distid', $distid);
        }

        if(!empty($upid))
        {
          $query->where('tbl_water.upid', $upid);
        }

        if(!empty($unid))
        {
          $query->where('tbl_water.unid', $unid);
        }

        if(!empty($proj_id))
        {
          $query->where('tbl_water.proj_id', $proj_id);
        }

        if(!empty($approve_id))
        {
            $query->where('tbl_water.approve_id', $approve_id);
        }

        if(!empty($water_id))
        {
            $query->where('tbl_water.id', $water_id);
        }

        if(!empty($village))
        {
          $query->where('tbl_water.Village', 'like', "%$village%");
        }

        if(!empty($app_status))
        {
          $query->where('tbl_water.app_status', $app_status);
        }

        if(!empty($imp_status))
        {
          $query->where('tbl_water.imp_status', $imp_status);
        }

        if(!empty($tech_type))
        {
            $query->where('tbl_water.Technology_Type', $tech_type);
        }

        if(!empty($Tw_no))
        {
          $query->where('tbl_water.TW_No', $Tw_no);
        }

        if(!empty($CDF_no))
        {
          $query->where('tbl_water.CDF_no', $CDF_no);
        }
  
  
      })
        ->leftJoin('fdistrict', 'tbl_water.distid', '=', 'fdistrict.id')
        ->leftJoin('fupazila', 'tbl_water.upid', '=', 'fupazila.id')
        ->leftJoin('funion', 'tbl_water.unid', '=', 'funion.id')
        ->leftJoin('region', 'tbl_water.region_id', '=', 'region.region_id')
        ->leftJoin('project', 'tbl_water.proj_id', '=', 'project.id')
        ->select(
          "region.region_name",
          "project.project",
          "fdistrict.distname",
          "fupazila.upname",
          "funion.unname",
          "tbl_water.Ward_no",
          "tbl_water.CDF_no",
          "tbl_water.Village",
          "tbl_water.TW_No",
          "tbl_water.App_date",
          "tbl_water.approve_id",
          "tbl_water.imp_date",
          "tbl_water.Tend_lot",
          "tbl_water.Technology_Type",
          "tbl_water.Landowner",
          "tbl_water.nid",
          "tbl_water.mobile",
          "tbl_water.Caretaker_male",
          "tbl_water.Caretaker_female",
          "tbl_water.HH_benefited",
          "tbl_water.HCHH_benefited",
          "tbl_water.beneficiary_male",
          "tbl_water.beneficiary_female",
          "tbl_water.beneficiary_hardcore",
          "tbl_water.beneficiary_safetynet",
          "tbl_water.wq_Arsenic",
          "tbl_water.wq_fe",
          "tbl_water.wq_mn",
          "tbl_water.wq_cl",
          "tbl_water.wq_ph",
          "tbl_water.wq_pb",
          "tbl_water.wq_zinc",
          "tbl_water.wq_fc",
          "tbl_water.wq_td",
          "tbl_water.wq_turbidity",
          "tbl_water.wq_as_lab",
          "tbl_water.wq_fe_lab",
          "tbl_water.wq_mn_lab",
          "tbl_water.wq_cl_lab",
          "tbl_water.x_coord",
          "tbl_water.y_coord",
          "tbl_water.gpschk",
          "tbl_water.depth",
          "tbl_water.platform",
          "tbl_water.app_status",
          "tbl_water.imp_status",
          "tbl_water.year",
          "tbl_water.remarks",
          "tbl_water.CT_trg",
          "tbl_water.MC_trg",
          "tbl_water.created_by",
          "tbl_water.updated_by",
          "tbl_water.created_at",
          "tbl_water.updated_at",
          "tbl_water.id"
        )
        ->get();

        //query chck----------------------------------------------------
        // echo \DB::table('tbl_water')->where( function($query) use($request) {
        //   $region_id      = $request->region_id;
        //   $starting_date  = $request->starting_date;
        //   $ending_date    = $request->ending_date;
        //   $created_by     = $request->created_by;
        //   $distid         = $request->distid;
        //   $upid           = $request->upid;
        //   $unid           = $request->unid;
        //   $ward_no        = $request->ward_no;
        //   $village        = $request->village;
        //   $app_status     = $request->app_status;
        //   $imp_status     = $request->imp_status;
        //   $Tw_no          = $request->Tw_no;
        //   $CDF_no         = $request->CDF_no;
          
  
        //   $request->date_type = "App_date";
          
        //   if(!empty($region_id))
        //   {
        //     $query->where('region.region_id', '=', $region_id);
        //   }
  
        //   if($request->date_type != "")
        //   {
        //     if(!empty($starting_date))
        //     {
        //       $query->where("tbl_water.".$request->date_type, '>=', $starting_date);
        //     }
  
        //     if(!empty($ending_date))
        //     {
        //       $query->where("tbl_water.".$request->date_type, '<=', $ending_date);
        //     }
        //   }
  
        //   if(!empty($created_by))
        //   {
        //     $query->where('tbl_water.created_by', $created_by);
        //   }
  
        //   if(!empty($distid))
        //   {
        //     $query->where('tbl_water.distid', $distid);
        //   }
  
        //   if(!empty($upid))
        //   {
        //     $query->where('tbl_water.upid', $upid);
        //   }
  
        //   if(!empty($unid))
        //   {
        //     $query->where('tbl_water.unid', $unid);
        //   }
  
        //   if(!empty($ward_no))
        //   {
        //     $query->where('tbl_water.Ward_no', $ward_no);
        //   }
  
        //   if(!empty($village))
        //   {
        //     $query->where('tbl_water.Village', 'like', "%$village%");
        //   }
  
        //   if(!empty($app_status))
        //   {
        //     $query->where('tbl_water.app_status', $app_status);
        //   }
  
        //   if(!empty($imp_status))
        //   {
        //     $query->where('tbl_water.imp_status', $imp_status);
        //   }
  
        //   if(!empty($Tw_no))
        //   {
        //     $query->where('tbl_water.TW_No', $Tw_no);
        //   }
  
        //   if(!empty($CDF_no))
        //   {
        //     $query->where('tbl_water.CDF_no', $CDF_no);
        //   }
    
    
        // })
        //   ->leftJoin('fdistrict', 'tbl_water.distid', '=', 'fdistrict.id')
        //   ->leftJoin('fupazila', 'tbl_water.upid', '=', 'fupazila.id')
        //   ->leftJoin('funion', 'tbl_water.unid', '=', 'funion.id')
        //   ->leftJoin('region', 'tbl_water.region_id', '=', 'region.region_id')
        //   ->leftJoin('project', 'tbl_water.proj_id', '=', 'project.id')
        //   ->select(
        //     "region.region_name",
        //     "project.project",
        //     "fdistrict.distname",
        //     "fupazila.upname",
        //     "funion.unname",
        //     "tbl_water.Ward_no",
        //     "tbl_water.CDF_no",
        //     "tbl_water.Village",
        //     "tbl_water.TW_No",
        //     "tbl_water.App_date",
        //     "tbl_water.Tend_lot",
        //     "tbl_water.Technology_Type",
        //     "tbl_water.Landowner",
        //     "tbl_water.Caretaker_male",
        //     "tbl_water.Caretaker_female",
        //     "tbl_water.HH_benefited",
        //     "tbl_water.HCHH_benefited",
        //     "tbl_water.beneficiary_male",
        //     "tbl_water.beneficiary_female",
        //     "tbl_water.beneficiary_hardcore",
        //     "tbl_water.beneficiary_safetynet",
        //     "tbl_water.wq_Arsenic",
        //     "tbl_water.wq_fe",
        //     "tbl_water.wq_mn",
        //     "tbl_water.wq_cl",
        //     "tbl_water.wq_ph",
        //     "tbl_water.wq_pb",
        //     "tbl_water.wq_zinc",
        //     "tbl_water.wq_fc",
        //     "tbl_water.wq_td",
        //     "tbl_water.wq_turbidity",
        //     "tbl_water.wq_as_lab",
        //     "tbl_water.wq_fe_lab",
        //     "tbl_water.wq_mn_lab",
        //     "tbl_water.wq_cl_lab",
        //     "tbl_water.x_coord",
        //     "tbl_water.y_coord",
        //     "tbl_water.gpschk",
        //     "tbl_water.depth",
        //     "tbl_water.platform",
        //     "tbl_water.app_status",
        //     "tbl_water.imp_status",
        //     "tbl_water.year",
        //     "tbl_water.remarks",
        //     "tbl_water.CT_trg",
        //     "tbl_water.MC_trg",
        //     "tbl_water.created_by",
        //     "tbl_water.updated_by",
        //     "tbl_water.created_at",
        //     "tbl_water.updated_at",
        //     "tbl_water.id"
        //   )
        //   ->toSql();

                  //query chck----------------------------------------------------
  }

  public function get()
  {


    return $this->waters;
  }
}
