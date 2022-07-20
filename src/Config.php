<?php

namespace Mrstock\Helper;

class Config
{

    public static $staticConfig = [];

    /**
     * 清空config
     *
     * @param unknown $configPath
     */
    public static function clear()
    {
        self::$staticConfig = [];
    }

    /**
     * 根据配置文件夹加载配置文件
     *
     * @param unknown $configPath
     */
    public static function register()
    {

        // 配置文件只加载一次
        if (empty(self::$staticConfig)) {
            if (defined("VENDOR_CONFIG_DIR") && VENDOR_CONFIG_DIR) {
                self::import(VENDOR_CONFIG_DIR);
            } else {
                self::import(VENDOR_DIR . '/../config');
            }
            if (defined("CONFIG_DIR") && CONFIG_DIR) {
                self::import(CONFIG_DIR);
            }
        }

    }

    private static function import($configDir)
    {
        foreach (glob($configDir . '/*.ini.php') as $start_file) {
            $config = [];
            $config = require_once $start_file;
            self::Set($config);
        }
        // 支持向下一级查找配置文件
        foreach (glob($configDir . '/*/*.ini.php') as $start_file) {
            $config = [];
            $config = require_once $start_file;
            self::Set($config);
        }
    }

    /**
     * 根据Key获取配置文件
     *
     * @param unknown $key
     */
    public static function get($key = '')
    {
        if (empty($key)) {
            return self::$staticConfig;
        }
        $keyName = explode('.', $key);
        $config = self::$staticConfig;
        foreach ($keyName as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return null;
            }
        }

        return $config;

    }

    /**
     * 注销 key 方便重置
     * @param string $key
     *
     * @return array|mixed
     */
    public static function unsetKey($key = '')
    {
        if (empty($key)) {
            return true;
        }
        if (strpos($key, '.')) {
            $key = explode('.', $key);
            if (isset($key[2])) {
                if (isset(self::$staticConfig[$key[0]][$key[1]][$key[2]])) {
                    unset(self::$staticConfig[$key[0]][$key[1]][$key[2]]);
                }
            } else {
                if (isset(self::$staticConfig[$key[0]][$key[1]])) {
                    unset(self::$staticConfig[$key[0]][$key[1]]);
                }
            }
        } else {
            if (isset(self::$staticConfig[$key])) {
                unset(self::$staticConfig[$key]);
            }
        }
        return true;
    }

    /**
     * 手动加载配置文件
     *
     * @param unknown $config
     */
    public static function set($config)
    {
        if (!empty($config) && is_array($config)) {
            self::$staticConfig = @array_merge_recursive(self::$staticConfig, $config);
        }
    }
}