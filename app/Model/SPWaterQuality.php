<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPWaterQuality extends Model
{
  protected $table = 'sp_water_quality';
    protected $fillable = ['water_id', 'infrastructure_id', 'sample_type', 'quarter', 'year', 'sampling_date',
        'test_date', 'parameter', 'unit', 'value', 'action_is_needed', 'risk_level', 'action_status',
        'disinfection_is_required', 'disinfection_date', 'verify_date', 'notify_date', 'sms_date', 'is_active',
        'created_at', 'updated_at', 'created_by', 'updated_by'];
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $dates = ['created_at', 'updated_at'];


    public function infrastructure()
    {
        return $this->belongsTo(SPInfrastructure::class, 'infrastructure_id', 'id');
    }

    public function sample()
    {
        return $this->belongsTo(SPSampleCollection::class, 'sample_id', 'sample_id');
    }

}