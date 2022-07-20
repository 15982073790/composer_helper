<?php

namespace Mrstock\Helper;

class Loader
{
    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param string $name 字符串
     * @param integer $type 转换类型
     * @param bool $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }

    /**
     * 转换命名空间中的目录
     * @param unknown $class
     * @return string
     */
    public static function parseClass($class)
    {
        $array = explode('\\', str_replace(['/', '.'], '\\', $class));
        if (count($array) == 1) {
            return ucfirst($class);
        }
        $class = self::parseName(array_pop($array), 1);
        $class = ucfirst($class);
        $path = implode('\\', $array) . '\\' . $class;
        $path = str_replace("\\", "/", $path);

        return $path;
    }
}