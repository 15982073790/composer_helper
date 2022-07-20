<?php

namespace Mrstock\Helper;

class Arr
{
    // 数组转换
    public static function arrayToArrayKey($arr, $field, $group = 0)
    {
        $array = [];
        if (empty($arr)) {
            return $array;
        }
        if ($group == 0) {

            foreach ($arr as $v) {
                if (array_key_exists($field, $v)) {
                    $array[$v[$field]] = $v;
                }
            }
        } else {
            foreach ($arr as $v) {
                if (array_key_exists($field, $v)) {
                    $array[$v[$field]][] = $v;
                }
            }
        }

        return $array;
    }

    //取数组指定的一个键来构建新的数组
    public static function arrayValueListByKey($arr, $k = 'id')
    {
        $newArr = [];
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                $newArr[] = $val[$k];
            }
        }
        return $newArr;
    }

    public static function reArray($Array, $k = 'id')
    {
        $newArr = [];
        if (is_array($Array)) {
            foreach ($Array as $key => $val) {
                $newArr[$val[$k]] = $val;
            }
        }
        return $newArr;
    }

    public static function arr_page($arr, $p, $count)
    {

        if (empty($p)) {
            $p = 1;
        }
        if (empty($count)) {
            $count = 10;
        }
        $start = ($p - 1) * $count;
        for ($i = $start; $i < $start + $count; $i++) {
            if (!empty($arr[$i])) {

                $new_arr[$i] = $arr[$i];
            }
        }

        return $new_arr;

    }

    public static function getUnitIndex($arr)
    {
        $arr1 = $arr;
        $arr2 = array_unique($arr);

        return array_keys(array_diff_assoc($arr1, $arr2));
    }

    public static function array_count_values($arr1)
    {
        $arr = [];
        foreach ($arr1 as $key => $value) {
            # code...
            // $value=$value."";
            if (isset($arr[$value])) {
                $arr[$value]["count"]++;

            } else {
                $arr[$value]["count"] = 1;

            }

        }
        return $arr;
    }

    //取数组指定的一个键来构建新的数组
    public static function dictCopyByKeys($arr, $k = [])
    {
        $newArr = [];
        if (is_array($arr) && !empty($k)) {
            foreach ($arr as $key => $val) {
                if ($key == $k) {
                    $newArr[$key] = $val;
                }
            }
        }
        return $newArr;
    }

}