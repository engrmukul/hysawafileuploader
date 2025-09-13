<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPRenovation extends Model
{
  protected $table = 'sp_renovation';
  protected $fillable = ['water_id', 'infrastructure_id', 'quarter', 'year', 'month', 'repair_details',
      'tw_platform_renov', 'replace_small_parts', 'replace_large_parts', 'drainage_soak_well',
      'labour_cost', 'mat_transport_cost', 'renov_catchment', 'pipes', 'tank_tap_stand', 'renov_filter',
      'submer_pump', 'vessel_dosing', 'total_cost', 'comments', 'created_at', 'updated_at', 'created_by', 'updated_by'];
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
