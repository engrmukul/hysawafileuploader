<?php

namespace App\Model;

use App\Model\District;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class TWLatList extends Model
{
  protected $table = 'tw_lat_lists';

  protected $fillable = [
    'type',

    'code',

    'district_id',
    'upazila_id',
    'union_id',
    'village',
    'area',

    'updated_by',
    'created_by',
  ];

  protected $guarded = ['id'];
  public $timestamps = true;
  public $dates = ['created_at', 'updated_at', 'deleted_at'];

  public function district()
  {
    return $this->belongsTo(District::class, 'district_id', 'id');
  }

  public function upazila()
  {
    return $this->belongsTo(Upazila::class, 'upazila_id', 'id');
  }

  public function union()
  {
    return $this->belongsTo(Union::class, 'union_id', 'id');
  }
}
