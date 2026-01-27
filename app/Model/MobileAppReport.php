<?php

namespace App\Model;

use App\Model\MobAppDataList;
use App\Model\MobAppDataListItem;

class MobileAppReport
{
  private $type;
  private $type_val;
  private $starting_date;
  private $ending_date;

  public function __construct($type = null, $val = null)
  {
    $this->type = $type;
    $this->type_val = $val;
  }

  public function generateReport($starting_date = null, $ending_date = null)
  {
    $this->starting_date = $starting_date;
    $this->ending_date = $ending_date;

    $mob = [];

    foreach(range(0,39) as $index)
    {
      $mob[$index] = 0;
    }

    $result = [];

    $result = MobAppDataList::with('items')
      ->where(function($q){
          if($this->type != null && $this->type_val != null)
          {
            $q->where($this->type, $this->type_val);
          }

          if($this->starting_date != "")
          {
            $q->where('created_at', '>=', $this->starting_date);
          }

          if($this->ending_date != "")
          {
            $q->where('created_at', '<=', $this->ending_date);
          }
      })

      ->orderBy('created_at', 'DESC')
      ->get();

    foreach($result as $ans)
    {
      foreach($ans->items as $item)
      {
        $index = $this->getIndex($item);
        $value = $this->getValue($item);

        if(!empty($index) && $index != -1 && !empty($value) )
        {

          \Log::info('Index '.$index. ' : Value '.$value);
          $mob[$index] += $value;
        }
      }
    }
    return $mob;
  }


  public function getIndex(MobAppDataListItem $item)
  {
    $index = -1;

    switch ($item->question_id) {

      case 1:
        return 0;
        break;

      case 2:
        switch ($item->value) {

          case 'Hand Wash':
            return 1;
            break;

          case 'Latrine Maintenance':
            return 2;
            break;

          case 'Garbage Disposal':
            return 3;
            break;

          case 'Menstrual Hygiene':
            return 4;
            break;

          case 'Water Safety':
            return 5;
            break;

          case 'Food Hygiene':
            return 6;
            break;

          case 'Climate Change Awareness':
            return 7;
            break;

          case 'Volunteer Orientation':
            return 8;
            break;

         case 'COVID Session':
            return 9;
            break;

        case 'Infrastructure Related':
            return 10;
            break;
        }
        break;
      case 3:
        switch ($item->value) {
          case 'Community':
            return 9;
            break;

          case 'School':
            return 10;
            break;

          case 'UP':
            return 11;
            break;
        }
        break;

      case 4:
        return 12;
        break;

      case 5:
        return 13;
        break;

      case 6:
        return 14;
        break;

      case 7:
        return 15;
        break;

      case 8:

        switch ($item->value) {
          case 'Water - TW':
            return 16;
            break;

          case 'Water - Sky H':
            return 17;
            break;

          case 'Water - RO':
            return 18;
            break;

          case 'Water - RWH':
            return 19;
            break;

          case 'School Latrine':
            return 20;
            break;

          case 'Public Latrine':
            return 21;
            break;
        }

        break;

      case 9:
        switch ($item->value) {
          case 'Non Functional':
            return 22;
            break;

          case 'Function':
            return 23;
            break;

          case 'Functional with problems':
            return 24;
            break;
        }
        break;

      case 10:
        switch ($item->value) {
          case 'High Saline':
            return 25;
            break;

          case 'High Iron':
            return 26;
            break;

          case 'Platform Broken':
            return 27;
            break;

          case 'Dirty':
            return 28;
            break;

          case 'Maintenance Issue':
            return 29;
            break;
        }
        break;

      case 11:
        switch ($item->value) {
          case 'Major Repair':
            return 30;
            break;

          case 'Minor repair':
            return 31;
            break;

          case 'Awareness':
            return 32;
            break;

          case 'Improved Management':
            return 33;
            break;
        }
        break;
    }
  }

  public function getValue(MobAppDataListItem $item)
  {
    if($item->question_id == 4 || $item->question_id == 5 || $item->question_id == 6 )
    {
      return $item->value;
    }
    return 1;
  }
}
