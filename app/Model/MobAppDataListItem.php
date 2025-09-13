<?php

namespace App\Model;

use App\Model\MobAppDataList;
use App\Model\MobAppQuestionLists;
use App\Model\QualityAppQuestionLists;
use Illuminate\Database\Eloquent\Model;

class MobAppDataListItem extends Model
{
  protected $table = 'mobile_app_data_list_items';

  public function mobAppDataList()
  {
    return $this->belongsTo(MobAppDataList::class, 'mobile_app_data_list_id', 'id');
  }

  public function question()
  {
    return $this->belongsTo(MobAppQuestionLists::class, 'question_id', 'id');
  }

  public function qualityQuestion()
  {
    return $this->belongsTo(QualityAppQuestionLists::class, 'question_id', 'id');
  }
}
