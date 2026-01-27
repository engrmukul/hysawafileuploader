<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MobAppQuestionLists extends Model
{
  protected $table = 'mob_app_question_lists';

  protected $fillable = [
    'id',
    'type',
    'title',
    'input_type',
    'options'
  ];
  protected $guarded = [
   'created_at', 'updated_at'
  ];
}

