<?php

namespace App\Model;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SPSchool extends Model
{
  protected $table = 'sp_school';
  protected $fillable = ['institution_id', 'org_code', 'proid', 'distid', 'upid', 'unid', 'ward', 'vill', 'vill_bn',
      'sch_name_en', 'sch_name_bn', 'estab_year', 'owner_type', 'sch_type_edu', 'sch_type_gen', 'boy_student',
      'girl_student', 'disabled_boys', 'disabled_girls', 'hcf_type', 'img1', 'img2', 'img3', 'img4', 'img5', 'img6', 'img9', 'tot_student',
      'vision_impair_student', 'mobility_impair_student', 'male_staff', 'female_staff ', 'tot_staff', 'nearby_families', 'daily_visitor', 'monthly_patients', 'catchm_patients', 'water_counts',
      'drinking_counts', 'non_func_counts', 'func_counts', 'contact_name', 'contact_position', 'contact_sex', 'contact_phone', 'contact_email', 'lat', 'lon', 'remark',
      'headmaster_chcp', 'head_phone', 'smc_president', 'smc_phone', 'onboard_date', 'last_update', 'is_asses', 'is_active', 'app_pass', 'secret_code', 'base_asse', 'base_date',
      'inserted_at', 'created_at', 'updated_at', 'created_by', 'updated_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['inserted_at', 'created_at', 'updated_at'];


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

    public function assesor()
    {
        return $this->belongsTo(User::class, 'base_asse', 'id');
    }
}
