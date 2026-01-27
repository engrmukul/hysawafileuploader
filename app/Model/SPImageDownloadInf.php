<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPImageDownloadInf extends Model
{
  protected $table = 'sp_image_download_inf';
  protected $fillable = ['waterpoint_id', 'waterpoint_photo'];
  protected $guarded = ['id'];
}
