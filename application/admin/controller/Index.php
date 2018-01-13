<?php

namespace application\admin\controller;

use aaphp\Model;
use aaphp\Request;
use aaphp\Url;


/**
 * 后台首页控制器
 * Class Index
 * @package application\admin\controller
 */
class Index extends Base
{
    /**
     * 后台首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 修改密码
     */
    public function changePassword()
    {
        // 如果不是POST提交，显示模版
        if (!IS_POST) {
            return $this->fetch();
        }
        $adminId = $_SESSION['admin_id'];

        $data = array(
            'password' => md5(Request::instance()->request('password'))
        );
        (new Model('admin'))->where(['admin_id', '=', $adminId])->update($data);

        // 跳转到后台首页
        $this->redirect(Url::build("Index/index"));
    }

}
