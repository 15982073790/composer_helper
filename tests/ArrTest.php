<?php
/**
 * Created by PhpStorm.
 * User: 李勇刚
 * Date: 2020/7/8
 * Time: 14:33
 */

namespace Mrstock\Helper\Tests;

use Mrstock\Helper\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    protected $ceshi_arr = [['id' => 110, 'name' => '张三'], ['id' => 111, 'name' => '王五'], ['id' => 112, 'name' => '李四'], ['id' => 113, 'name' => '江六'], ['id' => 114, 'name' => '刘九'], ['id' => 115, 'name' => '陆一']];

    //检查数组转换功能
    public function testArrayToArrayKey()
    {
        $arr = new  Arr();
        //判断传参不为空
        $this->assertNotEmpty($this->ceshi_arr);
        //判断传参为数组
        $this->assertIsArray($this->ceshi_arr);
        //请求方法
        $test_arr = $arr->arrayToArrayKey($this->ceshi_arr, 'id', 0);
        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }

    //检查 取数组指定的一个键值来构建新的数组 功能
    public function testArrayValueListByKey()
    {
        $arr = new  Arr();
        //判断传参为数组
        $this->assertIsArray($this->ceshi_arr);
        //请求方法
        $test_arr = $arr->arrayValueListByKey($this->ceshi_arr, 'id');
        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }

    //检查 取数组某个值为键值组成新的数组功能
    public function testReArray()
    {
        $arr = new  Arr();
        //判断传参为数组
        $this->assertIsArray($this->ceshi_arr);
        //请求方法
        $test_arr = $arr->reArray($this->ceshi_arr, 'id');
        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }

    //检查数据分页数组功能
    public function testArrPage()
    {
        $arr = new  Arr();
        $p = 1;
        $count = 3;

        //检查传参是否为数组
        $this->assertIsArray($this->ceshi_arr);

        //检查传参是否为int字符串
        $this->assertIsInt($p);
        $this->assertIsInt($count);

        //请求方法
        $test_arr = $arr->arr_page($this->ceshi_arr, $p, $count);

        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }

    //检查获取重复的数组键值功能
    public function testGetUnitIndex()
    {
        $arr = new  Arr();
        //检查传参是否为数组
        $this->assertIsArray($this->ceshi_arr);

        $arr_1 = [1, 2, 3, 3, 4, 4, 5, 6, 7, 8];

        //请求方法
        $test_arr = $arr->getUnitIndex($arr_1);

        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }

    //检查数组元素出现的次数
    public function testArrayCountValues()
    {
        $arr = new  Arr();
        //检查传参是否为数组
        $this->assertIsArray($this->ceshi_arr);

        //请求方法
        $test_arr = $arr->dictCopyByKeys($this->ceshi_arr, 4);

        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }

    //检查取数组指定的一个键来构建新的数组功能(获取数组指定键值的数组)

    public function testDictCopyByKeys()
    {
        $arr = new  Arr();

        //检查传参是否为数组
        $this->assertIsArray($this->ceshi_arr);

        //请求方法
        $test_arr = $arr->dictCopyByKeys($this->ceshi_arr, 4);

        //判断返回参数不为空
        $this->assertNotEmpty($test_arr);
    }
}