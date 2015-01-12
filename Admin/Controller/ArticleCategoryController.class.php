<?php

namespace Admin\Controller;

use Think\Controller;

class ArticleCategoryController extends BaseController {

    public function index() {
        $cate_name = I('get.cate_name');
        $status = I('get.status');
        $articleCategoryModel = M('ArticleCategory');
        $articleCategory_cond = array();
        if (!empty($cate_name)) {
            $articleCategory_cond['cate_name'] = array('like', '%' . $cate_name . '%');
        }
        if ($status !== '') {
            $articleCategory_cond['status'] = $status;
        }
        $articleCategory_count = $articleCategoryModel->where($articleCategory_cond)->order('sort asc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($articleCategory_count, C('PER_PAGE_NUM'));
        $articleCategory = $articleCategoryModel->limit($Page->firstRow . ',' . $Page->listRows)->where($articleCategory_cond)->order('sort asc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\ArticleCategoryModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('cate_name', $cate_name);
        $this->assign('page', $show);
        $this->assign('articleCategory', $articleCategory);
        $this->display('index');
    }

    //禁用用户
    public function disable() {
        $cate_id = I('post.id');
        $CateModel = M('ArticleCategory');
        $cate_data['status'] = \Admin\Model\ArticleCategoryModel::$UNAVAILABLE;
        $cate_data['update_time'] = time();
        $disable_res = $CateModel->where(array('id' => $cate_id))->save($cate_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁用成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁用失败';
        $this->ajaxReturn($data);
    }

    //启用用户
    public function enable() {
        $cate_id = I('post.id');
        $CateModel = M('ArticleCategory');
        $cate_data['status'] = \Admin\Model\ArticleCategoryModel::$AVAILABLE;
        $cate_data['update_time'] = time();
        $enable_res = $CateModel->where(array('id' => $cate_id))->save($cate_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '启用成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用失败';
        $this->ajaxReturn($data);
    }

    //日志分类添加
    public function add() {
        if (IS_POST) {
            $cate_data = I('post.');
            $ArticleCategoryModel = D('ArticleCategory');
            if ($ArticleCategoryModel->create($cate_data)) {
                $cate_id = $ArticleCategoryModel->add();
                if ($cate_id) {
                    $data['status'] = true;
                    $data['success'] = '日志分类添加成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $ArticleCategoryModel->getError();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }

    //用户编辑
    public function edit() {
        if (IS_POST) {
            $cate_data = I('post.');
            $cate_data['status'] = I('post.status')['id'];
            $ArticleCategoryModel = D('ArticleCategory');
            if ($ArticleCategoryModel->create($cate_data)) {
                $update_res = $ArticleCategoryModel->save();
                if ($update_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑日志分类成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $ArticleCategoryModel->getError();
            $this->ajaxReturn($data);
        }
        $cate_id = I('get.id');
        $ArticleCategoryModel = M('ArticleCategory');
        $article_category = $ArticleCategoryModel->where(array('id' => $cate_id))->find();
        if (empty($article_category)) {
            $this->error('编辑的日志分类不存在');
        }
        $this->assign('article_category', $article_category);
        $this->assign('json_article_category', json_encode($article_category));
        $this->display('edit');
    }

}
