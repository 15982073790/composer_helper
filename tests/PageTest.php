<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/10
 * Time: 14:37
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    //检查设置属性
    public function testSet()
    {
        $page_test = new Page();

        $key = 'array_key';
        //断言为字符串
        $this->assertIsString($key);
        //断言不为空
        $this->assertNotEmpty($key);

        $value = '512541255445485646';
        //断言不为空
        $this->assertNotEmpty($key);
        //请求设置属性方法
        $res = $page_test->set($key, $value);
        //断言不为空
        $this->assertNotEmpty($res);

    }

    //检查取得属性
    public function testGet()
    {
        $page_test = new Page();

        $key = 'array_key';
        //断言为int数字
        $this->assertIsString($key);
        //断言不为空
        $this->assertNotEmpty($key);

        $value = '512541255445485646';
        //断言不为空
        $this->assertNotEmpty($key);
        //请求设置配置方法
        $res = $page_test->set($key, $value);
        //断言不为空
        $this->assertNotEmpty($res);

        //请求获取配置方法
        $res = $page_test->get($key);
        //断言不为空
        $this->assertNotEmpty($res);
    }

    //检查设置当前页码
    public function testSetNowPage()
    {
        $page_test = new Page();

        $page = 2;
        //断言为int数字
        $this->assertIsInt($page);
        //断言不为空
        $this->assertNotEmpty($page);

        //请求设置当前页码
        $res = $page_test->setNowPage($page);
        //断言不为空
        $this->assertNotEmpty($res);
        //断言为BOOL
        $this->assertIsBool($res);
        //断言值
        $this->assertEquals($res, 1);
    }

    //检查设置每页数量
    public function testSetEachNum()
    {
        $page_test = new Page();

        $num = 10;
        //断言为int数字
        $this->assertIsInt($num);
        //断言不为空
        $this->assertNotEmpty($num);

        //请求设置每页数量
        $res = $page_test->setEachNum($num);
        //断言不为空
        $this->assertNotEmpty($res);
        //断言为BOOL
        $this->assertIsBool($res);
        //断言值
        $this->assertEquals($res, 1);
    }

    //检查设置信息总数
    public function testSetTotalNum()
    {
        $page_test = new Page();

        $total_num = 100;
        //断言为int数字
        $this->assertIsInt($total_num);
        //断言不为空
        $this->assertNotEmpty($total_num);

        //请求设置信息总数
        $res = $page_test->setTotalNum($total_num);
        //断言不为空
        $this->assertNotEmpty($res);
        //断言为BOOL
        $this->assertIsBool($res);
        //断言值
        $this->assertEquals($res, 1);
    }

    //检查取当前页码
    public function testGetNowPage()
    {
        $now_page = 2;
        $page_test = new Page($now_page);
        //请求取当前页码
        $res = $page_test->getNowPage();
        //断言不为空
        $this->assertNotEmpty($res);
        //断言为BOOL
        $this->assertIsInt($res);
        //断言值
        $this->assertEquals($res, $now_page);
    }

    //检查取页码总数
    public function testGetTotalPage()
    {
        $now_page = 2;
        $page_test = new Page($now_page);
        //设置总数
        $page_test->setTotalNum(100);
        //设置每页显示条数
        $page_test->setEachNum(10);
        //请求设置页码总数
        $page_test->setTotalPage();
        //请求取页码总数
        $res = $page_test->getTotalPage();
        //断言不为空
        $this->assertNotEmpty($res);
        //断言值
        $this->assertEquals($res, 10);
    }

    //检查取信息总数
    public function testGetTotalNum()
    {
        $page_test = new Page();
        //设置总数
        $page_test->setTotalNum(100);

        //获取总数
        $res = $page_test->getTotalNum();

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为数字
        $this->assertIsInt($res);

        //断言值
        $this->assertEquals($res, 100);
    }

    //检查取每页信息数量
    public function testGetEachNum()
    {
        $page_test = new Page();
        //设置每页显示条数
        $page_test->setEachNum(10);
        //获取每页显示条数
        $res = $page_test->getEachNum();

        //断言不为空
        $this->assertNotEmpty($res);

        //断言为数字
        $this->assertIsInt($res);

        //断言值
        $this->assertEquals($res, 10);
    }

    //检查取数据库select开始值
    public function testGetLimitStart()
    {
        $page_test = new Page(2);
        //设置总数
        $page_test->setTotalNum(100);
        //设置每页显示条数
        $page_test->setEachNum(10);
        //请求取数据库select开始值
        $res = $page_test->getLimitStart();

        //断言不为空
        $this->assertNotEmpty($res);
        //断言为数字
        $this->assertIsInt($res);
        //断言值
        $this->assertEquals($res, 10);
    }

    //检查取数据库select结束值
    public function testGetLimitEnd()
    {
        $page_test = new Page(2);
        //设置总数
        $page_test->setTotalNum(100);
        //设置每页显示条数
        $page_test->setEachNum(10);
        //取数据库select结束值
        $res = $page_test->getLimitEnd();
        //断言不为空
        $this->assertNotEmpty($res);
        //断言为数字
        $this->assertIsInt($res);
        //断言值
        $this->assertEquals($res, 20);
    }

    //检查设置页码总数
    public function testSetTotalPage()
    {
        $page_test = new Page(2);
        //设置总数
        $page_test->setTotalNum(100);
        //设置每页显示条数
        $page_test->setEachNum(10);
        //请求设置页码总数
        $page_test->setTotalPage();
        //请求获取设置页码总数
        $res = $page_test->getTotalPage();
        //断言不为空
        $this->assertNotEmpty($res);
        //断言值
        $this->assertEquals($res, 10);
    }
}