<?php

namespace Mrstock\Helper;

use Mrstock\Mjc\Container;
use Mrstock\Mjc\App;

class KeyValue
{

    /**
     * 解析key前缀
     *
     * @param 当前host配置 $config
     * @return string
     */
    public static function dynamicPrefix($config)
    {
        $tmpKey = '';
        $fields = [];
        $dynamicPrefix = [];
        if (isset($config['dynamicprefix'])) {
            $fields = $config['dynamicprefix'];
        }
        if ($fields && is_array($fields)) {
            $app = Container::get("app");
            $request = $app->request;
            foreach ($fields as $field) {
                $value = $request[$field];
                if ($value) {
                    $tmpKey .= $value . ':';
                }
            }
        }

        return $tmpKey;
    }
}