<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSampleCollection extends Model
{
  protected $table = 'sp_sample_collection';
  protected $fillable = ['water_id', 'infrastructure_id', 'quarter', 'year', 'sample_id', 'sample_no', 'sample_cat', 'round',
                          'sample_date', 'phy_test_date', 'start_time', 'sample_time', 'elevation', 'weather', 'color', 'decon_process', 'comments',
      'is_notified', 'is_tested', 'result_verified', 'sms_sent', 'sms_date', 'lat', 'lon', 'disinfect_status', 'disinfect_date',
      'created_at', 'updated_at', 'created_by', 'updated_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at', 'updated_at'];


    public function infrastructure()
    {
        return $this->belongsTo(SPInfrastructure::class, 'infrastructure_id', 'id');
    }


    public function water()
    {
        return $this->belongsTo(SPInfrastructure::class, 'water_id', 'water_id');
    }
}
