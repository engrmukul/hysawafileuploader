<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanAnswerCorr extends Model
{
  protected $table = 'sp_san_answer_corr';
  protected $fillable = ['si_id', 'ans_id', 'corr_id'];
  protected $guarded = ['id'];

    public function SpSanInspectionV2()
    {
        return $this->belongsTo(SPSanInspectionV2::class, 'si_id', 'id');
    }

    public function SpSanAnswer()
    {
        return $this->belongsTo(SPSanAnswer::class, 'ans_id', 'id');
    }

    public function SpSanCorrective()
    {
        return $this->belongsTo(SPSanCorrective::class, 'corr_id', 'id');
    }
}
