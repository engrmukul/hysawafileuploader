<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanAnswerObs extends Model
{
  protected $table = 'sp_san_answer_obs';
  protected $fillable = ['wat_user_q1', 'wat_user_q2', 'wat_user_q3', 'wat_user_q4', 'wat_user_q5', 'wat_user_q6', 'phy_obs_q1', 'phy_obs_q2', 'phy_obs_q3'];
  protected $guarded = ['id'];

    public function SpSanInspectionV2()
    {
        return $this->belongsTo(SPSanInspectionV2::class, 'id', 'observable_id');
    }
}
