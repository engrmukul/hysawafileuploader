<?php

namespace App\Model;

use App\Model\Water;
use Illuminate\Database\Eloquent\Model;

class WaterQualityResult extends Model
{
    protected $table = 'water_quality_results';

    protected $fillable = [
        'water_id',
        'arsenic',
        'fe',
        'mn',
        'cl',
        'ph',
        'pb',
        'zinc',
        'tc',
        'fc',
        'td',
        'turbidity',
        'as_lab',
        'fe_lab',
        'mn_lab',
        'cl_lab',

        'created_at',
        'updated_at',
        'deleted_at',

        'created_by',
        'updated_by',
        'deleted_by',
        'report_date'
    ];

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function water()
    {
        return $this->belongsTo(Water::class, 'water_id', 'id');
    }
}
