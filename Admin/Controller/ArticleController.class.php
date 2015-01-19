<?php

/**
 * 后台日志文章功能
 * @author 周东宝 2014-12
 */

namespace Admin\Controller;

use Think\Controller;

class ArticleController extends BaseController {

    public function index() {
        $title = I('get.title');
        $status = I('get.status');
        $articleModel = M('Article');
        $article_cond = array();
        if (!empty($title)) {
            $article_cond['title'] = array('like', '%' . $title . '%');
        }
        if ($status !== '') {
            $article_cond['status'] = $status;
        }
        $article_count = $articleModel->where($article_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($article_count, C('PER_PAGE_NUM'));
        $articles = $articleModel->limit($Page->firstRow . ',' . $Page->listRows)->where($article_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\ArticleModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('title', $title);
        $this->assign('page', $show);
        $this->assign('articles', $articles);
        $this->display('index');
    }

    //禁显示日志
    public function disable() {
        $article_id = I('post.id');
        $ArticleModel = M('Article');
        $ArticleModel->startTrans();
        $article_data['status'] = \Admin\Model\ArticleModel::$UNAVAILABLE;
        $article_data['update_time'] = time();
        $disable_res = $ArticleModel->where(array('id' => $article_id))->save($article_data);
        if ($disable_res) {
            $contentModel = M('Content');
            $content_data['status'] = \Admin\Model\ContentModel::$UNAVAILABLE;
            $content_data['update_time'] = time();
            $content_save = $contentModel->where(array('tapv_id' => $article_id,'type'=>  \Admin\Model\ContentModel::$TYPE_ARTICLE))->save($content_data);
            if (!$content_save) {
                $data['status'] = false;
                $data['message'] = '禁内容公开失败';
                $ArticleModel->rollback();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '禁显示成功';
            $ArticleModel->commit();
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁显示失败';
        $this->ajaxReturn($data);
    }

    //显示日志
    public function enable() {
        $article_id = I('post.id');
        $ArticleModel = M('Article');
        $ArticleModel->startTrans();
        $article_data['status'] = \Admin\Model\ArticleModel::$AVAILABLE;
        $article_data['update_time'] = time();
        $enable_res = $ArticleModel->where(array('id' => $article_id))->save($article_data);
        if ($enable_res) {
            $contentModel = M('Content');
            $content_data['status'] = \Admin\Model\ContentModel::$AVAILABLE;
            $content_data['update_time'] = time();
            $content_save = $contentModel->where(array('tapv_id' => $article_id,'type'=>  \Admin\Model\ContentModel::$TYPE_ARTICLE))->save($content_data);
            if (!$content_save) {
                $data['status'] = false;
                $data['message'] = '内容公开失败';
                $ArticleModel->rollback();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '启用显示成功';
            $ArticleModel->commit();
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用显示失败';
        $this->ajaxReturn($data);
    }

    //禁止评论
    public function disableComment() {
        $article_id = I('post.id');
        $ArticleModel = M('Article');
        $article_data['allow_comment'] = \Admin\Model\ArticleModel::$COMMENT_DENY;
        $article_data['update_time'] = time();
        $disable_res = $ArticleModel->where(array('id' => $article_id))->save($article_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁评论成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁评论失败';
        $this->ajaxReturn($data);
    }

    //允许评论
    public function enableComment() {
        $article_id = I('post.id');
        $ArticleModel = M('Article');
        $article_data['allow_comment'] = \Admin\Model\ArticleModel::$COMMENT_ALLOW;
        $article_data['update_time'] = time();
        $enable_res = $ArticleModel->where(array('id' => $article_id))->save($article_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '允许评论成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '允许评论失败';
        $this->ajaxReturn($data);
    }

    //日志添加
    public function add() {
        if (IS_POST) {
            $article_data = I('post.');
            $ArticleModel = D('Article');
            $ArticleModel->startTrans();
            if ($ArticleModel->create($article_data)) {
                $article_id = $ArticleModel->add();
                if ($article_id) {
                    //将说说同时保存如content表
                    $contentModel = D('Content');
                    $content_data['tapv_id'] = $article_id;
                    $content_data['title'] = $article_data['title'];
                    $content_data['type'] = \Admin\Model\ContentModel::$TYPE_ARTICLE;
                    $content_data['content'] = $article_data['content'];
                    $content_data['status`'] = \Admin\Model\ContentModel::$AVAILABLE;
                    if ($contentModel->create($content_data)) {
                        $content_id = $contentModel->add();
                        if ($content_id === false) {
                            $ArticleModel->rollback();
                            $data['status'] = false;
                            $data['message'] = $contentModel->getError();
                            $this->ajaxReturn($data);
                        }
                    } else {
                        $ArticleModel->rollback();
                        $data['status'] = false;
                        $data['message'] = $contentModel->getError();
                        $this->ajaxReturn($data);
                    }
                    $data['status'] = true;
                    $data['success'] = '保存日志成功';
                    $ArticleModel->commit();
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $ArticleModel->getError();
            $this->ajaxReturn($data);
        }
        $article_categorys = \Admin\Model\ArticleCategoryModel::getCategorys();
        $this->assign('article_categorys', json_encode($article_categorys));
        $this->display('add');
    }

    //日志编辑
    public function edit() {
        if (IS_POST) {
            $article_data = I('post.');
            $article_data['status'] = I('post.status')['id'];
            $ArticleModel = D('Article');
            $ArticleModel->startTrans();
            if ($ArticleModel->create($article_data)) {
                $update_res = $ArticleModel->save();
                if ($update_res) {
                    //将说说同时保存如content表
                    $contentModel = D('Content');
                    $content_data['tapv_id'] = $article_data['id'];
                    $content_data['title'] = $article_data['title'];
                    $content_data['type'] = \Admin\Model\ContentModel::$TYPE_ARTICLE;
                    $content_data['content'] = $article_data['content'];
                    $content_data['status'] = $article_data['status'];
                    if ($contentModel->create($content_data)) {
                        $content_save = $contentModel->where(array('tapv_id'=>$article_data['id'],'type'=>  \Admin\Model\ContentModel::$TYPE_ARTICLE))->save();
                        if ($content_save === false) {
                            $ArticleModel->rollback();
                            $data['status'] = false;
                            $data['message'] = $contentModel->getError();
                            $this->ajaxReturn($data);
                        }
                    } else {
                        $ArticleModel->rollback();
                        $data['status'] = false;
                        $data['message'] = $contentModel->getError();
                        $this->ajaxReturn($data);
                    }
                    $data['status'] = true;
                    $data['success'] = '编辑日志成功';
                    $ArticleModel->commit();
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $ArticleModel->getError();
            $this->ajaxReturn($data);
        }
        $article_id = I('get.id');
        $ArticleModel = M('Article');
        $article = $ArticleModel->where(array('id' => $article_id))->find();
        if (empty($article)) {
            $this->error('编辑的日志不存在');
        }
        $article['content'] = htmlspecialchars_decode(stripslashes($article['content']));
        $article_categorys = \Admin\Model\ArticleCategoryModel::getCategorys();
        $this->assign('article_categorys', json_encode($article_categorys));
        $this->assign('article', $article);
        $this->assign('json_article', json_encode($article));
        $this->display('edit');
    }

}
