<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanQuestRaw extends Model
{
  protected $table = 'sp_san_quest_raw';
  protected $fillable = ['quest_cat', 'quest_code', 'is_accountable', 'quest_en', 'quest_bn',
      'prob_identify_en', 'prob_identify_bn', 'correct1', 'correct2', 'correct3', 'correct4', 'correct5', 'is_active'];
  protected $guarded = ['id'];

    public function SpSanCorrective()
    {
        return $this->hasMany(SpSanCorrective::class, 'quest_id', 'id');
    }
}
