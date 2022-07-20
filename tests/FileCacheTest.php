<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/9
 * Time: 17:45
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\FileCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    //检查变量写入文件缓存
    public function testSet()
    {
        $file_cache = new FileCache();
        $key = 'file_cache';
        $data['id'] = 1;
        $data['name'] = '呵呵哒';
        //断言key不能为空
        $this->assertNotEmpty($key);
        //断言data不为空
        $this->assertNotEmpty($data);
        //请求方法
        $res = $file_cache->set($key, $data);
        //断言不能为空
        $this->assertNotEmpty($res);
        //断言BOOL值
        $this->assertIsBool($res);
        //断言值为1
        $this->assertEquals($res, 1);
    }

    //获取缓存变量内容
    public function testGet()
    {
        $file_cache = new FileCache();
        $key = 'file_cache';

        //断言key不能为空
        $this->assertNotEmpty($key);
        //请求方法
        $res = $file_cache->get($key);

        //断言不能为空
        $this->assertNotEmpty($res);

        //根据之前存的数据断言为数据
        $this->assertIsArray($res);
    }
}