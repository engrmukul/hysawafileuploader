<?php

namespace App\Model;

use App\Model\District;
use App\Model\Project;
use App\Model\Region;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class UnionStuff extends Model
{
  protected $table = 'union_staff';
  protected $fillable = [
    'name',
    'des',
    'phone',
    'email',

    'unid',
    'upid',
    'distid',
    'proid',

    'created_at',
    'updated_at',
    'created_by',
    'updated_by'
  ];
  protected $guarded = ['id'];
  public $timestamps = false;

  public function union()
  {
    return $this->belongsTo(Union::class, 'unid');
  }

  public function upazila()
  {
    return $this->belongsTo(Upazila::class, 'upid');
  }

  public function district()
  {
    return $this->belongsTo(District::class, 'distid');
  }
  public function project()
  {
    return $this->belongsTo(Project::class, 'proid', 'id');
  }
}
