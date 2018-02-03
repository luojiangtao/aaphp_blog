<?php

namespace application\controller;

use aaphp\Controller;

/**
 * 访问该文件夹下面的控制器，需要设置 config/common.php
 * 'moduel_status'         => false,//关闭分组状态
 * Class ViewController
 * @package application\example\controller
 */
class Index extends Controller
{
    /**
     * 首页
     * @return string
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 测试
     */
    public function test()
    {
        var_dump('test');
    }

    /**
     * 404 没有找到控制器或方法时执行
     */
    public function notFound()
    {
        return $this->fetch();
    }
}
