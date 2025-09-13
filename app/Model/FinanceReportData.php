<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FinanceReportData
{

  public static function getData($type, $value, $starting_date)
    {
      return  \DB::select(\DB::raw("
          SELECT
            bIncome,
            bExpenditure,
            cIncome,
            cExpenditure,
            bIncome-bExpenditure AS opening_bank,
            cIncome-cExpenditure AS opening_cash,
            Income-Expenditure AS total
          FROM(
            SELECT *,
                  SUM(IF(trans_type = 'in' AND MODE='bank', amount, 0)) AS 'bIncome',
                  SUM(IF(trans_type = 'ex' AND MODE='bank', amount, 0)) AS 'bExpenditure',
                  SUM(IF(trans_type = 'in' AND MODE='cash', amount, 0)) AS 'cIncome',
                  SUM(IF(trans_type = 'ex' AND MODE='cash', amount, 0)) AS 'cExpenditure',
                  SUM(IF(trans_type = 'in', amount, 0)) AS 'Income',
                  SUM(IF(trans_type = 'ex', amount, 0)) AS 'Expenditure',
                  SUM(amount) AS Total
            FROM  fdata
            WHERE $type = $value AND date < '".$starting_date."'

          ) AS t"));
    }

    public static function getData2($type, $value, $ending_date)
    {
      return \DB::select(\DB::raw("
            SELECT
              bIncome,
              bExpenditure,
              cIncome,
              cExpenditure,
              bIncome-bExpenditure AS opening_bank,
              cIncome-cExpenditure AS opening_cash,
              Income-Expenditure AS total
            FROM (
              SELECT
                *,
                SUM(IF(trans_type = 'in' AND MODE='bank', amount, 0)) AS 'bIncome',
                SUM(IF(trans_type = 'ex' AND MODE='bank', amount, 0)) AS 'bExpenditure',
                SUM(IF(trans_type = 'in' AND MODE='cash', amount, 0)) AS 'cIncome',
                SUM(IF(trans_type = 'ex' AND MODE='cash', amount, 0)) AS 'cExpenditure',
                SUM(IF(trans_type = 'in', amount, 0)) AS 'Income',
                SUM(IF(trans_type = 'ex', amount, 0)) AS 'Expenditure',
                SUM(amount) AS Total
                FROM
                  fdata
                WHERE
                  $type = $value AND DATE < '".$ending_date."'
            )
            AS t"));
    }

}
