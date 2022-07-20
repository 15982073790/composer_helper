<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/9
 * Time: 17:58
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Config;
use Mrstock\Helper\Ftp;
use PHPUnit\Framework\TestCase;

class FtpTest extends TestCase
{
    //检查配置FTP配置
    public function testSetFtpConfig()
    {
        //模拟配置
        $config['ftp']['default'] = [
            'server' => '192.168.10.200',
            'username' => 'app',
            'password' => '123456',
            'port' => '21',
            'url' => 'http://static.guxiansheng.cn'
        ];
        //断言不能为空
        $this->assertNotEmpty($config);
        //断言为数组
        $this->assertIsArray($config);

        //获取用户配置
        Config::set($config);
    }

    //检查FTP配置是否存在
    public function testFtpConfig()
    {
        //获取用户配置
        $ftp_config = Config::get("ftp");

        //断言不为空
        $this->assertNotEmpty($ftp_config);
    }

    //检查ftp开始
    public function testStart()
    {
        $ftp = new Ftp();
        $res = $ftp->start();
        //断言返回不为空
        $this->assertNotEmpty($res);
        //断言BOOL值
        $this->assertIsBool($res);
        //断言值为1
        $this->assertEquals($res, 1);
    }

    //检查上传文件
    public function testPut()
    {
        $ftp = new Ftp();

        //获取用户配置
        $ftp_config = Config::get("ftp");
        //断言不为空
        $this->assertNotEmpty($ftp_config);
        //请求方法
        $res = $ftp->put($ftp_config['default']['server'], 'l27.0.0.1');
        //断言BOOL值
        $this->assertIsBool($res);
    }

    //检查获取错误信息
    public function testGetError()
    {
        $ftp = new Ftp();

        //请求方法
        $res = $ftp->getError();

        //由于模拟不了错误信息 断言结果为空
        $this->assertEmpty($res);
    }

    //检查关闭Ftp链接
    public function testClose()
    {
        $ftp = new Ftp();
        //请求方法
        $res = $ftp->close();

        //断言结果为null值
        $this->assertNull($res);
    }
}