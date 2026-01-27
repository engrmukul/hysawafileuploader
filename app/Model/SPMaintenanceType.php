<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPMaintenanceType extends Model
{
  protected $table = 'sp_maintenance_type';
  protected $fillable = ['mtitle', 'mdescription', 'mdescription_bn', 'is_active'];
  protected $guarded = ['id'];
}
