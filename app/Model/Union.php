<?php

namespace App\Model;

use App\Model\Demand;
use App\Model\FinanceData;
use App\Model\ItemBudget;
use App\Model\Project;
use App\Model\Region;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class Union extends Model
{
  protected $table = 'funion';
  protected $fillable = [
    'uncode', 'distid', 'upid', 'proid', 'unname', 'unname_bn', 'region_id'
  ];
  protected $guarded = ['id'];
  public $timestamps = false;

  public function upazila()
  {
    return $this->belongsTo(Upazila::class, 'upid');
  }

  public function financeDatas()
  {
    return $this->hasMany(FinanceData::class, 'unid', 'id');
  }

  public function itemBudets()
  {
    return $this->hasMany(ItemBudget::class, 'ubid', 'id');
  }

  public function demands()
  {
    return $this->hasMany(Demand::class, 'unid', 'id');
  }

  public function project()
  {
    return $this->belongsTo(Project::class, 'proid', 'id');
  }

  public function region()
  {
    return $this->belongsTo(Region::class, 'region_id', 'id');
  }
}
