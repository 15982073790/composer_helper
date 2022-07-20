<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/12
 * Time: 15:09
 */

namespace Mrstock\Helper\Tests;


use Mrstock\Helper\Upload;
use PHPUnit\Framework\TestCase;

class UploadTest extends TestCase
{
    //检查使用 $_FILES 控件上传
    public function testFile()
    {
        $upload = new Upload();

        $field = 'ceshi,jpg';

        $res = $upload->file($field);
        //断言为空
        $this->assertEmpty($res);
    }

    //检查base64 上传图片 (文件后缀名为.jpg)
    public function testBase64()
    {
        $upload = new Upload();

        $string = 'mxzdaksdsadklasjaSJKSasa=.jpg';

        $res = $upload->base64($string);
        //断言为空
        $this->assertNotEmpty($res);
    }

    //检查获得错误信息
    public function testGetError()
    {
        $upload = new Upload();

        $res = $upload->getError();
        //断言为空
        $this->assertEmpty($res);
    }
}