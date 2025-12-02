<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class TrainingVenue extends Model
{
  protected $table = 'trg_venue';
  protected $primaryKey = 'VenueID';
  protected $fillable = [
    'VenueName',
    'VenueName',
    'VenueAddress',
    'ContactPerson',
    'ContactPhone1',
    'ContactPhone2',
    'EmailAddress',
    'created_by',
    'created_at',
    'updated_by',
    'updated_at'
  ];

  protected $guarded = ['VenueID'];
  public $timestamps = false;
}
