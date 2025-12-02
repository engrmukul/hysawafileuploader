<?php

namespace App\Model;

class SanitationSummary2
{
    public static function regionSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
            region.region_name,
            COUNT(sanitation.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
        
            sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
            sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
            sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
            sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
            sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
            sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
            sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
            sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            
            FROM
            sanitation
            INNER JOIN
            region ON region.region_id = sanitation.region_id
            GROUP BY sanitation.region_id")
          );
    }


    public static function districtSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
            fdistrict.distname,
            COUNT(sanitation.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',

            sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
            sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
            sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
            sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
            sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
            sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
            sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
            sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            FROM

            sanitation

            INNER JOIN

                    fdistrict ON fdistrict.id = sanitation.dist_id

            GROUP BY sanitation.dist_id")
          );
    }


    public static function unionSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT

            fdistrict.distname,
            fupazila.upname,
            funion.unname,
            sanitation.unid,
            sanitation.app_date,

            COUNT(sanitation.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',

            sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
            sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
            sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
            sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
            sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
            sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
            sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
            sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            FROM
            sanitation
            INNER JOIN funion ON funion.id = sanitation.unid
            INNER JOIN fupazila ON fupazila.id = funion.upid
            INNER JOIN fdistrict ON fdistrict.id = fupazila.disid
            GROUP BY
            sanitation.unid,
            sanitation.app_date

            ")
          );
    }



    public static function approvalSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
                COUNT(sanitation.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1,0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',

                sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
                sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
                sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
                sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
                sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
                sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
                sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
                sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'
    

            FROM sanitation




            ")

          );
    }

    public static function approvalRegionSummary($date = null)
    {
        if($date == null)
        {
            return \DB::select(\DB::Raw("
                SELECT
                    region.region_name,

                    COUNT(sanitation.id) AS 'subappcount',
                    sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                    sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                    sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                    sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                    sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',


                    sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
                    sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
                    sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
                    sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
                    sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
                    sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
                    sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
                    sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

                FROM
                sanitation
                    INNER JOIN
                        region ON region.region_id = sanitation.region_id
                    GROUP BY sanitation.region_id")
              );
        }

        return \DB::select(\DB::Raw("
            SELECT
                region.region_name,

                COUNT(sanitation.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',


                sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
                sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
                sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
                sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
                sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
                sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
                sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
                sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            FROM
                sanitation

            INNER JOIN region ON region.region_id = sanitation.region_id

            WHERE sanitation.app_date = '$date'

            GROUP BY sanitation.region_id")
        );
    }


    public static function approvalDistrictSummary($date = null)
    {
        if($date == null)
        {
            return \DB::select(\DB::Raw("
                SELECT
                fdistrict.distname,
                COUNT(sanitation.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
     
                sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
                sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
                sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
                sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
                sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
                sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
                sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
                sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

                FROM

                sanitation

                INNER JOIN

                        fdistrict ON fdistrict.id = sanitation.dist_id

                GROUP BY sanitation.dist_id")
              );
        }

        return \DB::select(\DB::Raw("
            SELECT
                fdistrict.distname,
                COUNT(sanitation.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
       
                sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
                sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
                sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
                sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
                sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
                sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
                sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
                sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            FROM
            sanitation

            INNER JOIN
                fdistrict ON fdistrict.id = sanitation.dist_id

            WHERE sanitation.app_date = '$date'

            GROUP BY sanitation.dist_id"
            )
          );
    }



    public static function approvalUnionSummary($date = null, $type = null, $value = null)
    {

        $condition = "";

        if($date == null){
            if($type != null){
                $condition = "WHERE sanitation.$type = $value";
            }
        }else{
            if($type == null){
                $condition = "WHERE sanitation.app_date = '$date'";
            }else{
                $condition = "WHERE sanitation.$type = $value AND sanitation.app_date = '$date'";
            }
        }
        $condition = " ".$condition." ";
      // dd($condition);

        return \DB::select(\DB::Raw("
            SELECT

            fdistrict.distname,
            fupazila.upname,
            funion.unname,
            sanitation.unid,
            sanitation.app_date,

            COUNT(sanitation.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Assessed',1,0)) AS 'Assessed',
            sum(IF(subtype LIKE '%School%' ,1, 0)) AS 'School',
            sum(IF(subtype LIKE '%Madrasha%' ,1, 0)) AS 'Madrasha',
            sum(IF(subtype LIKE '%Mosque%' ,1, 0)) AS 'Mosque',
            sum(IF(subtype IN ( 'Community', 'Bazar', 'Slum') ,1, 0)) AS 'Community',
            sum(IF(( malechamber + femalechamber < 3) ,1, 0)) AS 'TwoChamber',
            sum(IF(( malechamber + femalechamber > 2 ) ,1, 0)) AS 'ThreeChamber',
            sum(IF(cons_type = 'New' ,1, 0)) AS 'New',
            sum(IF(cons_type = 'Renovation' ,1, 0)) AS 'Renovation'

            FROM
            sanitation
                INNER JOIN funion ON funion.id = sanitation.unid
                INNER JOIN fupazila ON fupazila.id = funion.upid
                INNER JOIN fdistrict ON fdistrict.id = fupazila.disid

            $condition

            GROUP BY
            sanitation.unid,
            sanitation.app_date
            ")
          );
    }

}




// SELECT tbl_water.id,Count(tbl_water.id) AS sumappcount
// FROM tbl_water WHERE tbl_water.app_status = 'Approved';

// SELECT tbl_water.id,Count(tbl_water.id) AS spendcount
// FROM tbl_water WHERE tbl_water.app_status = 'Submitted';


// SELECT tbl_water.id,Count(tbl_water.id) AS spendcount FROM
// tbl_water WHERE tbl_water.app_status = 'Recomended';

// SELECT tbl_water.id,Count(tbl_water.id) AS scanccount FROM
// tbl_water WHERE tbl_water.app_status = 'Cancelled';

// SELECT tbl_water.id,Count(tbl_water.id) AS srejcount FROM
// tbl_water WHERE tbl_water.app_status = 'Rejected' ;

// SELECT tbl_water.id,Count(tbl_water.id) AS stendcount FROM
// tbl_water WHERE tbl_water.app_status = 'Tendering in process';


// SELECT tbl_water.id,Count(tbl_water.id) AS simplcount FROM
// tbl_water WHERE tbl_water.imp_status = 'Under Implementation' ;


// SELECT tbl_water.id, Count(tbl_water.id) AS scompcount
// FROM tbl_water WHERE tbl_water.imp_status = 'Completed';

// SELECT tbl_water.id,Count(tbl_water.wq_Arsenic) AS swqcount FROM
// tbl_water WHERE tbl_water.wq_Arsenic is NOT NULL ;


// SELECT tbl_water.id,Count(tbl_water.platform) AS splcount FROM
// tbl_water WHERE tbl_water.platform = 'Yes' ;



// SELECT tbl_water.id,Count(tbl_water.depth) AS sdepcount FROM
// tbl_water WHERE tbl_water.depth > 0 ;

// SELECT tbl_water.id,Count(tbl_water.x_coord) AS sgpscount FROM
// tbl_water WHERE tbl_water.x_coord  != '' ;

// ?>
