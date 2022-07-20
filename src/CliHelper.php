<?php

namespace Mrstock\Helper;

use Mrstock\Mjc\Facade\Log;
use Mrstock\Mjc\Log\LogLevel;

class CliHelper
{

    /**
     * 命令行开始
     */
    public static function cliStart()
    {
        if (ob_get_level()) ob_end_clean();
    }

    /**
     * @param $message
     * 命令行输出文字
     */
    public static function cliEcho($message)
    {
        //if(defined("APP_DEBUG")&&APP_DEBUG==true){
        //Log::write($message, LogLevel::CLIECHO);
        fwrite(STDOUT, $message . PHP_EOL);
        self::cliFlush();
        //}

    }

    /**
     * 命令行 休眠并输出
     * @param $seconds
     */
    public static function cliSleep($seconds)
    {
        $message = 'Sleep ' . $seconds . 'S';
        self::cliEcho($message);
        sleep($seconds);
    }

    /**
     * 命令行 休眠并输出
     * @param $seconds
     */
    public static function cliUsleep($micro_second)
    {
        $message = 'Usleep ' . $micro_second . ' micro_second';
        self::cliEcho($message);
        usleep($micro_second);
    }


    /**
     * 命令行 及时输出
     */
    public static function cliFlush()
    {
        flush();
//        ob_flush();
        if (ob_get_level() > 0) ob_flush();
    }

}