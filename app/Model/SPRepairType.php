<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPRepairType extends Model
{
  protected $table = 'sp_repair_type';
  protected $fillable = ['rtitle', 'water_type', 'rdescription', 'rdescription_bn', 'cost', 'is_active'];
  protected $guarded = ['id'];
}
