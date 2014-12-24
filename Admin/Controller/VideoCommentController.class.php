<?php
/**
 * 后台视频评论功能
 * @author 周东宝 2014-12-24
 */
namespace Admin\Controller;

use Think\Controller;

class VideoCommentController extends BaseController {

    //视频评论列表
    public function index() {
        $content = I('get.content');
        $status = I('get.status');
        $videoCommentModel = M('VideoComment');
        $comment_cond = array();
        if (!empty($content)) {
            $comment_cond['content'] = array('like', '%' . $content . '%');
        }
        if ($status !== '') {
            $comment_cond['status'] = $status;
        }
        $comment_count = $videoCommentModel->where($comment_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($comment_count, 1);
        $comments = $videoCommentModel->limit($Page->firstRow . ',' . $Page->listRows)->where($comment_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\VideoCommentModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('content', $content);
        $this->assign('page', $show);
        $this->assign('comments', $comments);
        $this->display('index');
    }
    
    //视频评论编辑
    public function edit() {
        if (IS_POST) {
            $comment_data = I('post.');
            $comment_data['status'] = I('post.status')['id'];
            $videoCommentModel = D('VideoComment');
            if ($videoCommentModel->create($comment_data)) {
                $update_res = $videoCommentModel->save();
                if ($update_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑视频评论成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $videoCommentModel->getError();
            $this->ajaxReturn($data);
        }
        $videoComment_id = I('get.id');
        $videoCommentModel = M('VideoComment');
        $videoComment = $videoCommentModel->where(array('id' => $videoComment_id))->find();
        if (empty($videoComment)) {
            $this->error('编辑的视频评论不存在');
        }
        $this->assign('videoComment', $videoComment);
        $this->assign('json_comment', json_encode($videoComment));
        $this->display('edit');
    }
    
    //删除视频评论
    public function delete(){
        if(IS_POST){
            $videoComment_id = I('post.id');
            $videoCommentModel = M('VideoComment');
            $videoComment = $videoCommentModel->where(array('id'=>$videoComment_id))->find();
            if(empty($videoComment)){
                $this->error('要删除的视频评论不存在');
            }
            $del_res = $videoCommentModel->where(array('id'=>$videoComment_id))->delete();
            if($del_res === false){
                $data['status'] = false;
                $data['message'] = $videoCommentModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除视频评论成功';
            $this->ajaxReturn($data);
        }
    }
    
    //禁公开视频评论
    public function disable() {
        $videoComment_id = I('post.id');
        $videoCommentModel = M('VideoComment');
        $comment_data['status'] = \Admin\Model\VideoCommentModel::$UNAVAILABLE;
        $comment_data['update_time'] = time();
        $disable_res = $videoCommentModel->where(array('id' => $videoComment_id))->save($comment_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //公开视频评论
    public function enable() {
        $videoComment_id = I('post.id');
        $videoCommentModel = M('VideoComment');
        $comment_data['status'] = \Admin\Model\VideoCommentModel::$AVAILABLE;
        $comment_data['update_time'] = time();
        $enable_res = $videoCommentModel->where(array('id' => $videoComment_id))->save($comment_data);
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
