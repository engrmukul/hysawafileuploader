<?php

namespace App\Model;

use App\Model\MobAppDataListItem;
use App\Model\Project;
use App\Model\Region;
use App\Model\Union;
use App\User;
use Illuminate\Database\Eloquent\Model;

class MobAppDataList extends Model
{
  protected $table = 'mob_app_data_list';

  // protected $fillable = [

  // ];

  protected $guarded = [
    'id','created_at', 'updated_at'
  ];

  public function project()
  {
    return $this->belongsTo(Project::class, 'proj_id', 'id');
  }

  public function region()
  {
    return $this->belongsTo(Region::class, 'region_id', 'region_id');
  }

  public function district()
  {
    return $this->belongsTo(District::class, 'distid', 'id');
  }

  public function upazila()
  {
    return $this->belongsTo(Upazila::class, 'upid', 'id');
  }

  public function union()
  {
    return $this->belongsTo(Union::class, 'unid', 'id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }

  public function events()
   {
    return $this->belongsTo(MobAppDataEvents::class, 'id', 'mob_app_list_id');
   }

  public function items()
  {
    return $this->hasMany(MobAppDataListItem::class, 'mobile_app_data_list_id', 'id');
  }


  public static function getQuestion($id)
  {
    if($id == 1){
      return "Q. CDF No/School No";
    }elseif($id == 2){
      return "Q. CDF No/School No <br/> A.Hand Wash, Latrine Maintenance, Garbage Disposal, Menstrual Hygiene, Water Safety, Food Hygiene, Climate Change Awareness, Volunteer Orientation, COVID Session, Infrastructure Related";
    }elseif($id == 3){
      return "Q. Location <br/>A. Community, School, UP";
    }elseif($id == 4){
      return "Q. Nos of Men/Boy";
    }elseif($id == 5){
      return "Q. Nos of Women/Girl";
    }elseif($id == 6){
      return "Q. Nos of Disabled";
    }elseif($id == 7){
      return "Q. ID No";
    }elseif($id == 8){
      return "Q. Type <br/>A. Water - TW, Water - Sky H, Water - RO, Water - RWH, School Latrine, Public Latrine";
    }elseif($id == 9){
      return "Q. Functionality <br/>A. Non Functional, Function, Functional with problems";
    }elseif($id == 10){
      return "Q. Problem Type <br/>A. High Saline, High Iron, Platform Broken, Dirty, Maintenance Issue";
    }elseif($id == 11){
      return "Q. Problem Type <br/>A. Major Repair, Minor repair, Awareness, Improved Management";
    }
  }
}
