<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class TrainingTitle extends Model
{
  protected $table = 'trg_title';
  protected $primaryKey = 'id';
  protected $fillable = ['title'];
  protected $guarded = ['id'];
  public $timestamps = false;
}
