<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPProblemReport extends Model
{
  protected $table = 'sp_problem_report';
  protected $fillable = ['user_id', 'proj_id', 'unid', 'inserted_at', 'infrastructure_id', 'institution_id',
      'ptype1', 'ptype2', 'ptype3', 'ptype4', 'ptype5', 'ptype6', 'ptype7', 'ptype8',
      'p_reportdate', 'p_createdate', 'p_image1', 'p_image2', 'p_image3', 'p_reocording', 'p_description',
      'is_cost_entered', 'is_resolved', 'is_maintenance', 'response_time', 'created_at', 'updated_at', 'created_by', 'updated_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at', 'updated_at'];

    public function ptype1()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype1', 'id');
    }
    public function ptype2()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype2', 'id');
    }
    public function ptype3()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype3', 'id');
    }
    public function ptype4()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype4', 'id');
    }
    public function ptype5()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype5', 'id');
    }
    public function ptype6()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype6', 'id');
    }
    public function ptype7()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype7', 'id');
    }
    public function ptype8()
    {
        return $this->belongsTo(SPRepProblemType::class, 'ptype8', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'proj_id', 'id');
    }
    public function union()
    {
        return $this->belongsTo(Union::class, 'unid', 'id');
    }
    public function infrastructure()
    {
        return $this->belongsTo(SPInfrastructure::class, 'infrastructure_id', 'water_id');
    }
    public function problemVerification()
    {
        return $this->hasMany(SPProblemVerification::class, 'problem_id', 'id');
    }

}
