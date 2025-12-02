<?php

namespace App\Model;

use App\Model\District;
use App\Model\Head;
use App\Model\Item;
use App\Model\SubHead;
use App\Model\Union;
use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class FinanceData extends Model
{
  protected $table = 'fdata';
  protected $fillable = [
    'head',
    'subhead',
    'item',
    'mode',
    'vou',
    'amount',
    'date',
    'remarks',
    'region_id',
    'distid',
    'upid',
    'unid',
    'trans_type',
    'userid',
    'proid'
  ];
  protected $guarded = ['id'];
  public $timestamps = false;

  public function district()
  {
    return $this->belongsTo(District::class, 'upid');
  }

  public function upazila()
  {
    return $this->belongsTo(Upazila::class, 'upid');
  }

  public function union()
  {
    return $this->belongsTo(Union::class, 'unid');
  }

  public function getHead()
  {
    return $this->belongsTo(Head::class, 'head');
  }

  public function getSubhead()
  {
    return $this->belongsTo(SubHead::class, 'subhead');
  }

  public function getItem()
  {
    return $this->belongsTo(Item::class, 'item');
  }

}
