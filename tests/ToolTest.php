<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/10
 * Time: 15:34
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Tool;
use PHPUnit\Framework\TestCase;

class ToolTest extends TestCase
{
    //检查在特定字符串后面加0.00000
    public function testSctonum()
    {
        $tool = new Tool();

        $res = $tool::sctonum('11234e1');
        //断言不为空
        $this->assertNotEmpty($res);
        //断言值
        $this->assertEquals($res, 112340.00000);
    }

    //检查获得IP
    public function testGetIp()
    {
        $tool = new Tool();
        $_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';

        $res = $tool::getIp();
        //断言不为空
        $this->assertNotEmpty($res);
        //断言值
        $this->assertEquals($res, '127.0.0.1');
    }

    //检查创建文件
    public function testMkDir()
    {
        $tool = new Tool();

        $dir = 'ceshi';
        $res = $tool->mkDir($dir);

        //断言不为空
        $this->assertNotEmpty($res);
        //断言值
        $this->assertEquals($res, 1);
    }

    //检查加密函数
    public function testEncrypt()
    {
        $tool = new Tool();

        $txt = '45455525535565653665565665';
        $key = 'wodemd5123';

        $res = $tool->encrypt($txt, $key);

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为字符串
        $this->assertIsString($res);

    }

    //检查解密函数
    public function testDecrypt()
    {
        $tool = new Tool();
        $txt = 'y08KzZelTw19UgJfcg881bu7lYjk0Wv0W4Kh2AI1Y-X1k6Mez7Yu0UU';
        $key = 'wodemd5123';

        $res = $tool->decrypt($txt, $key);

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为字符串
        $this->assertIsString($res);

        //断言值
        $this->assertEquals($res, '45455525535565653665565665');
    }

    //检查数组转字符串
    public function testArrToStr()
    {
        $tool = new Tool();

        $data['id'] = 1;
        $data['name'] = '呵呵哒';

        $res = $tool->arrToStr($data);

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为字符串
        $this->assertIsString($res);
    }

    //检查字符串转数组
    public function testStrToArr()
    {
        $tool = new Tool();

        $string = '{"id":1,"name":"呵呵哒"}';

        $res = $tool->strToArr($string);

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为字符串
        $this->assertIsArray($res);

        //断言id值
        $this->assertEquals($res['id'], 1);
        //断言name值
        $this->assertEquals($res['name'], '呵呵哒');

    }

    //检查获取当前微秒时间戳
    public function testMsectime()
    {
        $tool = new Tool();

        $res = $tool->msectime();

        //断言不为空
        $this->assertNotEmpty($res);

        //断言结果为13位
        $this->assertEquals(strlen($res), 13);

    }

    //检查获取特定的的微秒日期时间
    public function testGetMicrotimeFormat()
    {
        $tool = new Tool();

        $res = $tool->get_microtime_format(time());
        //断言不为空
        $this->assertNotEmpty($res);

    }
}