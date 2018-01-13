<?php

namespace application\admin\controller;

use aaphp\Model;
use aaphp\Request;
use aaphp\Url;
use luojiangtao\page\Page;

/**
 * 栏目控制器
 * Class Category
 * @package application\admin\controller
 */
class Category extends Base
{
    /**
     * 栏目列表
     */
    public function categoryList()
    {
        // 统计栏目数量
        $count = (new Model('category'))->count();
        // 分页
        $page = new Page($count, 10);
        // 获取栏目数据
        $categoryList = (new Model('category'))->order('`sort` ASC')->limit($page->start_row . ',' . $page->page_size)->select();
        // 分配变量到前台模版
        $this->assign('category_list', $categoryList);
        $this->assign('page', $page->show());
        // 载入模版
        return $this->fetch();
    }

    /**
     * 栏目添加修改表单
     */
    public function categoryForm()
    {
        // 不管有没有都接收并查询文章
        $categoryId = Request::instance()->request('category_id');
        $category = (new Model('category'))->where(['category_id', '=', $categoryId])->selectOne();
        // 分配变量到前台模版
        $this->assign('category', $category);
        // 载入模版
        return $this->fetch();
    }

    /**
     * 添加栏目
     */
    public function createCategory()
    {
        $category = array(
            'category_name' => Request::instance()->request('category_name'),
            'sort' => Request::instance()->request('sort'),
            'status' => Request::instance()->request('status'),
        );
        // 添加
        (new Model('category'))->insert($category);
        // 回到列表页
        $this->redirect(Url::build('admin/Category/category_list'));
    }

    /**
     * 修改栏目
     */
    public function updateCategory()
    {
        $categoryId = Request::instance()->request('category_id');
        $category = array(
            'category_name' => Request::instance()->request('category_name'),
            'sort' => Request::instance()->request('sort'),
            'status' => Request::instance()->request('status'),
        );
        // 修改栏目
        (new Model('category'))->where(['category_id', '=', $categoryId])->update($category);
        // 回到列表页
        $this->redirect(Url::build('admin/Category/category_list'));
    }

    /**
     * 删除栏目
     */
    public function deleteCategory()
    {
        $categoryId = Request::instance()->request('category_id');

        $where = ['category_id', '=', $categoryId];
        // 查看是否该分类下面有文章，如果有请将文章转移或删除后在删除该分类
        $count = (new Model('article'))->where($where)->count();
        if ($count > 0) {
            return $this->error('该分类下面有文章，请将文章转移或删除后在删除该分类');
        }

        // 删除
        (new Model('category'))->where($where)->delete();
        // 回到列表页
        $this->redirect(Url::build('admin/Category/category_list'));
    }
}
