<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class MobSurveyAction extends Model
{
  protected $table = 'mob_survey_action';
  protected $fillable = ['ver_date', 'ver_by', 'ver_func_status', 'prob_type', 'maint_date', 'maint_by', 'maint_func_status',
      'maint_comments', 'maint_cost', 'created_by', 'updated_by', 'created_at', 'updated_at'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
