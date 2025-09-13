<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanQuest extends Model
{
  protected $table = 'sp_san_quest';
  protected $fillable = ['quest_cat', 'quest_code', 'is_accountable', 'quest_en', 'quest_bn',
      'prob_identify_en', 'prob_identify_bn', 'correct1', 'correct2', 'correct3', 'correct4', 'correct5', 'is_active'];
  protected $guarded = ['id'];

    public function SpSanCorrective()
    {
        return $this->hasMany(SpSanCorrective::class, 'quest_id', 'id');
    }

    public function Corrective1()
    {
        return $this->belongsTo(SpSanCorrective::class, 'correct1', 'id');
    }

    public function Corrective2()
    {
        return $this->belongsTo(SpSanCorrective::class, 'correct2', 'id');
    }

    public function Corrective3()
    {
        return $this->belongsTo(SpSanCorrective::class, 'correct3', 'id');
    }

    public function Corrective4()
    {
        return $this->belongsTo(SpSanCorrective::class, 'correct4', 'id');
    }

    public function Corrective5()
    {
        return $this->belongsTo(SpSanCorrective::class, 'correct5', 'id');
    }
}
