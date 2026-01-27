<?php

namespace App\Model;

use App\Model\FinanceData;
use App\Model\Head;
use App\Model\ItemBudget;
use App\Model\SubHead;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $table = 'fitem';
  protected $fillable = ['headid', 'subid', 'itemname'];
  protected $guarded = ['id'];
  public $timestamps = false;

  public function head()
  {
  	return $this->belongsTo(Head::class, 'headid');
  }

  public function subHead()
  {
  	return $this->belongsTo(SubHead::class, 'subid');
  }

  public function itemBudets()
  {
    return $this->hasMany(ItemBudget::class, 'itemid', 'id');
  }

  public function financeDatas()
  {
    return $this->hasMany(FinanceData::class, 'item', 'id');
  }
}
