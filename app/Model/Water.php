<?php

namespace App\Model;

use App\Model\FinanceData;
use App\Model\Head;
use App\Model\ItemBudget;
use App\Model\SubHead;
use App\Model\WaterQualityResult;
use Illuminate\Database\Eloquent\Model;

class Water extends Model
{
    protected $table = 'tbl_water';

    protected $fillable = [

    'unid',
    'region_id',
    'proj_id',
    'distid',
    'upid',
    'Ward_no',
    'CDF_no',
    'Village',
    'TW_No',
    'App_date',
    'Tend_lot',
    'Technology_Type',
    'Landowner',
    'Caretaker_male',
    'Caretaker_female',
    'HH_benefited',
    'HCHH_benefited',
    'beneficiary_male',
    'beneficiary_female',
    'beneficiary_hardcore',
    'beneficiary_safetynet',
    'wq_Arsenic',
    'wq_fe',
    'wq_mn',
    'wq_cl',
    'wq_ph',
    'wq_pb',
    'wq_zinc',
    'wq_tc',
    'wq_fc',
    'wq_td',
    'wq_turbidity',
    'wq_as_lab',
    'wq_fe_lab',
    'wq_mn_lab',
    'wq_cl_lab',
    'x_coord',
    'y_coord',
    'gpschk',
    'depth',
    'platform',
    'app_status',
    'imp_status',
    'imp_date',
    'year',
    'remarks',
    'CT_trg',
    'MC_trg',

     'com_con_amount',
     'com_con_id',
     'pay_date',
     'bkash_trx',

    'created_at',
    'updated_at',
    'up_approval_at',
    'dist_approval_at',

    'created_by',
    'updated_by',
    'up_approval_by',
    'dist_approval_by',
    ];

    protected $guarded = ['id'];
    public $timestamps = false;

    protected $dates = ['created_at', 'updated_at', 'up_approval_at', 'dist_approval_at',];


    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }


    public function project()
    {
        return $this->belongsTo(Project::class, 'proj_id', 'id');
    }


    public function district()
    {
        return $this->belongsTo(District::class, 'distid', 'id');
    }


    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'upid', 'id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class, 'unid', 'id');
    }

    public function qualityResults()
    {
        return $this->hasMany(WaterQualityResult::class, 'water_id', 'id');
    }
}
