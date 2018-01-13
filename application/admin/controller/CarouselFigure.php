<?php

namespace application\admin\controller;

use aaphp\Model;
use aaphp\Request;
use aaphp\Url;
use luojiangtao\upload\Upload;

/**
 * 轮播图控制器
 * Class CarouselFigure
 * @package application\admin\controller
 */
class CarouselFigure extends Base
{
    /**
     * 轮播图列表
     */
    public function carouselFigureList()
    {
        // 获取轮播图
        $carouselFigureList = (new Model('carousel_figure'))->order('sort ASC')->select();
        $this->assign('carousel_figure_list', $carouselFigureList);
        // 载入模版
        return $this->fetch();
    }

    /**
     * 轮播图添加，修改表单
     */
    public function carouselFigureForm()
    {
        // 不管有没有都接收并查询轮播图
        $carouselFigureId = Request::instance()->request('carousel_figure_id', 0);
        $carousel_figure = (new Model('carousel_figure'))->where(['carousel_figure_id', '=', $carouselFigureId])->selectOne();
        // 分配变量到前台模版
        $this->assign('carousel_figure', $carousel_figure);
        // 载入模版
        return $this->fetch();
    }

    /**
     * 添加轮播图
     */
    public function createCarouselFigure()
    {
        // 需要添加的数据
        $carouselFigure = array(
            'url' => Request::instance()->request('url'),
            'sort' => Request::instance()->request('sort'),
        );

        // 上传图片
        $upload = new Upload();
        $fileInfo = $upload->upload('image');
        if ($fileInfo['filename']) {
            // 如果上传成功，保存图片名称
            $carouselFigure['image'] = $fileInfo['filename'];
        }

        // 添加
        (new Model('carousel_figure'))->insert($carouselFigure);

        // 回到列表页
        $this->redirect(Url::build('admin/CarouselFigure/carousel_figure_list'));
    }

    /**
     * 修改轮播图
     */
    public function updateCarouselFigure()
    {
        $carouselFigureId = Request::instance()->request('carousel_figure_id');
        // 需要修改的数据
        $carouselFigure = array(
            'url' => Request::instance()->request('url'),
            'sort' => Request::instance()->request('sort'),
        );

        // 上传图片
        $upload = new Upload();
        $fileInfo = $upload->upload('image');
        if ($fileInfo['filename']) {
            // 如果上传成功，保存图片名称
            $carouselFigure['image'] = $fileInfo['filename'];
            // 获取以前的logo地址
            $image = (new Model("carousel_figure"))->where(['carousel_figure_id', '=', $carouselFigureId])->getField("image");
            if ($image) {
                // 补全以前的logo地址
                $image = "./upload/" . $image;
                // 删除以前的logo地址
                if (file_exists($image)) {
                    @unlink($image);
                }
            }
        }

        // 更新轮播图
        (new Model('carousel_figure'))->where(['carousel_figure_id', '=', $carouselFigureId])->update($carouselFigure);

        // 回到列表页
        $this->redirect(Url::build('admin/CarouselFigure/carousel_figure_list'));
    }

    /**
     * 删除轮播图
     */
    public function deleteCarouselFigure()
    {
        $carouselFigureId = Request::instance()->request('carousel_figure_id');
        // 获取以前的logo地址
        $image = (new Model("carousel_figure"))->where(['carousel_figure_id', '=', $carouselFigureId])->getField("image");
        if ($image) {
            // 补全以前的logo地址
            $image = "./upload/" . $image;
            // 删除以前的logo地址
            if (file_exists($image)) {
                @unlink($image);
            }
        }

        $where = ['carousel_figure_id', '=', $carouselFigureId];
        // 删除
        (new Model('carousel_figure'))->where($where)->delete();

        // 回到列表页
        $this->redirect(Url::build('admin/CarouselFigure/carousel_figure_list'));
    }
}
