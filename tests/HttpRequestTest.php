<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/10
 * Time: 10:09
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\HttpRequest;
use PHPUnit\Framework\TestCase;

class HttpRequestTest extends TestCase
{
    //检查queue方法
    public function testQuery()
    {
        $http_request = new HttpRequest();
        //定义一个接口URL
        $url = 'service.agent.dexunzhenggu.cn/index.php?c=Membergoods&a=gettime&site=membergoods&v=app&appcode=5c3d73a0f1f18c30cqb5zkov';

        //请求方法
        $res = $http_request->query($url, '');
        //断言结果不为空
        $this->assertNotEmpty($res);
        //断言结果为数组
        $this->assertIsArray($res);
        if ($res['code'] == 1) {
            //断言请求结果为1
            $this->assertEquals($res['code'], 1);
        } else {
            //断言请求结果为1
            $this->assertLessThan(1, $res['code']);
        }

    }

    //检查execute方法

    public function testExecute()
    {
        $http_request = new HttpRequest();
        //定义一个接口URL
        $url = 'service.agent.dexunzhenggu.cn/index.php?c=Membergoods&a=gettime&site=membergoods&v=app&appcode=5c3d73a0f1f18c30cqb5zkov';
        //请求方法
        $res = $http_request->execute($url);
        //断言结果为字符串
        $this->assertIsString($res);
        //转化为数组
        $res = json_decode($res, true);

        //断言结果不为空
        $this->assertNotEmpty($res);
        //断言结果为数组
        $this->assertIsArray($res);
        if ($res['code'] == 1) {
            //断言请求结果为1
            $this->assertEquals($res['code'], 1);
        } else {
            //断言请求结果为1
            $this->assertLessThan(1, $res['code']);
        }
    }
}