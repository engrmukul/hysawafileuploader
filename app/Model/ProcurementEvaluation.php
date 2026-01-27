<?php

namespace App\Model;

use App\Model\Union;
use Illuminate\Database\Eloquent\Model;

class ProcurementEvaluation extends Model
{
  protected $table = 'comp_sheet';
  protected $fillable = [
    'package',
    'con_name',
    'con_add',
    'b_detail',

    'amount',
    'quate',
    'quate_perc',
    'rate',

    'm_receipt',
    'security',
    'l_asset',
    'signed',

    'r_status',
    'rank',
    'noa',
    'noa_date',

    'con_date',
    'con_status',
    'remarks',

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
