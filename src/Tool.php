<?php

namespace Mrstock\Helper;

class Tool
{
    public static function sctonum($num, $double = 5)
    {
        if (false !== stripos($num, "e")) {
            $a = explode("e", strtolower($num));
            return bcmul($a[0], bcpow(10, $a[1], $double), $double);
        }
    }

    public static function getIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != 'unknown') {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown') {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '';
        }
        return preg_match('/^\d[\d.]+\d$/', $ip) ? $ip : '';
    }

    public static function mkDir($dir, $mode = '0777')
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        return false;
    }

    /**
     * 加密函数
     *
     * @param string $txt
     *            需要加密的字符串
     * @param string $key
     *            密钥
     * @return string 返回加密结果
     */
    public static function encrypt($txt, $key = '')
    {
        if (empty($txt))
            return $txt;
        if (empty($key))
            $key = md5(MD5_KEY);
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
        $nh1 = rand(0, 64);
        $nh2 = rand(0, 64);
        $nh3 = rand(0, 64);
        $ch1 = $chars[$nh1];
        $ch2 = $chars[$nh2];
        $ch3 = $chars[$nh3];
        $nhnum = $nh1 + $nh2 + $nh3;
        $knum = 0;
        $i = 0;
        while (isset($key[$i]))
            $knum += ord($key[$i++]);
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $txt = base64_encode(time() . '_' . $txt);
        $txt = str_replace(array(
            '+',
            '/',
            '='
        ), array(
            '-',
            '_',
            '.'
        ), $txt);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = strlen($txt);
        $klen = strlen($mdKey);
        for ($i = 0; $i < $tlen; $i++) {
            $k = $k == $klen ? 0 : $k;
            $j = ($nhnum + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        $tmplen = strlen($tmp);
        $tmp = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
        $tmp = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
        $tmp = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);
        return $tmp;
    }

    /**
     * 解密函数
     *
     * @param string $txt
     *            需要解密的字符串
     * @param string $key
     *            密匙
     * @return string 字符串类型的返回结果
     */
    public static function decrypt($txt, $key = '', $ttl = 0)
    {
        if (empty($txt))
            return $txt;
        if (empty($key))
            $key = md5(MD5_KEY);

        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
        $ikey = "-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
        $knum = 0;
        $i = 0;
        $tlen = @strlen($txt);
        while (isset($key[$i]))
            $knum += ord($key[$i++]);
        $ch1 = @$txt[$knum % $tlen];
        $nh1 = strpos($chars, $ch1);
        $txt = @substr_replace($txt, '', $knum % $tlen--, 1);
        $ch2 = @$txt[$nh1 % $tlen];
        $nh2 = @strpos($chars, $ch2);
        $txt = @substr_replace($txt, '', $nh1 % $tlen--, 1);
        $ch3 = @$txt[$nh2 % $tlen];
        $nh3 = @strpos($chars, $ch3);
        $txt = @substr_replace($txt, '', $nh2 % $tlen--, 1);
        $nhnum = $nh1 + $nh2 + $nh3;
        $mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
        $tmp = '';
        $j = 0;
        $k = 0;
        $tlen = @strlen($txt);
        $klen = @strlen($mdKey);
        for ($i = 0; $i < $tlen; $i++) {
            $k = $k == $klen ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nhnum - ord($mdKey[$k++]);
            while ($j < 0)
                $j += 64;
            $tmp .= $chars[$j];
        }
        $tmp = str_replace(array(
            '-',
            '_',
            '.'
        ), array(
            '+',
            '/',
            '='
        ), $tmp);
        $tmp = trim(base64_decode($tmp));

        if (preg_match("/\d{10}_/s", substr($tmp, 0, 11))) {
            if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl)) {
                $tmp = null;
            } else {
                $tmp = substr($tmp, 11);
            }
        }
        return $tmp;
    }

    public static function arrToStr($arr)
    {

        $str = json_encode($arr, JSON_UNESCAPED_UNICODE);

        return $str;
    }

    public static function strToArr($str)
    {
        $arr = json_decode($str, true);
        return $arr;
    }

    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    public static function get_microtime_format($time)
    {
        if (strstr($time, '.')) {
            sprintf("%01.3f", $time); //小数点。不足三位补0
            list($usec, $sec) = explode(".", $time);
            $sec = str_pad($sec, 3, "0", STR_PAD_RIGHT); //不足3位。右边补0
        } else {
            $usec = $time;
            $sec = "000";
        }
        $date = date("Y-m-d H:i:s.x", $usec);
        return str_replace('x', $sec, $date);
    }

    /**
     * @param $arr
     * @return array|string
     * @comment 递归方式把数组或字符串 null转换为空'',全转字符串
     */
    public static function _unsetNull($arr)
    {
        if ($arr !== null) {
            if (is_array($arr)) {
                if (!empty($arr)) {
                    foreach ($arr as $key => $value) {
                        if ($value === null) {
                            $arr[$key] = '';
                        } elseif (is_int($arr) || is_float($arr)) {
                            $arr[$key] = (string)$value;
                        } else {
                            $arr[$key] = static::_unsetNull($value);      //递归再去执行
                        }
                    }
                } else {
                    $arr = [];
                }
            } else {
                if ($arr === null) {
                    $arr = '';
                } elseif (is_int($arr) || is_float($arr)) {
                    $arr = (string)$arr;
                }         //注意三个等号
            }
        } else {
            $arr = '';
        }
        return $arr;
    }

}