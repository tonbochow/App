<?php
/**
 * 后台相片评论功能
 * @author 周东宝 2014-12-23
 */
namespace Admin\Controller;

use Think\Controller;

class PhotoCommentController extends BaseController {

    //相片评论列表
    public function index() {
        $content = I('get.content');
        $status = I('get.status');
        $photoCommentModel = M('PhotoComment');
        $comment_cond = array();
        if (!empty($content)) {
            $comment_cond['content'] = array('like', '%' . $content . '%');
        }
        if ($status !== '') {
            $comment_cond['status'] = $status;
        }
        $comment_count = $photoCommentModel->where($comment_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($comment_count, 1);
        $comments = $photoCommentModel->limit($Page->firstRow . ',' . $Page->listRows)->where($comment_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\PhotoCommentModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('content', $content);
        $this->assign('page', $show);
        $this->assign('comments', $comments);
        $this->display('index');
    }
    
    //相片评论编辑
    public function edit() {
        if (IS_POST) {
            $comment_data = I('post.');
            $comment_data['status'] = I('post.status')['id'];
            $photoCommentModel = D('PhotoComment');
            if ($photoCommentModel->create($comment_data)) {
                $update_res = $photoCommentModel->save();
                if ($update_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑相片评论成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $photoCommentModel->getError();
            $this->ajaxReturn($data);
        }
        $photoComment_id = I('get.id');
        $photoCommentModel = M('PhotoComment');
        $photoComment = $photoCommentModel->where(array('id' => $photoComment_id))->find();
        if (empty($photoComment)) {
            $this->error('编辑的相片评论不存在');
        }
        $this->assign('photoComment', $photoComment);
        $this->assign('json_comment', json_encode($photoComment));
        $this->display('edit');
    }
    
    //删除相片评论
    public function delete(){
        if(IS_POST){
            $photoComment_id = I('post.id');
            $photoCommentModel = M('PhotoComment');
            $photoComment = $photoCommentModel->where(array('id'=>$photoComment_id))->find();
            if(empty($photoComment)){
                $this->error('要删除的相片评论不存在');
            }
            $del_res = $photoCommentModel->where(array('id'=>$photoComment_id))->delete();
            if($del_res === false){
                $data['status'] = false;
                $data['message'] = $photoCommentModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除相片评论成功';
            $this->ajaxReturn($data);
        }
    }
    
    //禁公开相片评论
    public function disable() {
        $photoComment_id = I('post.id');
        $photoCommentModel = M('PhotoComment');
        $comment_data['status'] = \Admin\Model\PhotoCommentModel::$UNAVAILABLE;
        $comment_data['update_time'] = time();
        $disable_res = $photoCommentModel->where(array('id' => $photoComment_id))->save($comment_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //公开相片评论
    public function enable() {
        $photoComment_id = I('post.id');
        $photoCommentModel = M('PhotoComment');
        $comment_data['status'] = \Admin\Model\PhotoCommentModel::$AVAILABLE;
        $comment_data['update_time'] = time();
        $enable_res = $photoCommentModel->where(array('id' => $photoComment_id))->save($comment_data);
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
