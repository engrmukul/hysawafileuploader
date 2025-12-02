<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $table = 'project';
  protected $fillable = ['project'];
  protected $guarded = ['id'];
  public $timestamps = false;
}
