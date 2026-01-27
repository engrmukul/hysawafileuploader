<?php

namespace App\Model;

use App\Model\Upazila;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
  protected $table = 'budget';
  protected $fillable = [
  'itemid',
  'desbus',
  'mode',
  'vou',
  'desdate',
  'remarks',
  'distid',
  'upid',
  'unid',
  'userid',
  'proid',
  'entry_date',
  'region_id',
  'dup_id'];
  protected $guarded = ['id'];
  public $timestamps = false;





  public static function data1($unid, $sql)
  {
    \DB::enableQueryLog();
    $data = \DB::select(\DB::Raw("select sum(desbus)as tdesbus,
     SUM(CASE WHEN headid=9 THEN headid ELSE 0 END) AS 'cdf_no',
     SUM(CASE WHEN headid=13 THEN headid ELSE 0 END) AS 'cdf_no2',
     SUM(CASE WHEN headid in (4,5,6,7,8,10) THEN headid ELSE 0 END) AS 'cdf_no3',t.*
     from (
     select `fitem`.`headid` AS `headid`,`fitem`.`subid` AS `subid`,`budget`.`itemid` AS `itemid`,`budget`.`desbus` AS `desbus`,`budget`.`mode` AS `mode`,`budget`.`vou` AS `vou`,`budget`.`desdate` AS `desdate`,`budget`.`remarks` AS `remarks`,`budget`.`distid` AS `distid`,`budget`.`upid` AS `upid`,`budget`.`unid` AS `unid`,`budget`.`region_id` AS `region_id`,`budget`.`userid` AS `userid`,`budget`.`proid` AS `proid`,`budget`.`entry_date` AS `entry_date` from (`budget` left join `fitem` on((`budget`.`itemid` = `fitem`.`id`)))
) as t where unid=$unid $sql"));

    return $data;
  }


  public static function data2($unid, $sql)
  {
    $data = \DB::select(\DB::Raw("
        SELECT
        SUM(
          CASE WHEN
            head IN (4,5,6,7,8,9,10,13)  AND trans_type = 'in'
            -- `head`  IN (4,5,6,7,8,9,10,13)  and
            --   trans_type = 'in'
          THEN amount
          ELSE 0 END) AS 'amount',

        SUM(CASE WHEN item = 36 AND trans_type = 'in' THEN amount ELSE 0 END) AS 'amount2',
        SUM(CASE WHEN item = 12 AND trans_type = 'in' THEN amount ELSE 0 END) AS 'amount3',

        SUM(
          CASE WHEN `head` IN (4,5,6,7,8,9,10,12,13)  and trans_type = 'in'
          THEN amount
          ELSE 0 END) AS 'amount4',

        SUM(
          CASE WHEN `head` IN (4,5,6,7,8,9,10,12,13)  and trans_type = 'ex'
          THEN amount
          ELSE 0 END) AS 'amount5'



        FROM fdata
        WHERE

        unid=$unid $sql"));

   // dd($data);

    return $data;
  }
}
