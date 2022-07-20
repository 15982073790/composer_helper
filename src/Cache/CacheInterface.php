<?php

namespace Mrstock\Helper\Cache;

interface CacheInterface
{

    /**
     * 写入缓存
     *
     * @param unknown $key
     * @param obj $data 可以为任意数据类型
     * @param number $exp
     * <=0 永不过期 >0 过期时间为期值
     * @return boolean
     */
    public static function set($key, $data, $exp);

    /**
     * 获取缓存
     *
     * @param unknown $key
     * @return number|boolean
     */
    public static function get($key);
}