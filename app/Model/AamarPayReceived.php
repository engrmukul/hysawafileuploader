<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class AamarPayReceived extends Model
{
    protected $table = 'aamar_pay_received';

    protected $fillable = [
    'hardware_type',
    'hardware_id',
    'app_id',
    'distid',
    'upid',
    'unid',
    'recv_amount',
    'payee_name',
    'email',
    'pay_time',
    'pay_date'
    ];

    protected $guarded = ['id'];
    public $timestamps = false;

    protected $dates = [];


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
