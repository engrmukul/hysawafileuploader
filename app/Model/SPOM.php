<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPOM extends Model
{
  protected $table = 'sp_om';
  protected $fillable = ['water_id', 'infrastructure_id', 'quarter', 'year', 'month', 'maintenance_activity', 'maintenance_details',
      'problem_identification', 'notification_time', 'maintenance_time', 'response_time', 'response_time_digit',
      'materials_cost', 'labor_cost', 'transport_cost', 'total_cost', 'days_count', 'days_frac', 'comments', 'problem_id',
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
