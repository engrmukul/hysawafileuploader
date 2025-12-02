<?php

namespace App\Model;

use App\Model\MobAppDataListItem;
use App\Model\Project;
use App\Model\Region;
use App\Model\Union;
use App\User;
use Illuminate\Database\Eloquent\Model;

class MobAppDataEvents extends Model
{
    protected $table = 'mobile_app_data_events';
    protected $fillable = ['mob_app_list_id', 'rep_id', 'ev_cdf', 'ev_name', 'ev_loc', 'ev_male', 'ev_female',
        'ev_disable', 'is_approved', 'ev_time', 'ev_comments'];
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $dates = ['created_at', 'updated_at'];

}
