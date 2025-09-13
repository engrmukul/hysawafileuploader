<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
  protected $table = 'tbl_training';
  protected $primaryKey = 'TrgCode';
  protected $fillable = [
    'region_id',
    'title_id',
    'TrgTitle',
    'TrgVenue',
    'TrgFrom',
    'TrgTo',
    'TrgBatchNo',
    'TrgParticipantsType',
    'TrgParticipantNo',
    'TrgStatus',
    'TrgFacilitators',
    'Agency',
    'Organizedby',
    'created_by',
    'created_at',
    'updated_by',
    'updated_at'
  ];

  protected $guarded = ['TrgCode'];
  public $timestamps = false;
}
