<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/9
 * Time: 13:05
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    //检查 根据配置文件夹加载配置文件
    public function testRegister()
    {
        $cofig = new Config();
        //获取config配置
        $staticConfig = $cofig::$staticConfig;
        //断言配置为空
        $this->assertEmpty($staticConfig);
    }

    //检查根据Key获取配置文件
    public function testGet()
    {
        $cofig = new Config();
        //获取config配置

        $key = 'ceshi';

        //断言为字符串
        $this->assertIsString($key);

        //请求方法
        $cofig->get($key);
    }

    //检查注销 key 方便重置
    public function testUnsetKey()
    {
        $cofig = new Config();
        //获取config配置

        $key = 'ceshi';

        //断言为字符串
        $this->assertIsString($key);

        //请求方法
        $res = $cofig->unsetKey($key);

        //断言为bool值
        $this->assertTrue($res);

        //断言为true
        $this->assertEquals($res, 1);
    }

    //检查设置config配置
    public function testSet()
    {
        $cofig = new Config();

        //模拟配置
        $cofig_arr['config'] = '5c3d73a0f1f18c30cqb5zkov';

        //请求方法
        $cofig->set($cofig_arr);
        //获取config配置
        $staticConfig = $cofig::$staticConfig;

        //断言配置不为空
        $this->assertNotEmpty($staticConfig);

        //断言为数组
        $this->assertIsArray($staticConfig);

    }
}