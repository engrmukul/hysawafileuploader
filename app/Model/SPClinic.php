<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPClinic extends Model
{
  protected $table = 'sp_clinic';
  protected $fillable = ['clinic_name', 'org_code', 'distid', 'upid', 'unid', 'vill', 'ward', 'proid',
      'tot_staff', 'male_staff', 'female_staff', 'disable_staff', 'avg_visitor', 'male_visitor', 'female_visitor',
      'child_visitor', 'estab_year', 'remark', 'created_at', 'updated_at', 'created_by', 'updated_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at', 'updated_at'];


    public function project()
    {
        return $this->belongsTo(Project::class, 'proid', 'id');
    }


    public function district()
    {
        return $this->belongsTo(District::class, 'distid', 'id');
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'upid', 'id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class, 'unid', 'id');
    }
}
