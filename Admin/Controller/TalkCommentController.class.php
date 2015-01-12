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
        $content = I('get.content');
        $status = I('get.status');
        $talkCommentModel = M('TalkComment');
        $talk_cond = array();
        if (!empty($content)) {
            $talk_cond['content'] = array('like', '%' . $content . '%');
        }
        if ($status !== '') {
            $talk_cond['status'] = $status;
        }
        $talk_comment_count = $talkCommentModel->where($talk_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($talk_comment_count, C('PER_PAGE_NUM'));
        $talkComments = $talkCommentModel->limit($Page->firstRow . ',' . $Page->listRows)->where($talk_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\TalkCommentModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('content', $content);
        $this->assign('page', $show);
        $this->assign('talkComments', $talkComments);
        $this->display('index');
    }

    //禁公开说说评论
    public function disable() {
        $comment_id = I('post.id');
        $TalkCommentModel = M('TalkComment');
        $comment_data['status'] = \Admin\Model\TalkCommentModel::$UNAVAILABLE;
        $comment_data['update_time'] = time();
        $disable_res = $TalkCommentModel->where(array('id' => $comment_id))->save($comment_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //公开说说评论
    public function enable() {
        $comment_id = I('post.id');
        $TalkCommentModel = M('TalkComment');
        $comment_data['status'] = \Admin\Model\TalkCommentModel::$AVAILABLE;
        $comment_data['update_time'] = time();
        $enable_res = $TalkCommentModel->where(array('id' => $comment_id))->save($comment_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '启用公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用公开失败';
        $this->ajaxReturn($data);
    }

}
