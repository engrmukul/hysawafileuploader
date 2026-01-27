<?php

namespace App\Model;

use App\Model\District;
use App\Model\Item;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class ItemBudget extends Model
{
  protected $table = 'item_budget';
  protected $fillable = [
  'itemid', 
  'budget', 
  's_year', 
  'e_year', 
  'distid', 
  'upid', 
  'ubid', 
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
    return $this->belongsTo(Union::class, 'ubid');
  }

  public function upazila()
  {
    return $this->belongsTo(Upazila::class, 'upid'); 
  }

  public function district()
  {
    return $this->belongsTo(District::class, 'distid');
  }

  public function item()
  {
    return $this->belongsTo(Item::class, 'itemid');
  }
}
