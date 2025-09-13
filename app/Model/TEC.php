<?php

namespace App\Model;

use App\Model\Union;
use Illuminate\Database\Eloquent\Model;

class TEC extends Model
{
  protected $table = 'tec';
  protected $fillable = [
    'name',
    'deg',
    'UP_desg',
    'phone',

    'distid',
    'upid',
    'unid',
    'proid',

    'created_by',
    'created_at',
    'updated_by',
    'updated_at'
  ];

  protected $guarded = ['id'];
  public $timestamps = false;

  public function union()
  {
    return $this->belongsTo(Union::class, 'unid', 'id');
  }

  public function project()
  {
    return $this->belongsTo(Project::class, 'proid', 'id');
  }
}
