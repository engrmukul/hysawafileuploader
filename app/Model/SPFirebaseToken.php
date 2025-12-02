<?php

namespace App\Model;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SPFirebaseToken extends Model
{
  protected $table = 'sp_firebase_token';
  protected $fillable = ['userid', 'username', 'user_group', 'registered_token', 'current_token',
      'email', 'requested_role', 'is_active', 'created_at', 'updated_at', 'created_by', 'updated_by'];
  protected $guarded = ['id'];
  public $timestamps = false;
  protected $dates = ['created_at', 'updated_at'];


    public function infrastructure()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }

}
