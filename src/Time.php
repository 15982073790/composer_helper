<?php

namespace Mrstock\Helper;

class Time
{
    public static $arr_types = array(
        'Y-m-d H:i:s',
        'Y年m月d日',
        'Y/m/d H:i:s',
        'Ymd',
        'Y-m-d',
        'Ymdhis',
        'm月d日',
        'Y',
        'm-d H:i',
        'H:i',
        'm月',
        'd日',
        'Y-m-d H:i',
    );

    /**
     * 获取app
     * @param unknown $date
     * @return string
     */
    public static function getAppShowDate($date, $date_type = 2)
    {
        $d = strtotime($date);
        $new_d = time();
        $cha = $new_d - $d;//var_dump($date,$cha);exit;
        if ($cha < 3600) {//1小时之内显示分钟
            return (ceil(round($cha / 60, 1))) ? ceil(round($cha / 60, 1)) . "分钟前" : "1分钟前";
        } elseif ($cha < 86400) {//1天之内显示小时
            return (ceil(round($cha / 3600, 1))) ? ceil(round($cha / 3600, 1)) . "小时前" : "1小时前";
        } elseif ($cha < (86400 * 30)) {//1月之内显示天
            return (ceil(round($cha / 86400, 1))) ? ceil(round($cha / 86400, 1)) . "天前" : "1天前";
        } else {
            return self::strDate2Date($date, $date_type);
        }
    }


    /**
     * 获取app聊天消息显示的时间
     * @param unknown $date
     * @return string
     */
    public static function getAppShowChatDate($date)
    {

        $d = strtotime($date);
        $taday_s = strtotime(date('Y-m-d'));
        $taday_e = strtotime(date('Y-m-d 23:59:59'));

        $y_taday_s = strtotime(self::getDateAndNum(date('Y-m-d'), 1, 0, '-'));
        $y_taday_e = strtotime(self::getDateAndNum(date('Y-m-d 23:59:59'), 1, 0, '-'));


        $w_taday_s = strtotime(self::getThisMonday(4));
        $w_taday_e = strtotime(self::getDateAndNum(self::getThisMonday(4) + " 23:59:59", 6, 0));

        if ($d >= $taday_s && $d <= $taday_e) {//今天
            return self::strDate2Date($date, 11);
        } elseif ($d >= $y_taday_s && $d <= $y_taday_e) {//昨天
            return '昨天  ' . self::strDate2Date($date, 11);
        } elseif ($d >= $w_taday_s && $d <= $w_taday_e) {//本周
            return self::strDate2DateAndWeek($date, 11);
        } else {
            return self::strDate2Date($date, 6);
        }
    }

    /**
     *
     * @return 获取指定格式的时间
     */
    public static function getDate($type = 0)
    {
        $types = array(
            'Y-m-d H:i:s',
            'Y年m月d日',
            'Y/m/d H:i:s',
            'Ymd',
            'Y-m-d',
            'Ymdhis',
            'm月d日',
            'Y',
            'm-d H:i',
            'H:i',
            'm月',
            'd日',
            'Y-m-d H:i',
        );
        return date($types[$type], time());
    }

    /**
     * 获取当前时间
     */
    public static function getTime($hmState = false)
    {
        $time = date('H:i:s', time());
        if ($hmState) {
            list($usec, $sec) = explode(" ", microtime());
            $time .= ' ' . floor($usec * 1000);
        }
        return $time;
    }

    /**
     *
     * @return 获取指定时间N天后的时间
     */
    public static function getDateAndNum($date, $num, $type = 0, $sign = '+')
    {
        $types = array(
            'Y-m-d H:i:s',
            'Y年m月d日',
            'Y/m/d H:i:s',
            'Ymd',
            'Y-m-d',
        );
        return date($types[$type], (strtotime("$sign$num   days ", strtotime($date))));
    }


    /**
     *
     * @return 返回值为相差的天数
     */
    public static function dateDiff($d1, $d2)
    {
        $d1 = strtotime($d1);
        $d2 = strtotime($d2);
        return ceil(round(($d2 - $d1) / 86400, 1));//不取整，会出现2.666666这样的天
    }


    /**
     *
     * @return 返回值为相差的秒数
     */
    public static function dateDiffSecond($d1, $d2)
    {
        $d1 = strtotime($d1);
        $d2 = strtotime($d2);
        return $d2 - $d1;
    }


    /**
     * 日期转换成指定格式的时间
     * @param string $strdate Unix 时间戳
     * @param int $type 显示的时间类型
     * @return 指定格式的时间
     */
    public static function strDate2Date($strdate, $type = 0)
    {
        $types = array(
            'Y年m月d日',
            'Y/m/d H:i:s',
            'Y-m-d H:i:s',
            'Ymd',
            'Y-m-d',
            'm-d',
            'Y年m月d日　H:i',
            'YmdHis',
            'm月d日',
            'Y',
            'm-d H:i',
            'H:i',
            'm月',
            'd日',
            'm月d日　H:i',
            'Y-m-d H:i',
        );

        return date($types[$type], strtotime($strdate));
    }

    /**
     * 时间戳转换成指定格式的时间
     * @param int $timestamp Unix 时间戳
     * @param int $type 显示的时间类型
     * @return 指定格式的时间
     */
    public static function Gmt2Date($timestamp, $type = 0)
    {
        $types = self::$arr_types;
        return date($types[$type], $timestamp);
    }

    /**
     *转换一个日期为带星期的日期
     */
    public static function strDate2DateAndWeek($date, $type = 0, $wz = 0)
    {
        $datearr = explode("-", $date);     //将传来的时间使用“-”分割成数组
        $year = $datearr[0];       //获取年份
        $month = sprintf('%02d', $datearr[1]);  //获取月份
        $day = sprintf('%02d', $datearr[2]);      //获取日期
        $hour = $minute = $second = 0;   //默认时分秒均为0
        $dayofweek = mktime($hour, $minute, $second, $month, $day, $year);    //将时间转换成时间戳
        $shuchu = date("w", $dayofweek);      //获取星期值
        $weekarray = array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六");

        $types = array(
            'Y年m月d日',
            'Y/m/d H:i:s',
            'Y-m-d H:i:s',
            'Ymd',
            'Y-m-d',
            'm-d',
            'Y年m月d日　H:i',
            'YmdHis',
            'm月d日',
            'Y',
            'm-d H:i',
            'H:i',
            'm月',
            'd日',
        );
        if ($wz == 0) {
            return date($types[$type], strtotime($date)) . ' ' . $weekarray[$shuchu];
        } else {
            return $weekarray[$shuchu] . ' ' . date($types[$type], strtotime($date));
        }

    }


    /**
     *获取是否在线时间判定条件
     */
    public static function getOnlineTime()
    {
        $wher_time = self::Gmt2Date(time() - AppUsers::ONLINE_TIME_WHERE, '2');
        return $wher_time;
    }


    /**
     *获取本周一的日期
     */
    public static function getThisMonday($type = 0)
    {
        $types = array(
            'Y年m月d日',
            'Y/m/d H:i:s',
            'Y-m-d H:i:s',
            'Ymd',
            'Y-m-d',
            'm-d'
        );
        $cha = date("w");
        if ($cha == 0) {
            $cha = 7 - 1;
        } else {
            $cha = $cha - 1;
        }
        $ThisMonday = date($types[$type], strtotime("-$cha day"));
        return $ThisMonday;
    }


}


?>
