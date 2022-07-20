<?php

namespace Mrstock\Helper;

/**
 * 分页类
 *
 *
 * @package
 *
 */
class Page
{

    /**
     * 信息总数
     */
    private $total_num = 1;

    /**
     * 页码链接
     */
    private $page_url = '';

    /**
     * 每页信息数量
     */
    private $each_num = 10;

    /**
     * 当前页码
     */
    public $now_page = '';

    /**
     * 设置页码总数
     */
    private $total_page = 1;

    /**
     * 构造函数
     *
     * 数据库使用到的方法：
     * $this->setTotalNum($total_num);
     * $this->getLimitStart();
     * $this->getLimitEnd();
     *
     * @param
     *
     * @return
     *
     */
    public function __construct($nowPage = 1)
    {
        $this->setNowPage($nowPage);
    }

    /**
     * 取得属性
     *
     * @param string $key
     *            属性键值
     * @return string 字符串类型的返回结果
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     * 设置属性
     *
     * @param string $key
     *            属性键值
     * @param string $value
     *            属性值
     * @return bool 布尔类型的返回结果
     */
    public function set($key, $value)
    {
        return $this->$key = $value;
    }

    /**
     * 设置当前页码
     *
     * @param int $page
     *            当前页数
     * @return bool 布尔类型的返回结果
     */
    public function setNowPage($page)
    {
        $this->now_page = intval($page) > 0 ? intval($page) : 1;
        return true;
    }

    /**
     * 设置每页数量
     *
     * @param int $num
     *            每页显示的信息数
     * @return bool 布尔类型的返回结果
     */
    public function setEachNum($num)
    {
        $this->each_num = intval($num) > 0 ? intval($num) : 10;
        return true;
    }

    /**
     * 设置信息总数
     *
     * @param int $total_num
     *            信息总数
     * @return bool 布尔类型的返回结果
     */
    public function setTotalNum($total_num)
    {
        $this->total_num = $total_num;
        return true;
    }

    /**
     * 取当前页码
     *
     * @param
     *
     * @return int 整型类型的返回结果
     */
    public function getNowPage()
    {
        return $this->now_page;
    }

    /**
     * 取页码总数
     *
     * @param
     *
     * @return int 整型类型的返回结果
     */
    public function getTotalPage()
    {
        if ($this->total_page == 1) {
            $this->setTotalPage();
        }
        return $this->total_page;
    }

    /**
     * 取信息总数
     *
     * @param
     *
     * @return int 整型类型的返回结果
     */
    public function getTotalNum()
    {
        return $this->total_num;
    }

    /**
     * 取每页信息数量
     *
     * @param
     *
     * @return int 整型类型的返回结果
     */
    public function getEachNum()
    {
        return $this->each_num;
    }

    /**
     * 取数据库select开始值
     *
     * @param
     *
     * @return int 整型类型的返回结果
     */
    public function getLimitStart()
    {
        if ($this->getNowPage() <= 1) {
            $tmp = 0;
        } else {
            $this->setTotalPage();
            $tmp = ($this->getNowPage() - 1) * $this->getEachNum();
            if ($tmp < 0) {
                $tmp = 0;
            }
        }
        return $tmp;
    }

    /**
     * 取数据库select结束值
     *
     * @param
     *
     * @return int 整型类型的返回结果
     */
    public function getLimitEnd()
    {
        $tmp = $this->getNowPage() * $this->getEachNum();
        if ($tmp > $this->getTotalNum()) {
            $tmp = $this->getTotalNum();
        }
        return $tmp;
    }

    /**
     * 设置页码总数
     *
     * @param int $id
     *            记录ID
     * @return array $rs_row 返回数组形式的查询结果
     */
    public function setTotalPage()
    {
        $this->total_page = ceil($this->getTotalNum() / $this->getEachNum());
    }
}
