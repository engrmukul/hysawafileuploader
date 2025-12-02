<?php

namespace App\Model;

use App\Model\District;
use App\Model\FinanceData;
use App\Model\Head;
use App\Model\ItemBudget;
use App\Model\SubHead;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
  protected $table = 'region';
  protected $fillable = ['region_id', 'region_name'];
  protected $guarded = ['id'];
  public $timestamps = false;

  public function districts()
  {
    return $this->hasMany(District::class, 'region_id', 'id');
  }

}
