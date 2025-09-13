<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class WaterQualityOthers extends Model
{
  protected $table = 'water_quality_others';
  protected $fillable = ['tech_type', 'proj_id', 'dist', 'up', 'un', 'vill', 'ward', 'cdf', 'owner', 'wq_as', 'wq_cl', 'wq_mn', 'wq_fe',
      'wq_tc', 'wq_fc', 'lat', 'lon', 'img1', 'img2', 'img3', 'inserted_at', 'test_date', 'created_by', 'updated_by', 'created_at', 'updated_at'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at', 'updated_at'];


    public function project()
    {
        return $this->belongsTo(Project::class, 'proj_id', 'id');
    }


    public function district()
    {
        return $this->belongsTo(District::class, 'distid', 'id');
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'up', 'id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class, 'un', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
