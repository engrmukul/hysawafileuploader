<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPWQStatus extends Model
{
  protected $table = 'sp_wq_status';
  protected $fillable = ['is_past_wq', 'wq_when', 'wq_who', 'is_rep', 'is_ars', 'is_cl', 'is_fe',
      'is_agency', 'agency_nm', 'agency_freq', 'vul_ann_flood', 'vul_storm',
      'vul_dec_water', 'vul_tid_flood', 'comm_reliance','created_at', 'created_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at'];

    public function SpInfrastructure()
    {
        return $this->belongsTo(SpInfrastructure::class, 'id', 'wq_status_id');
    }
}
