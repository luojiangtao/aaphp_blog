<?php

namespace application\index\controller;

use aaphp\Controller;
use aaphp\Model;
use aaphp\Request;

/**
 * 首页，文章控制器
 * 访问该文件夹下面的控制器，需要设置 config/common.php
 * 'moduel_status'         => true,//开启分组状态
 * Class Index
 * @package application\index\controller
 */
class Index extends Controller
{

    public function test()
    {
        var_dump($_SERVER);
    }

    /**
     * 首页
     */
    public function index()
    {
        // 栏目ID
        $category_id = intval(Request::instance()->request('category_id', 0));
        // 关键字
        $keyword = Request::instance()->request('keyword');
        // 防止报错
        $where[] = ['status', '=', 1];
        if ($category_id) {
            // 查询某个栏目的文章
            $where[] = ['category_id', '=', $category_id];
        }
        if ($keyword) {
            // 模糊匹配标题
            $where[] = ['title', 'like', "%{$keyword}%"];
        }

        // 获取文章
        $article = (new Model('article'))->where($where)->field(['article_id', 'title', 'summary', 'time', 'image', 'category_id', 'click_number'])->order('article_id DESC')->limit('0,10')->select();

        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = (new Model('category'))->field(['category_name'])->where(['category_id', '=', $value['category_id']])->getField('category_name');
            $article[$key]['comment_number'] = (new Model('comment'))->where(['article_id', '=', $value['article_id']])->count();
        }

        $carousel_figure = (new Model('carousel_figure'))->order('`sort` ASC')->select();
        // 分配变量到前台模版
        $this->assign('carousel_figure', $carousel_figure);
        $this->assign('article', $article);

        // 载入模版
        return $this->fetch();
    }

    /**
     * 异步分页
     */
    public function ajaxGetArticleList()
    {
        // 栏目ID
        $category_id = intval(Request::instance()->request('category_id', 0));
        // 关键字
        $keyword = Request::instance()->request('keyword');
        $page = Request::instance()->request('page', 0);
        // 防止报错
        $where[] = ['status', '=', 1];
        if ($category_id) {
            // 查询某个栏目的文章
            $where[] = ['category_id', '=', $category_id];
        }
        if ($keyword) {
            // 模糊匹配标题
            $where[] = ['title', 'like', "%{$keyword}%"];
        }

        // 获取文章
        $article = (new Model('article'))->where($where)->field(['article_id', 'title', 'summary', 'time', 'image', 'category_id', 'click_number'])->order('article_id DESC')->limit("{$page},2")->select();
        if (!$article) {
            $result = array(
                'status' => 0,
                'message' => '没有找到文章',
            );
            echo json_encode($result);
            return;
        }
        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = (new Model('category'))->field(['category_name'])->where(['category_id', '=', $value['category_id']])->getField('category_name');
            $article[$key]['comment_number'] = (new Model('comment'))->where(['article_id', '=', $value['article_id']])->count();
        }
        // 分配变量到前台模版
        $result = array(
            'status' => 1,
            'message' => '查询文章成功',
            'data' => $article,
        );
        echo json_encode($result);
    }

    /**
     * 详情
     */
    public function articleDetail()
    {
        // 文章ID
        $article_id = intval(Request::instance()->request('article_id', 0));
        $where = ['article_id', '=', $article_id];;
        // 获取文章详情
        $article = (new Model('article'))->where($where)->selectOne();
        // 点击量+1
        (new Model('article'))->where($where)->increase('click_number');
        // 获取栏目数据
        $category = (new Model('category'))->order('`sort` ASC')->select();
        // 分配变量到前台模版
        $this->assign('category', $category);
        $this->assign('article', $article);
        // 载入模版
        return $this->fetch();
    }

    /**
     * 404 没有找到控制器或方法时执行
     */
    public function notFound()
    {
        return $this->fetch();
    }
}
