<?php

namespace Mrstock\Helper\Cache;

use Mrstock\Helper\File as FS;

/**
 *
 * 变量文件缓存类
 *
 * @author Administrator
 *
 */
class File implements CacheInterface
{

    protected static $keyPre = "cache:";

    public static function set($key, $data, $exp = 300)
    {
        $key = self::$keyPre . $key;
        $handle = new FS();
        $result = $handle->set($key, $data, intval($exp));

        return $result;
    }

    public static function get($key)
    {
        $key = self::$keyPre . $key;
        $handle = new FS();
        $value = $handle->get($key);
        return $value;
    }

    public static function delete($key)
    {
        $key = self::$keyPre . $key;
        $handle = new FS();
        $value = $handle->delete($key);
        return $value;
    }
}