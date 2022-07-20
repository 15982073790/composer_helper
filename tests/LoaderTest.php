<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/10
 * Time: 13:27
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    //检查字符串命名风格转换
    public function testParseName()
    {
        $loader = new Loader();

        //请求方法
        $res = $loader::parseName('hehhehe');

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为字符串
        $this->assertIsString($res);

    }

    //检查转换命名空间中的目录
    public function testParseClass()
    {
        $loader = new Loader();

        //请求方法
        $res = $loader::parseName('./push/desi/ceshi');

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为字符串
        $this->assertIsString($res);
    }
}