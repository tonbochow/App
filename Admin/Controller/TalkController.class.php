<?php
/**
 * 后台说说功能
 * @author 周东宝 2014-12-18
 */
namespace Admin\Controller;

use Think\Controller;

class TalkController extends BaseController {

    //说说管理列表
    public function index() {
        $content = I('get.content');
        $status = I('get.status');
        $talkModel = M('Talk');
        $talk_cond = array();
        if (!empty($content)) {
            $talk_cond['content'] = array('like', '%' . $content . '%');
        }
        if ($status !== '') {
            $talk_cond['status'] = $status;
        }
        $talk_count = $talkModel->where($talk_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($talk_count, 1);
        $talks = $talkModel->limit($Page->firstRow . ',' . $Page->listRows)->where($talk_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\TalkModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('content', $content);
        $this->assign('page', $show);
        $this->assign('talks', $talks);
        $this->display('index');
    }
    
    //说说添加
    public function add() {
        if (IS_POST) {
            $talk_data = I('post.');
            $TalkModel = D('Talk');
            if ($TalkModel->create($talk_data)) {
                $talk_id = $TalkModel->add();
                if ($talk_id) {
                    $data['status'] = true;
                    $data['success'] = '保存说说成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $TalkModel->getError();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }

    //禁显示说说
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

    //显示说说
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
    

}
