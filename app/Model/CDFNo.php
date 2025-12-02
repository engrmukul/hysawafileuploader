<?php

namespace App\Model;

use App\Model\District;
use App\Model\Project;
use App\Model\Region;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class CDFNo extends Model
{
  protected $table = 'cdf_nos';
  protected $fillable = [
    'cdf_no',

    'project_id',
    'region_id',
    'district_id',
    'upazila_id',
    'union_id',
    'village',
    'area',
    'updated_by',
    'created_by',
    'type',
    'school_title',
    'owner',
    'word_no'
  ];

  protected $guarded = ['id'];
  public $timestamps = true;
  public $dates = ['created_at', 'updated_at', 'deleted_at'];

  public function region()
  {
    return $this->belongsTo(Region::class, 'region_id', 'region_id');
  }

  public function project()
  {
    return $this->belongsTo(Project::class, 'project_id', 'id');
  }

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