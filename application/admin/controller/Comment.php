<?php

namespace application\admin\controller;

use aaphp\Model;
use aaphp\Request;
use aaphp\Url;
use luojiangtao\page\Page;

/**
 * 评论控制器
 * Class Comment
 * @package application\admin\controller
 */
class Comment extends Base
{
    /**
     * 评论列表
     */
    public function commentList()
    {
        // 搜索关键字
        $keyword = Request::instance()->request('keyword');
        // 防止报错
        $where = [];
        if ($keyword) {
            // 模糊匹配标题
            $where = ['nickname', 'like', "%{$keyword}%"];
        }

        // 统计评论总数
        $count = (new Model('comment'))->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取评论
        $comment = (new Model('comment'))->order('comment_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();

        // 分配变量到前台模版
        $this->assign('comment', $comment);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        return $this->fetch();
    }

    /**
     * 删除
     */
    public function deleteComment()
    {
        $comment_id = Request::instance()->request('comment_id');
        $where = ['comment_id', '=', $comment_id];
        // 删除
        (new Model('comment'))->where($where)->delete();
        // 回到列表页
        $this->redirect(Url::build('admin/Comment/commentList'));
    }
}
