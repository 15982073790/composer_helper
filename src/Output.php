<?php

namespace Mrstock\Helper;

use Mrstock\Mjc\Facade\Debug;
use Mrstock\Mjc\Facade\Log;
use Mrstock\Mjc\Log\LogLevel;
use Mrstock\Mjc\Container;

class Output
{

    /**
     * 格式化返回数据
     *
     * @param number $code
     * @param unknown $data
     * @return string[]|number[]|unknown[]|array[]
     */
    protected static function format($data, $code = 1)
    {
        $result = array();
        $result['code'] = $code;
        if (intval($code) <= 0) {
            $result['message'] = $data;
        } else {
            $result['message'] = is_string($data) ? $data : 'ok';
            $result['data'] = $data;
        }
        $request = Container::get('request');
        if ($request->isdebug) {
            $debugData = Debug::getData();
            if ($debugData) {
                $result['debug'] = $debugData;
            }
        }

        $log = json_encode($result, JSON_UNESCAPED_UNICODE);
        Log::write($code, LogLevel::ACCESS);
        Log::write($log, LogLevel::ROUTERECORD);

        if (intval($code) <= 0) {
            if ($code == -404) {
                Log::write($log, LogLevel::ROUTENONE);
            } elseif ($code == -500) {
                Log::write($log, LogLevel::ERR);
            }
        }

        return $result;
    }

    public static function response($data, $code = 1, $status = 200)
    {
        $response = Container::get("response");

        $data = self::format($data, $code);
        return $response->data($data)->code($status);
    }

}