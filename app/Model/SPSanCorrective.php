<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanCorrective extends Model
{
  protected $table = 'sp_san_corrective';
  protected $fillable = ['quest_code', 'quest_id', 'correct_en', 'correct_bn'];
  protected $guarded = ['id'];

    public function SpSanInspectionV2()
    {
        return $this->belongsTo(SPSanQuest::class, 'quest_id', 'id');
    }
}
