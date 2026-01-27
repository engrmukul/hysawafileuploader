<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPRepProblemType extends Model
{
  protected $table = 'sp_rep_problem_type';
  protected $fillable = ['ptitle', 'pdescription', 'pdescription_bn', 'is_active'];
  protected $guarded = ['id'];
}
