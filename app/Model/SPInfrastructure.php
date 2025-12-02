<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPInfrastructure extends Model
{
    protected $table = 'sp_infrastructure';
    protected $fillable = ['school_id', 'water_id', 'tech_type', 'tech_type_bn', 'tech_type_name', 'install_year', 'install_by', 'functional_status',
        'install_cost', 'functional_status', 'non_func_status', 'non_func_days', 'taps_installation', 'taps_present', 'household_count', 'tariff_month', 'power_source', 'water_source',
        'drinking_use', 'non_drink_reason', 'run_year_round', 'run_year_reason', 'pumping', 'depth', 'tanks_count', 'tank_material', 'tank_capacity', 'tank_distance', 'catchment_area',
        'catchment_material', 'water_lasts_month', 'capacity_liter', 'monthly_bill', 'image', 'is_om_req', 'is_ren_req', 'ren_om_req', 'ren_om_id', 'wq_status_id',
        'lat', 'lon', 'comments', 'is_active', 'inactive_reason', 'onboard_date', 'is_asses', 'si_count', 'sample_count', 'inserted_at', 'created_at', 'updated_at', 'created_by', 'updated_by'];
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $dates = ['inserted_at', 'created_at', 'updated_at'];


    public function school()
    {
        return $this->belongsTo(SPSchool::class, 'school_id', 'id');
    }

    public function SpRepairRen()
    {
        return $this->hasOne(SPRepairRen::class, 'id', 'ren_om_id');
    }

    public function SpWQStatus()
    {
        return $this->hasOne(SPWQStatus::class, 'id', 'wq_status_id');
    }

    public function Samples()
    {
        return $this->hasMany(SPSampleCollection::class, 'infrastructure_id', 'id');
    }

    public function Inspections()
    {
        return $this->hasMany(SPSanInspectionV2::class, 'infrastructure_id', 'id');
    }


}
