<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPSanInspectionV2 extends Model
{
  protected $table = 'sp_san_inspection_v2';
  protected $fillable = ['water_id', 'infrastructure_id', 'infrastructure_cat', 'quarter', 'year', 'inspection_date', 'sanitary_score', 'accnt_score', 'sanitary_risk',
                          'lat', 'lon', 'answer_id', 'observable_id', 'image1', 'image2', 'image3', 'comments',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function SpSanAnswer()
    {
        return $this->hasOne(SPSanAnswer::class, 'id', 'answer_id');
    }

    public function SpSanAnswerObs()
    {
        return $this->hasOne(SPSanAnswerObs::class, 'id', 'observable_id');
    }

    public function SpSanAnswerCorr()
    {
        return $this->hasMany(SPSanAnswerCorr::class, 'si_id', 'id');
    }
}
