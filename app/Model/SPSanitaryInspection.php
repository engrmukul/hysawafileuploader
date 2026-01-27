<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanitaryInspection extends Model
{
  protected $table = 'sp_sanitary_inspection';
  protected $fillable = ['water_id', 'infrastructure_id', 'quarter', 'year', 'inspection_date', 'sanitary_score', 'accnt_score', 'sanitary_risk',
      'si_answer', 'si_v2_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];
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
