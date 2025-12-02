<?php

namespace App\Model;

use App\Model\Item;
use App\Model\SubHead;
use Illuminate\Database\Eloquent\Model;

class Head extends Model
{
  protected $table = 'fhead';
  protected $fillable = ['headname'];
  protected $guarded = ['id'];
  public $timestamps = false;
 	
 	public function subheads()
 	{
 		return $this->hasMany(SubHead::class, 'headid', 'id');
 	}

 	public function items()
 	{
 		return $this->hasMany(Item::class, 'headid', 'id');
 	} 
}
