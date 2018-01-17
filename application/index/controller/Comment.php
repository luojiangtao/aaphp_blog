<?php

namespace application\index\controller;

use aaphp\Controller;
use aaphp\Model;
use aaphp\Request;
use application\index\validate\CommentValidate;

/**
 * 首页，评论控制器
 * Class Comment
 * @package application\index\controller
 */
class Comment extends Controller
{
    /**
     * 评论详情页
     */
    public function createComment()
    {
        $articleId = Request::instance()->request('article_id');
        $request = Request::instance();
        $comment = array(
            'article_id' => $articleId,
            'nickname' => $request->request('nickname'),
            'email' => $request->request('email'),
            'content' => $request->request('content'),
//            csrf验证
            '_token_' => $request->post('_token_'),
            'time' => time(),
        );

//        数据验证
        $validate = new CommentValidate();
        if (!$validate->check($comment)) {
            $errorMessage = current($validate->getError());
            $errorMessage = current($errorMessage);
            $this->redirect(__ROOT__ . '/' . $articleId . '.html?errorMessage=' . $errorMessage);
            return;
        }

        unset($comment['_token_']);

        // 添加
        (new Model('comment'))->insert($comment);

        // 回到列表页
        $this->redirect(__ROOT__ . '/' . $articleId . '.html');
    }

}
