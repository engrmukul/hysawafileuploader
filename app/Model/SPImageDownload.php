<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SPImageDownload extends Model
{
  protected $table = 'sp_image_download';
  protected $fillable = ['name', 'image_url'];
  protected $guarded = ['id'];
}
