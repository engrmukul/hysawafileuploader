<?php

namespace App\Model;

use App\Model\Head;
use App\Model\Item;
use App\Model\SubHead;
use App\Model\Union;
use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
  protected $table = 'demand';
  protected $fillable = [
  'head', 
  'subhead', 
  'item', 
  'amount', 
  'date', 
  'remarks', 
  'unid',
  'userid'
  ];
  protected $guarded = ['id'];
  public $timestamps = false;


  public function head()
  {
    return $this->belongsTo(Head::class, 'head'); 
  }

  public function subHead()
  {
    return $this->belongsTo(SubHead::class, 'subhead');
  }

  public function item()
  {
    return $this->belongsTo(Item::class, 'item');
  }

  public function union()
  {
    return $this->belongsTo(Union::class, 'unid');
  }
}
