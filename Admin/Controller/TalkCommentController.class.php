<?php
/**
 * 后台说说评论功能
 * @author 周东宝 2014-12-18
 */
namespace Admin\Controller;

use Think\Controller;

class TalkCommentController extends BaseController {

    //说说评论管理列表
    public function index() {
        $title = I('get.article_title');
        $status = I('get.status');
        $articleCommentModel = M('ArticleComment');
        $article_cond = array();
        if (!empty($title)) {
            $article_cond['article_title'] = array('like', '%' . $title . '%');
        }
        if ($status !== '') {
            $article_cond['status'] = $status;
        }
        $article_comment_count = $articleCommentModel->where($article_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($article_comment_count, 1);
        $articleComments = $articleCommentModel->limit($Page->firstRow . ',' . $Page->listRows)->where($article_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\ArticleCommentModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('title', $title);
        $this->assign('page', $show);
        $this->assign('articleComments', $articleComments);
        $this->display('index');
    }

    //禁显示说说评论
    public function disable() {
        $comment_id = I('post.id');
        $ArticleCommentModel = M('ArticleComment');
        $comment_data['status'] = \Admin\Model\ArticleCommentModel::$UNAVAILABLE;
        $comment_data['update_time'] = time();
        $disable_res = $ArticleCommentModel->where(array('id' => $comment_id))->save($comment_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁显示成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁显示失败';
        $this->ajaxReturn($data);
    }

    //显示说说评论
    public function enable() {
        $comment_id = I('post.id');
        $ArticleCommentModel = M('ArticleComment');
        $comment_data['status'] = \Admin\Model\ArticleCommentModel::$AVAILABLE;
        $comment_data['update_time'] = time();
        $enable_res = $ArticleCommentModel->where(array('id' => $comment_id))->save($comment_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '启用显示成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用显示失败';
        $this->ajaxReturn($data);
    }

}
