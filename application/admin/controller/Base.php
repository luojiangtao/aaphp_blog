<?php

namespace application\admin\controller;

use aaphp\Controller;
use aaphp\Url;

/**
 * 基类
 * Class Base
 * @package application\admin\controller
 */
class Base extends Controller
{
    /**
     * 框架初始化方法，如果没有登录跳转到登录页面
     */
    public function __construct()
    {
        // redis 共享session
//        $redis = new Redis();
//        $redis->connect('10.135.45.212', 6379);
//        $admin_id = $redis->get(session_id().'_admin_id');
//        $username = $redis->get(session_id().'_username');
        $adminId = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;

        // 如果没有登录，跳转到登录页
        if (!$adminId) {
            $this->redirect(Url::build('admin/Login/login'));
        }
    }

    /**
     * 错误方法
     * @param string $message [错误信息]
     * @return string
     */
    public function error($message = '错误')
    {
        // 分配错误信息到前台模版
        $this->assign('message', $message);
        // 载入模版
        return $this->fetch('Index/error');
    }
}
