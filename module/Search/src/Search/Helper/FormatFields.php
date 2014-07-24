<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/7/14
 * Time: 2:49 PM
 */

namespace Search\Helper;


class FormatFields {
    public static function reformatDate($dateFrom, $dateTo){
        $from = explode(' ', $dateFrom);
        $to = explode(' ', $dateTo);
        $f = $from[0];
        $t = $to[0];
        $reformatedFromDate = date('m/d/Y', strtotime($f));
        $reformatedtoDate = date('m/d/Y', strtotime($t));
        return $reformatedFromDate.' - '.$reformatedtoDate;
    }
} 