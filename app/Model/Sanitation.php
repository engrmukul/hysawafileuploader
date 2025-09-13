<?php

namespace App\Model;

use App\Model\District;
use App\Model\FinanceData;
use App\Model\Head;
use App\Model\ItemBudget;
use App\Model\Project;
use App\Model\Region;
use App\Model\SubHead;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class Sanitation extends Model
{
    protected $table = 'sanitation';

    protected $fillable = [

        'region_id',
        'proj_id',
        'dist_id',
        'upid',
        'unid',

        'cdfno',
        'latrineno',
        'cons_type',
        'village',
        'maintype',
        'subtype',
        'name',
        'malechamber',
        'femalechamber',
        'overheadtank',
        'motorpump',
        'watersource',
        'sockwell',
        'seotictank',
        'tapoutside',
        'longitude',
        'latitude',
        'male_ben',
        'fem_ben',
        'child_ben',
        'disb_bene',
        'caretakername',
        'caretakerphone',
        'ch_comittee',
        'ch_com_tel',

        'app_date',

        'app_status',
        'imp_status',

        'imp_date',
        'com_con_amount',
        'com_con_id',
        'pay_date',
        'bkash_trx',

        'created_by',
        'updated_by',
        'up_approval_by',
        'dist_approval_by',

        'created_at',
        'updated_at',
        'up_approval_at',
        'dist_approval_at',
      ];

    protected $guarded = ['id'];
    public $timestamps = false;

    protected $dates = ['created_at', 'updated_at','up_approval_at','dist_approval_at'];

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
        return $this->belongsTo(District::class, 'dist_id', 'id');
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'upid', 'id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class, 'unid', 'id');
    }


}
