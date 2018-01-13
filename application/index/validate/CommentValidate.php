<?php

namespace application\index\validate;

use aaphp\Validate;

/**
 * 评论验证
 * Class CommentValidate
 * @package application\index\validate
 */
class CommentValidate extends Validate
{
    protected $rule = [
        'article_id' => [
//            不能为空
            'require',
//            必须是邮箱格式
            'integer',
            '>' => 0,
        ],
        'nickname' => [
//            不能为空
            'require',
//            长度必须 在 1-10 之间
            'length' => [1, 10],
        ],
        'email' => [
//            不能为空
            'require',
//            必须是邮箱格式
            'email'
        ],
        'content' => [
//            不能为空
            'require',
//            长度必须 在 5-100 之间
            'length' => [5, 100],
        ],
    ];

//    用户自定义错误信息
    protected $message = [
        'article_id' => [
            'require' => 'article_id不能为空',
            'integer' => 'article_id必须是整数',
            '>' => 'article_id必须大于0',
        ],

        'nickname' => [
            'require' => '昵称不能为空',
            'length' => '昵称长度范围：1-10个字符',
        ],

        'email' => [
            'require' => '邮箱不能为空',
            'email' => '邮箱格式错误',
        ],
        'content' => [
            'require' => '内容不能为空',
            'length' => '内容长度范围：5-100个字符',
        ],
    ];
}
