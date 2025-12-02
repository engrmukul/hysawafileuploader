<?php

namespace App\Model;

use App\Model\District;
use App\Model\Union;
use Illuminate\Database\Eloquent\Model;

class Upazila extends Model
{
  protected $table = 'fupazila';
  protected $fillable = [
    'upcode', 'disid', 'upname', 'upname_bn'
  ];
  protected $guarded = ['id'];
  public $timestamps = false;


  public function district()
  {
    return $this->belongsTo(District::class, 'disid');
  }

  public function unions()
  {
    return $this->hasMany(Union::class, 'upid', 'id');
  }
}
