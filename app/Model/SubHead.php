<?php

namespace App\Model;

use App\Model\Head;
use App\Model\Item;
use Illuminate\Database\Eloquent\Model;

class SubHead extends Model
{
  protected $table = 'fsubhead';
  protected $fillable = ['headid', 'sname'];
  protected $guarded = ['id'];
  public $timestamps = false;
  
  public function head()
  {
  	return $this->belongsTo(Head::class, 'headid');
  }

  public function items()
  {
  	return $this->hasMany(Item::class, 'subid', 'id');
  }
}
