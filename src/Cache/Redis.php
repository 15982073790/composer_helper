<?php

namespace Mrstock\Helper\Cache;

use Mrstock\Redis\RedisHelper;

/**
 *
 * 变量缓存类
 *
 * @author Administrator
 *
 */
class Redis implements CacheInterface
{

    protected static $keyPre = "cache:";

    public static function set($key, $data, $exp = 300)
    {
        $key = self::$keyPre . $key;
        $value = serialize($data);
        $handle = new RedisHelper();
        $exp = intval($exp);
        if ($exp > 0) {
            $result = $handle->setex($key, intval($exp), $value);
        } else {
            $result = $handle->set($key, $value);
        }
        return $result;
    }

    public static function get($key)
    {
        $key = self::$keyPre . $key;
        $handle = new RedisHelper();
        $value = $handle->get($key);
        $data = unserialize($value);
        return $data;
    }
}