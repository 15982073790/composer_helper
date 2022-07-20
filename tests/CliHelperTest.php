<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/9
 * Time: 10:22
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\CliHelper;
use PHPUnit\Framework\TestCase;

class CliHelperTest extends TestCase
{
    //检查命令行输出文字
    public function testCliEcho()
    {
        $CliHelper = new CliHelper();

        $string = '测试';
        //断言传参为字符串
        $this->assertIsString($string);
        //请求方法
        $CliHelper->cliEcho($string);

    }

    //测试命令行 休眠并输出 秒
    public function testCliSleep()
    {
        $CliHelper = new CliHelper();

        $number = 1;
        //断言传参为数字
        $this->assertIsInt($number);
        //请求方法
        $CliHelper->cliSleep($number);
    }

    //命令行 休眠并输出 微秒
    public function testCliUsleep()
    {
        $CliHelper = new CliHelper();

        $number = 1;
        //断言传参为数字
        $this->assertIsInt($number);
        //请求方法
        $CliHelper->cliUsleep($number);
    }
}