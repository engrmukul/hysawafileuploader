<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanQuestObs extends Model
{
  protected $table = 'sp_san_quest_obs';
  protected $fillable = ['quest_en', 'quest_bn'];
  protected $guarded = ['id'];

}
