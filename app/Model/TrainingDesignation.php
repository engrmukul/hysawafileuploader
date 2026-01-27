<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class TrainingDesignation extends Model
{
  protected $table = 'trg_desg';
  protected $primaryKey = 'id';

  protected $fillable = [
    'Desg_id',

    'desg',
    'created_at',
    'created_by',
    'updated_at',
    'updated_by'
  ];

  protected $guarded = ['id'];
  public $timestamps = false;
}
