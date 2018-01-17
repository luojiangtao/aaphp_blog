<?php

namespace application\admin\controller;

use aaphp\Model;
use aaphp\Request;
use aaphp\Url;
use luojiangtao\page\Page;

/**
 * 标签控制器
 * Class Tag
 * @package application\admin\controller
 */
class Tag extends Base
{
    /**
     * 列表
     */
    public function tagList()
    {
        // 搜索关键字
        $keyword = Request::instance()->request('keyword');
        // 防止报错
        $where = " ";
        if ($keyword) {
            // 模糊匹配标题
            $where = ['tag_name', 'like', "%{$keyword}%"];
        }

        // 统计标签总数
        $count = (new Model('tag'))->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取标签
        $tag = (new Model('tag'))->order('tag_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();

        // 分配变量到前台模版
        $this->assign('tag', $tag);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        return $this->fetch();
    }

    /**
     * 删除
     */
    public function deleteTag()
    {
        $tagId = Request::instance()->request('tag_id');
        $where = ['tag_id', '=', $tagId];
        // 删除
        (new Model('tag'))->where($where)->delete();
        // 删除关系
        (new Model('article_tag'))->where($where)->delete();
        // 回到列表页
        $this->redirect(Url::build('admin/Tag/tagList'));
    }
}
