<?php

namespace application\admin\controller;

use aaphp\Controller;
use aaphp\Model;
use aaphp\Url;

/**
 * 登录，退出登录
 * Class Login
 * @package application\admin\controller
 */
class Login extends Controller
{
    /**
     * 登录
     */
    public function login()
    {
        // 如果不是POST提交，显示模版
        if (!IS_POST) {
            return $this->fetch();
        }

        //有数据提交
        $username = $_POST["username"];
        // md5加密
        $password = md5($_POST["password"]);

        // 根据传值查找管理员
        $admin = (new Model('admin'))->where(['username', '=', $username])->selectOne();

        if ($admin['password'] == $password) {
            // 如果密码正确保存管理的昵称，和ID，
            $_SESSION['admin_id'] = $admin["admin_id"];
            $_SESSION['username'] = $admin["username"];

//            $admin_id=$admin["admin_id"];
//            $username=$admin["username"];
//            $redis = new Redis();
//            $redis->connect('10.135.45.212', 6379);
//            $redis->set(session_id().'_admin_id', $admin_id);
//            $redis->set(session_id().'_username', $username);
            // 跳转到后台首页
            $this->redirect(Url::build("admin/Index/index"));
        } else {
            // 如果密码不准确，报错，并返回
            $this->assign('errorMessage', '用户名或密码不正确，请重试');
            return $this->fetch();
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        // 清除session
        unset($_SESSION['admin_id']);
        unset($_SESSION['username']);
        // 跳转到登录页
        $this->redirect(Url::build("admin/Login/login"));
    }
}
