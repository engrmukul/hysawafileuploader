<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPImageDownloadSch extends Model
{
  protected $table = '921schools';
  protected $fillable = ['Institution_ID', 'School Photo Link'];
  protected $guarded = ['id'];
}
