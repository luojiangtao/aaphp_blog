<?php

namespace application\index\controller;

use aaphp\Controller;
use aaphp\Model;
use aaphp\Request;
use aaphp\Url;
use application\index\validate\CommentValidate;
use Gregwar\Captcha\CaptchaBuilder;

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
        $captcha = Request::instance()->request('captcha');
        $articleId = Request::instance()->request('article_id');

        if ($captcha != $_SESSION['captcha']) {
            $this->redirect(__ROOT__ . '/' . $articleId . '.html?errorMessage=验证码不正确');
            return;
        }
        $comment = array(
            'article_id' => $articleId,
            'nickname' => Request::instance()->request('nickname'),
            'email' => Request::instance()->request('email'),
            'content' => Request::instance()->request('content'),
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

        // 添加
        (new Model('comment'))->insert($comment);

        // 回到列表页
        $this->redirect(__ROOT__ . '/' . $articleId . '.html');
    }

    /**
     * 验证码
     */
    public function captcha()
    {
        $builder = new CaptchaBuilder;
        $builder->build(130, 34);
        header('Content-type: image/jpeg');
        $builder->output();
        $_SESSION['captcha'] = $builder->getPhrase();
    }
}
