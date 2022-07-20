<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/9
 * Time: 14:02
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    //检查删除缓存
    public function testDelete()
    {
        $file = new File();
        $key = 'get_file_config';

        //请求方法
        $res = $file->delete($key);
        //断言不能为空
        $this->assertNotEmpty($res);
        //断言BOOL值
        $this->assertIsBool($res);
        //断言值
        $this->assertEquals($res, 1);
    }

    //检查获取缓存
    public function testSet()
    {
        $file = new File();
        $key = 'get_file_config';

        //断言为字符串
        $this->assertIsString($key);

        //断言不能为空
        $this->assertNotEmpty($key);

        //请求接口
        $res = $file->set($key, ['id' => 1, 'name' => '李四']);
        //断言不能为空
        $this->assertNotEmpty($res);
        //断言BOOL值
        $this->assertIsBool($res);
        //断言值为1
        $this->assertEquals($res, 1);
    }

    //检查获取缓存
    public function testGet()
    {
        $file = new File();
        $key = 'get_file_config';

        //断言为字符串
        $this->assertIsString($key);

        //断言不能为空
        $this->assertNotEmpty($key);

        //请求接口
        $res = $file->get($key);
        //断言之前的缓存不为空
        $this->assertNotEmpty($res);
        //根据之前存的数据断言为数据
        $this->assertIsArray($res);
    }
}