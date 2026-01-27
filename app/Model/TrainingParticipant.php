<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
  protected $table = 'tbl_trg_participants';
  protected $primaryKey = 'participant_id';
  protected $fillable = [
    'region_id',
    'participant_name',
    'district_id',
    'upazila_id',
    'union_id',
    'designation',
    'mobile_no',
    'email_address',
    'remarks',
    'TrgCode',
    'title_id',
    'created_by',
    'created_at',
    'updated_by',
    'updated_at'
  ];

  protected $guarded = ['participant_id'];
  public $timestamps = false;
}
