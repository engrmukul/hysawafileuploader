<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPProblemVerification extends Model
{
  protected $table = 'sp_problem_verification';
  protected $fillable = ['problem_id', 'eng_main_status', 'eng_main_date', 'eng_main_comment',
      'user_veri_status', 'user_main_date', 'user_veri_comment', 'user_resolve_status', 'prob_identification', 'identification_date',
      'main_cost', 'materials_cost', 'labor_cost', 'transport_cost', 'tank_cleaning_cost', 'electricity_bill', 'close_days',
      'mtype1', 'mtype2', 'mtype3', 'mtype4', 'mtype5', 'mtype6', 'mtype7', 'mtype8',
      'eng_updated_at', 'eng_updated_by', 'user_updated_at', 'user_updated_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['eng_updated_at', 'user_updated_at'];

    public function problemReport()
    {
        return $this->belongsTo(SPProblemReport::class, 'problem_id', 'id');
    }
}
