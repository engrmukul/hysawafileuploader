<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class TrainingAgency extends Model
{
  protected $table = 'trg_agency';
  protected $primaryKey = 'id';

  protected $fillable = [
    'agency_id',
    'region_id',
    'agency_name',
    'address',
    'created_at',
    'created_by',
    'updated_at',
    'updated_by'
  ];

  protected $guarded = ['id'];
  public $timestamps = false;
}
