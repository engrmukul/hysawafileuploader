<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
  protected $table = 'bank';
  protected $fillable = ['balance', 'date', 'remarks', 'unid', 'userid', 'created_by', 'created_at', 'updated_by', 'updated_at'];
  protected $guarded = ['id'];
  public $timestamps = false;
}
