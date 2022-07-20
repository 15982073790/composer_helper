<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/10
 * Time: 11:14
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Config;
use Mrstock\Helper\KeyValue;
use PHPUnit\Framework\TestCase;

class KeyValueTest extends TestCase
{
    //检查解析key前缀
    public function testDynamicPrefix()
    {
        $key_value = new KeyValue();
        $key_config['dynamicprefix'] = 'dsjsjdnjkdsjdkasdnjsaasddsdsdsssssssssssssssssssssss';
        //断言为数组
        $this->assertIsArray($key_config);

        //请求方法
        $res = $key_value->dynamicPrefix($key_config);

        //断言返回为空
        $this->assertEmpty($res);
    }
}