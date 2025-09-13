<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
  protected $table = 'fdistrict';

  protected $fillable = [
    'region_id', 'distcode', 'distname', 'created_at', 'created_by', 'updated_at', 'updated_by'
  ];
  protected $guarded = ['id'];

  public $timestamps = false;

  public function upazilas()
  {
    return $this->hasMany(Upazila::class, 'disid', 'id');
  }

  public function region()
  {
    return $this->belongsTo(Region::class, 'region_id', 'id');
  }
}
