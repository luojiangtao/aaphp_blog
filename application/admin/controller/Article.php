<?php

namespace application\admin\controller;

use aaphp\Database;
use aaphp\Model;
use aaphp\Request;
use aaphp\Url;
use Gregwar\Captcha\CaptchaBuilder;
use luojiangtao\page\Page;
use luojiangtao\upload\Upload;

/**
 * 文章控制器
 * Class Article
 * @package application\admin\controller
 */
class Article extends Base
{
    public function test()
    {
        $builder = new CaptchaBuilder;
        $builder->build();
        header('Content-type: image/jpeg');
        $builder->output();
    }

    /**
     * 文章列表
     */
    public function articleList()
    {
        // 搜索关键字
        $keyword = Request::instance()->request('keyword');
        // 防止报错
        $where[] = ['status', '=', 1];
        if ($keyword) {
            // 模糊匹配标题
            $where[] = ['title', 'like', "%{$keyword}%"];
        }
        // 统计文章总数
        $count = (new Model('article'))->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取文章
        $article = (new Model('article'))->order('article_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();

        // 实例化分类数据库类
        $categoryModel = new Model('category');
        $commentModel = new Model('comment');
        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = $categoryModel->where(['category_id', '=', $value['category_id']])->getField('category_name');
            $article[$key]['comment_number'] = $commentModel->where(['article_id', '=', $value['article_id']])->count();
        }

        // 分配变量到前台模版
        $this->assign('article', $article);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        return $this->fetch();
    }

    /**
     * 文章列表
     */
    public function articleRecycleList()
    {
        // 搜索关键字
        $keyword = Request::instance()->request('keyword');
        $where[] = ['status', '=', 0];
        if ($keyword) {
            // 模糊匹配标题
            $where[] = ['title', 'like', "%{$keyword}%"];
        }
        // 统计文章总数
        $count = (new Model('article'))->where($where)->count();
        // 分页
        $page = new Page($count, 10);
        // 获取文章
        $model = new Model('article');
        $article = $model->order('article_id DESC')->where($where)->limit($page->start_row . ',' . $page->page_size)->select();

        // 实例化分类数据库类
        $categoryModel = new Model('category');
        $commentModel = new Model('comment');
        foreach ($article as $key => $value) {
            $article[$key]['category_name'] = $categoryModel->where(['category_id', '=', $value['category_id']])->getField('category_name');
            $article[$key]['comment_number'] = $commentModel->where(['article_id', '=', $value['article_id']])->count();
        }

        // 分配变量到前台模版
        $this->assign('article', $article);
        // 分页
        $this->assign('page', $page->show());
        // 载入模版
        return $this->fetch();
    }

    /**
     * 改变文章状态 status=0 放入回收站，status=1 放入正常列表
     */
    public function changeStatus()
    {
        // 搜索关键字
        $status = Request::instance()->request('status');
        $articleId = Request::instance()->request('article_id');

        $article = array(
            'status' => $status,
        );
        (new Model('article'))->where(['article_id', '=', $articleId])->update($article);

        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 文章添加，修改表单
     */
    public function articleForm()
    {
        // 不管有没有都接收并查询文章
        $articleId = Request::instance()->request('article_id');
        $model = new Model('article');
        $article = $model->where(['article_id', '=', $articleId])->selectOne();
        if ($article) {
            $database = new Database();
            $sql = "SELECT t.tag_name FROM aa_article_tag at, aa_tag t WHERE t.tag_id=at.tag_id and at.article_id={$article['article_id']}";
            $tags = $database->execute($sql);
            if ($tags) {
                $tags_array = array();
                foreach ($tags as $key => $value) {
                    $tags_array[] = $value['tag_name'];
                }
                // 标签
                $tags = implode('，', $tags_array);
            } else {
                $tags = '';
            }
            $article['tags'] = $tags;
        }

        $category = (new Model('category'))->select();

        // 分配变量到前台模版
        $this->assign('article', $article);
        $this->assign('category', $category);
        // 载入模版
        return $this->fetch();
    }

    /**
     * 添加文章
     */
    public function createArticle()
    {
        // 需要添加的数据
        $article = array(
            'title' => Request::instance()->request('title'),
            'summary' => Request::instance()->request('summary'),
            'content' => Request::instance()->request('content'),
            'category_id' => Request::instance()->request('category_id'),
            'time' => time(),
        );
        $tags = Request::instance()->request('tags');

        // 上传图片
        $upload = new Upload();
        $fileInfo = $upload->upload('image');
        if ($fileInfo['filename']) {
            // 如果上传成功，保存图片名称
            $article['image'] = $fileInfo['filename'];
        }

        // 添加
        $articleId = (new Model('article'))->insert($article);
        if (!$articleId) {
            $this->error('添加文章失败');
        }

        // 标签
        if ($tags) {
            $tags = str_replace('，', ',', $tags);
            $tags = explode(',', $tags);
            foreach ($tags as $key => $value) {
                $tagId = (new Model('tag'))->where(['tag_name', '=', $value])->getField('tag_id');
                if (!$tagId) {
                    $data = array(
                        'tag_name' => $value,
                    );
                    $tagId = (new Model('tag'))->insert($data);
                }
                $data = array(
                    'article_id' => $articleId,
                    'tag_id' => $tagId,
                );
                (new Model('article_tag'))->insert($data);
            }
        }

        // 回到列表页
        $this->redirect(Url::build('admin/Article/article_list'));
    }

    /**
     * 修改文章
     */
    public function updateArticle()
    {
        $articleId = Request::instance()->request('article_id');
        $tags = Request::instance()->request('tags');
        // 需要修改的数据
        $article = array(
            'title' => Request::instance()->request('title'),
            'summary' => Request::instance()->request('summary'),
            'content' => Request::instance()->request('content'),
            'category_id' => Request::instance()->request('category_id'),
            'time' => time(),
        );

        // 上传图片
        $upload = new Upload(ROOT_PATH . '/upload');
        $fileInfo = $upload->upload('image');
        if ($fileInfo['filename']) {
            // 如果上传成功，保存图片名称
            $article['image'] = $fileInfo['filename'];
            // 获取以前的logo地址
            $image = (new Model("article"))->where(['article_id', '=', $articleId])->getField("image");
            if ($image) {
                // 补全以前的logo地址
                $image = "./upload/" . $image;
                // 删除以前的logo地址
                if (file_exists($image)) {
                    @unlink($image);
                }
            }
        }

        // 更新文章
        (new Model('article'))->where(['article_id', '=', $articleId])->update($article);

        if ($tags) {
            $tags = str_replace('，', ',', $tags);
            $tags = explode(',', $tags);
            // 删除全部文章和标签的关系
            (new Model('article_tag'))->where(['article_id', '=', $articleId])->delete();
            foreach ($tags as $key => $value) {
                $tagId = (new Model('tag'))->where(['tag_name', '=', $value])->getField('tag_id');
                if (!$tagId) {
                    $data = array(
                        'tag_name' => $value,
                    );
                    $tagId = (new Model('tag'))->insert($data);
                }
                $data = array(
                    'article_id' => $articleId,
                    'tag_id' => $tagId,
                );
                (new Model('article_tag'))->insert($data);
            }
        }

        // 回到列表页
        $this->redirect(Url::build('admin/Article/article_list'));
    }

    /**
     * 删除
     */
    public function deleteArticle()
    {
        $articleId = Request::instance()->request('article_id');
        $where = ['article_id', '=', $articleId];
        // 删除
        (new Model('article'))->where($where)->delete();
        // 回到列表页
        $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
