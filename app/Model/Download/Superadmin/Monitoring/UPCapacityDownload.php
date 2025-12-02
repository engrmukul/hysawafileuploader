<?php

namespace App\Model\Download\Superadmin\Monitoring;

use Illuminate\Http\Request;

class UPCapacityDownload
{
  public function download()
  {
    $rows = \DB::select(\DB::Raw("
      SELECT
      cap_rep_id.querter,
      region_name,

      `upcapacity`.`proj_id` AS `proj_id`,
      `upcapacity`.`region_id` AS `region_id`,
      `upcapacity`.`distid` AS `distid`,
      `fdistrict`.`distname` AS `distname`,
      `upcapacity`.`upid` AS `upid`,
      `fupazila`.`upname` AS `upname`,
      `upcapacity`.`unid` AS `unid`,
      `funion`.`unname` AS `unname`,
      `upcapacity`.`rep_id` AS `rep_id`,

       sum(`upcapacity`.`11`) AS `SumOf11`,
       sum(`upcapacity`.`12`) AS `SumOf12`,
       sum(`upcapacity`.`13`) AS `SumOf13`,
       sum(`upcapacity`.`14`) AS `SumOf14`,
       sum(`upcapacity`.`15`) AS `SumOf15`,
       sum(`upcapacity`.`16`) AS `SumOf16`,
       sum(`upcapacity`.`17`) AS `SumOf17`,
       sum(`upcapacity`.`18`) AS `SumOf18`,
       sum(`upcapacity`.`19`) AS `SumOf19`,
       sum(`upcapacity`.`110`) AS `SumOf110`,
       sum(`upcapacity`.`21`) AS `SumOf21`,
       sum(`upcapacity`.`22`) AS `SumOf22`,
       sum(`upcapacity`.`23`) AS `SumOf23`,
       sum(`upcapacity`.`24`) AS `SumOf24`,
       sum(`upcapacity`.`31`) AS `SumOf31`,
       sum(`upcapacity`.`32`) AS `SumOf32`,
       sum(`upcapacity`.`33`) AS `SumOf33`,
       sum(`upcapacity`.`34`) AS `SumOf34`,
       sum(`upcapacity`.`35`) AS `SumOf35`,
       sum(`upcapacity`.`36`) AS `SumOf36`,
       sum(`upcapacity`.`37`) AS `SumOf37`,
       sum(`upcapacity`.`38`) AS `SumOf38`,
       sum(`upcapacity`.`39`) AS `SumOf39`,
       sum(`upcapacity`.`310`) AS `SumOf310`,
       sum(`upcapacity`.`311`) AS `SumOf311`,
       sum(`upcapacity`.`312`) AS `SumOf312`,
       sum(`upcapacity`.`313`) AS `SumOf313`,
       sum(`upcapacity`.`314`) AS `SumOf314`,
       sum(`upcapacity`.`315`) AS `SumOf315`,
       sum(`upcapacity`.`316`) AS `SumOf316`,
       sum(`upcapacity`.`317`) AS `SumOf317`,
       sum(`upcapacity`.`41`) AS `SumOf41`,
       sum(`upcapacity`.`42`) AS `SumOf42`,
       sum(`upcapacity`.`43`) AS `SumOf43`,
       sum(`upcapacity`.`44`) AS `SumOf44`,
       sum(`upcapacity`.`45`) AS `SumOf45`,
       sum(`upcapacity`.`46`) AS `SumOf46`,
       sum(`upcapacity`.`47`) AS `SumOf47`,
       sum(`upcapacity`.`48`) AS `SumOf48`,
       sum(`upcapacity`.`49`) AS `SumOf49`,
       sum(`upcapacity`.`51`) AS `SumOf51`,
       sum(`upcapacity`.`52`) AS `SumOf52`,
       sum(`upcapacity`.`53`) AS `SumOf53`,
       sum(`upcapacity`.`54`) AS `SumOf54`,
       sum(`upcapacity`.`55`) AS `SumOf55`,
       sum(`upcapacity`.`56`) AS `SumOf56`,
       sum(`upcapacity`.`57`) AS `SumOf57`,
       sum(`upcapacity`.`58`) AS `SumOf58`,
       sum(`upcapacity`.`59`) AS `SumOf59`,
       sum(`upcapacity`.`510`) AS `SumOf510`,
       sum(`upcapacity`.`511`) AS `SumOf511`,
       sum(`upcapacity`.`512`) AS `SumOf512`,
       sum(`upcapacity`.`513`) AS `SumOf513`,
       sum(`upcapacity`.`514`) AS `SumOf514`,
       sum(`upcapacity`.`515`) AS `SumOf515`,
       sum(`upcapacity`.`61`) AS `SumOf61`,
       sum(`upcapacity`.`62`) AS `SumOf62`,
       sum(`upcapacity`.`63`) AS `SumOf63`,

       (((((((((
         sum(`upcapacity`.`11`) +
         sum(`upcapacity`.`12`)) +
         sum(`upcapacity`.`13`)) +
         sum(`upcapacity`.`14`)) +
         sum(`upcapacity`.`15`)) +
         sum(`upcapacity`.`16`)) +
         sum(`upcapacity`.`17`)) +
         sum(`upcapacity`.`18`)) +
         sum(`upcapacity`.`19`)) +
         sum(`upcapacity`.`110`)) AS `finance`,

       (((sum(`upcapacity`.`21`) + sum(`upcapacity`.`22`)) + sum(`upcapacity`.`23`)) + sum(`upcapacity`.`24`)) AS `procurement`,

       ((((((((((((((((sum(`upcapacity`.`31`) + sum(`upcapacity`.`32`)) + sum(`upcapacity`.`33`)) + sum(`upcapacity`.`34`)) + sum(`upcapacity`.`35`)) + sum(`upcapacity`.`36`)) + sum(`upcapacity`.`37`)) + sum(`upcapacity`.`38`)) + sum(`upcapacity`.`39`)) + sum(`upcapacity`.`310`)) + sum(`upcapacity`.`311`)) + sum(`upcapacity`.`312`)) + sum(`upcapacity`.`313`)) + sum(`upcapacity`.`314`)) + sum(`upcapacity`.`315`)) + sum(`upcapacity`.`316`)) + sum(`upcapacity`.`317`)) AS `program`,

       ((((((((sum(`upcapacity`.`41`) + sum(`upcapacity`.`42`)) + sum(`upcapacity`.`43`)) + sum(`upcapacity`.`44`)) + sum(`upcapacity`.`45`)) + sum(`upcapacity`.`46`)) + sum(`upcapacity`.`47`)) + sum(`upcapacity`.`48`)) + sum(`upcapacity`.`49`)) AS `admin`,

       ((((((((((((((sum(`upcapacity`.`51`) + sum(`upcapacity`.`52`)) + sum(`upcapacity`.`53`)) + sum(`upcapacity`.`54`)) + sum(`upcapacity`.`55`)) + sum(`upcapacity`.`56`)) + sum(`upcapacity`.`57`)) + sum(`upcapacity`.`58`)) + sum(`upcapacity`.`59`)) + sum(`upcapacity`.`510`)) + sum(`upcapacity`.`511`)) + sum(`upcapacity`.`512`)) + sum(`upcapacity`.`513`)) + sum(`upcapacity`.`514`)) + sum(`upcapacity`.`515`)) AS `offmgt`,((sum(`upcapacity`.`61`) + sum(`upcapacity`.`62`)) + sum(`upcapacity`.`63`)) AS `resource`,

       (((((((sum(`upcapacity`.`71`) + sum(`upcapacity`.`72`)) + sum(`upcapacity`.`73`)) + sum(`upcapacity`.`74`)) + sum(`upcapacity`.`75`)) + sum(`upcapacity`.`76`)) + sum(`upcapacity`.`77`)) + sum(`upcapacity`.`78`)) AS `gov`

        from (((((`upcapacity` join `fdistrict`) join `fupazila`) join `funion`) join `region`) JOIN `cap_rep_id`)
        where ((`upcapacity`.`distid` = `fdistrict`.`id`) and (`upcapacity`.`upid` = `fupazila`.`id`) and (`upcapacity`.`unid` = `funion`.`id`)AND (`region`.`id` = `funion`.`region_id`)AND (`upcapacity`.`rep_id` = `cap_rep_id`.`id`))
        group by `upcapacity`.`proj_id`,`upcapacity`.`region_id`,`upcapacity`.`distid`,`upcapacity`.`upid`,`upcapacity`.`unid`,`upcapacity`.`rep_id`
        ORDER BY distid, upid, unid, cap_rep_id.id

    "));

    \Excel::create('Monitoring UP Capacity Report '.time(), function($excel) use($rows) {
      $excel->sheet('Sheetname', function($sheet) use($rows) {

        $sheet->setOrientation('landscape');

        $sheet->row(1, array(

            'District',
            'Upazila',
            'Union',
            'Period',

            'Total score out of 290',
            'Financial Management (Total  score out of 50)',
            'Procurement (Total  score out of 20)',
            'Program Management (Total  score out of 85)',

            'Institutional Management (Total  score out of 45)',
            'Office Management (Total  score out of 75)',
            'Resource Mobilisation  (Total  score out of 15)',
        ));

        $rowIndex = 2;
        foreach($rows as $row)
        {
          $total=$row->finance + $row->procurement + $row->program + $row->admin + $row->offmgt + $row->resource;
          $sheet->row($rowIndex, [
            $row->distname,
            $row->upname,
            $row->unname,
            $row->querter,
            $total ,
            $row->finance,
            $row->procurement,
            $row->program,
            $row->admin,
            $row->offmgt ,
            $row->resource
          ]);
          $rowIndex++;
        }
        });
    })->download('csv');
  }
}