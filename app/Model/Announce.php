<?php

namespace App\Model;

use App\Model\Union;
use Illuminate\Database\Eloquent\Model;

class Announce extends Model
{
  protected $table = 'p_announce';
  protected $fillable = [
    'memo_no',
    'method',
    'package',
    'd_announce',
    'd_sell',
    'd_receive',
    'd_open',

    'office_open',
    'qualification',
    'specification',
    'price_schedule',

    'estimate',
    's_money',
    'date_com_work',

    's_office_1',
    's_office_2',
    's_office_3',
    'r_office_1',
    'r_office_2',
    'r_office_3',

    'distid',
    'upid',
    'unid',
    'proid',

    'created_by',
    'created_at',
    'updated_by',
    'updated_at'
  ];

  protected $guarded = ['id'];
  public $timestamps = false;

  public function union()
  {
    return $this->belongsTo(Union::class, 'unid', 'id');
  }

  public function project()
  {
    return $this->belongsTo(Project::class, 'proid', 'id');
  }
}
