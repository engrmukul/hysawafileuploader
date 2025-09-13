<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPAppSettings extends Model
{
    protected $table = 'sp_app_settings';

    protected $fillable = ['settings_name', 'settings_value', 'status'];

    protected $guarded = ['id'];
}
