<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/10
 * Time: 13:41
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Output;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    //检查格式化返回数据
    public function testResponse()
    {
        $output = new Output();

        $data = 'jksdasdjsadaskd';
        define('APP_PATH', 'membergoods');
        $res = $output->response($data);
        //断言不为空
        $this->assertNotEmpty($res);
        //断言为字符串
        $this->assertIsObject($res);
    }


}