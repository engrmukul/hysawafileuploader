<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPVolumetric extends Model
{
  protected $table = 'sp_volumetric';
  protected $fillable = ['water_id', 'infrastructure_id', 'flowmeter_no', 'reading', 'consumption', 'reading_date',
      'comments', 'created_at', 'updated_at', 'created_by', 'updated_by'];
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
