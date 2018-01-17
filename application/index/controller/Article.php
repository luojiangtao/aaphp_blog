<?php

namespace application\index\controller;

use aaphp\Controller;
use aaphp\Database;
use aaphp\Model;
use aaphp\Request;
use aaphp\Url;

/**
 * 首页，文章控制器
 * Class Article
 * @package application\index\controller
 */
class Article extends Controller
{
    /**
     * 文章详情页
     */
    public function articleDetail()
    {
        // 文章ID
        $article_id = intval(Request::instance()->request('article_id', 0));
        $where = ['article_id', '=', $article_id];

        // 点击量+1
        (new Model('article'))->where($where)->increase('click_number');

        // 获取文章详情
        $article = (new Model('article'))->where($where)->selectOne();
        if (!$article) {
            // 404
            $this->redirect(Url::build('index/Index/notFound'));
        }
        $article['category_name'] = (new Model('category'))->where(['category_id', '=', $article['category_id']])->getField('category_name');
        $article['comment_number'] = (new Model('comment'))->where(['article_id', '=', $article_id])->count();

        $database = new Database();
        $sql = "SELECT t.tag_name FROM aa_article_tag at, aa_tag t WHERE t.tag_id=at.tag_id and at.article_id={$article_id}";
        $tags = $database->execute($sql);

        $tags_array = array_column($tags, 'tag_name');

        // 相关文章推荐
        $where = [
            ['category_id', '=', $article['category_id']],
            ['status', '=', 1],
        ];
        $recommend_article = (new Model('article'))->where($where)->field(['article_id', 'title'])->order('click_number DESC')->limit('5')->select();

        $comment = (new Model('comment'))->where(['article_id', '=', $article_id])->field(['comment_id', 'nickname', 'content', 'time'])->order('comment_id DESC')->limit('100')->select();

        // 分配变量到前台模版
        $this->assign('article', $article);
        $this->assign('tags_array', $tags_array);
        $this->assign('recommend_article', $recommend_article);
        $this->assign('comment', $comment);

        // 载入模版
        return $this->fetch();
    }
}
