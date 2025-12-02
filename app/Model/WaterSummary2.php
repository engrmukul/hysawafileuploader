<?php

namespace App\Model;

class WaterSummary2
{
    public static function projectSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
            project.project,
            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != ' ', 1, 0)) AS 'x_coord'
            FROM
            tbl_water
            INNER JOIN
            project ON project.id = tbl_water.proj_id
            GROUP BY tbl_water.proj_id")
        );
    }

    public static function regionSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
            region.region_name,
            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != ' ', 1, 0)) AS 'x_coord'
            FROM
            tbl_water
            INNER JOIN
            region ON region.region_id = tbl_water.region_id
            GROUP BY tbl_water.region_id")
          );
    }


    public static function districtSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
            fdistrict.distname,
            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != ' ', 1, 0)) AS 'x_coord'

            FROM

            tbl_water

            INNER JOIN

                    fdistrict ON fdistrict.id = tbl_water.distid

            GROUP BY tbl_water.distid")
          );
    }


    public static function unionSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT

            fdistrict.distname,
            fupazila.upname,
            funion.unname,
            tbl_water.unid,
            tbl_water.App_date,
            tbl_water.Tend_lot,

            SUM(tbl_water.HH_benefited) AS 'hhcount',
            Sum(tbl_water.HCHH_benefited) AS hchhcount,
            Sum(tbl_water.HCHH_benefited) / Sum(tbl_water.HH_benefited)*100 AS hcPcount,
            (Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100)*0.1 + (100-(Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100))*0.2 AS cccount,

            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != '', 1, 0)) AS 'x_coord'

            FROM
            tbl_water
            INNER JOIN funion ON funion.id = tbl_water.unid
            INNER JOIN fupazila ON fupazila.id = funion.upid
            INNER JOIN fdistrict ON fdistrict.id = fupazila.disid
            GROUP BY
                tbl_water.unid,
                tbl_water.Tend_lot,
                tbl_water.App_date

            ")
          );
    }



    public static function approvalSummary()
    {
        return \DB::select(\DB::Raw("
            SELECT
                COUNT(tbl_water.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1,0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
                sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
                sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
                sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',

                SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
                sum(IF(platform = 'yes', 1, 0)) AS 'platform',

                SUM(IF(depth != '0', 1, 0)) AS 'depth',
                SUM(IF(x_coord != '', 1, 0)) AS 'x_coord'

            FROM tbl_water




            ")

          );
    }

    public static function approvalProjectSummary($date = null, $tech_type = null)
    {
        $condition = "";

        if($tech_type != null){
            if($date != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
            } else {
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type'";
            }
        } else if($date != null){
            if($tech_type != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
            } else {
                $condition = "WHERE tbl_water.App_date = '$date'";
            }
        }
        $condition = " ".$condition." ";

        return \DB::select(\DB::Raw("
            SELECT
                project.project,

                COUNT(tbl_water.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
                sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
                sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
                sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',

                SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
                sum(IF(platform = 'yes', 1, 0)) AS 'platform',

                sum(IF(depth != '0', 1, 0)) AS 'depth',
                sum(IF(x_coord != ' ', 1, 0)) AS 'x_coord'

            FROM
                tbl_water

            INNER JOIN project ON project.id = tbl_water.proj_id
            $condition
            GROUP BY tbl_water.proj_id")
        );
    }

    public static function approvalRegionSummary($date = null, $tech_type = null)
    {
        $condition = "";

        if($tech_type != null){
            if($date != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
            } else {
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type'";
            }
        } else if($date != null){
            if($tech_type != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
            } else {
                $condition = "WHERE tbl_water.App_date = '$date'";
            }
        }
        $condition = " ".$condition." ";

        return \DB::select(\DB::Raw("
            SELECT
                region.region_name,

                COUNT(tbl_water.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
                sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
                sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
                sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',

                SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
                sum(IF(platform = 'yes', 1, 0)) AS 'platform',

                sum(IF(depth != '0', 1, 0)) AS 'depth',
                sum(IF(x_coord != ' ', 1, 0)) AS 'x_coord'

            FROM
                tbl_water

            INNER JOIN region ON region.region_id = tbl_water.region_id

            $condition

            GROUP BY tbl_water.region_id")
        );
    }


    public static function approvalDistrictSummary($date = null, $tech_type = null)
    {
        $condition = "";

        if($tech_type != null){
            if($date != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
            } else {
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type'";
            }
        } else if($date != null){
            if($tech_type != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
            } else {
                $condition = "WHERE tbl_water.App_date = '$date'";
            }
        }
        $condition = " ".$condition." ";


        return \DB::select(\DB::Raw("
            SELECT
                fdistrict.distname,
                COUNT(tbl_water.id) AS 'subappcount',
                sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
                sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
                sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
                sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
                sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
                sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
                sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
                sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
                SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
                sum(IF(platform = 'yes', 1, 0)) AS 'platform',
                sum(IF(depth != '0', 1, 0)) AS 'depth',
                sum(IF(x_coord != ' ', 1, 0)) AS 'x_coord'

            FROM
                tbl_water

            INNER JOIN
                fdistrict ON fdistrict.id = tbl_water.distid

            WHERE tbl_water.App_date = '$date'
            
            $condition

            GROUP BY tbl_water.distid"
            )
          );
    }



    public static function approvalUnionSummary($date = null, $type = null, $value = null, $tech_type = null)
    {
        $condition = "";

        if($tech_type != null){
             if($type != null){
                 if($date != null){
                     $condition = "WHERE tbl_water.$type = $value AND tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
                 } else {
                     $condition = "WHERE tbl_water.$type = $value AND tbl_water.Technology_Type = '$tech_type'";
                 }
             } else if($date != null){
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type' AND tbl_water.App_date = '$date'";
             } else {
                $condition = "WHERE tbl_water.Technology_Type = '$tech_type'";
             }
        } else if($date == null){
            if($type != null){
                $condition = "WHERE tbl_water.$type = $value";
            }
        }else{
            if($type == null){
                $condition = "WHERE tbl_water.App_date = '$date'";
            }else{
                $condition = "WHERE tbl_water.$type = $value AND tbl_water.App_date = '$date'";
            }
        }
        $condition = " ".$condition." ";
        //dd($condition);

        return \DB::select(\DB::Raw("
            SELECT

            fdistrict.distname,
            fupazila.upname,
            funion.unname,
            tbl_water.unid,
            tbl_water.App_date,
            tbl_water.Tend_lot,

            SUM(tbl_water.HH_benefited) AS 'hhcount',
            Sum(tbl_water.HCHH_benefited) AS hchhcount,
            Sum(tbl_water.HCHH_benefited) / Sum(tbl_water.HH_benefited)*100 AS hcPcount,
            (Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100)*0.1 + (100-(Sum(tbl_water.HCHH_benefited)/Sum(tbl_water.HH_benefited)*100))*0.2 AS cccount,

            COUNT(tbl_water.id) AS 'subappcount',
            sum(IF(app_status = 'Approved',1,0)) AS 'sumappcount',
            sum(IF(app_status = 'Submitted',1, 0)) AS 'Submitted',
            sum(IF(app_status = 'Recomended',1,0)) AS 'Recomended',
            sum(IF(app_status = 'Cancelled',1,0)) AS 'Cancelled',
            sum(IF(app_status = 'Rejected',1,0)) AS 'Rejected',
            sum(IF(app_status = 'Assessed',1,0)) AS 'Assessed',
            sum(IF(app_status = 'Tendering in process',1,0)) AS 'TenderingInProcess',
            sum(IF(imp_status = 'Under Implementation', 1,0)) AS 'UnderImplementation',
            sum(IF(imp_status = 'Completed', 1, 0)) AS 'Completed',
            SUM(CASE WHEN wq_Arsenic IS NOT NULL THEN 1 ELSE 0 END) AS 'wq_Arsenic',
            sum(IF(platform = 'yes', 1, 0)) AS 'platform',
            sum(IF(depth != '0', 1, 0)) AS 'depth',
            sum(IF(x_coord != '', 1, 0)) AS 'x_coord'

            FROM
                tbl_water
                INNER JOIN funion ON funion.id = tbl_water.unid
                INNER JOIN fupazila ON fupazila.id = funion.upid
                INNER JOIN fdistrict ON fdistrict.id = fupazila.disid

            $condition

            GROUP BY
                tbl_water.unid,
               tbl_water.App_date
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
