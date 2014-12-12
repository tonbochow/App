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
        $Page = new \BootstrapPage($article_count, 1);
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
        $article_data['status'] = \Admin\Model\ArticleModel::$UNAVAILABLE;
        $article_data['update_time'] = time();
        $disable_res = $ArticleModel->where(array('id' => $article_id))->save($article_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁显示成功';
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
        $article_data['status'] = \Admin\Model\ArticleModel::$AVAILABLE;
        $article_data['update_time'] = time();
        $enable_res = $ArticleModel->where(array('id' => $article_id))->save($article_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '启用显示成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用显示失败';
        $this->ajaxReturn($data);
    }

    //日志添加
    public function add() {
        if (IS_POST) {
//            dump(I('post.'));exit;
            $article_data = I('post.');
            $ArticleModel = D('Article');
            if ($ArticleModel->create($article_data)) {
                $article_id = $ArticleModel->add();
                if ($article_id) {
                    $data['status'] = true;
                    $data['success'] = '保存日志成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $ArticleModel->getError();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }

    //日志编辑
    public function edit() {
        if (IS_POST) {
            $article_data = I('post.');
            $article_data['status'] = I('post.status')['id'];
            $ArticleModel = D('Article');
            if ($ArticleModel->create($article_data)) {
                $update_res = $ArticleModel->save();
                if ($update_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑日志成功';
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
        $this->assign('article', $article);
        $this->assign('json_article', json_encode($article));
        $this->display('edit');
    }

}
