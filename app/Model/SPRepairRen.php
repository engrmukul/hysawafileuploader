<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPRepairRen extends Model
{
  protected $table = 'sp_repair_ren';
  protected $fillable = ['rtype1', 'rtype2', 'rtype3', 'rtype4', 'rtype5', 'rtype6', 'rtype7', 'rtype8', 'rtype9',
      'rtype10', 'rtype11', 'rtype12', 'rtype13', 'rtype14', 'rtype15', 'rtype16', 'rtype17', 'rtype18', 'rtype19',
      'rtype20', 'rtype21', 'rtype22', 'rtype23', 'rtype24', 'rtype25', 'rtype26', 'rtype27', 'rtype28', 'rtype29', 'rtype30',
      'rtype31', 'rtype32', 'rtype33', 'rtype34', 'rtype35', 'rtype36', 'total_cost'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['updated_at'];

    public function SpInfrastructure()
    {
        return $this->belongsTo(SpInfrastructure::class, 'id', 'ren_om_id');
    }
}
