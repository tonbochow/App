<?php
/**
 * 后台留言回复功能
 * @author 周东宝 2015-01-13
 */
namespace Admin\Controller;

use Think\Controller;

class MessageReplyController extends BaseController {

    //留言回复管理列表
    public function index() {
        $content = I('get.content');
        $status = I('get.status');
        $messageReplyModel = M('MessageReply');
        $messageReply_cond = array();
        if (!empty($title)) {
            $messageReply_cond['content'] = array('like', '%' . $content . '%');
        }
        if ($status !== '') {
            $messageReply_cond['status'] = $status;
        }
        $messageReply_count = $messageReplyModel->where($messageReply_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($messageReply_count, C('PER_PAGE_NUM'));
        $messageReplys = $messageReplyModel->limit($Page->firstRow . ',' . $Page->listRows)->where($messageReply_count)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\MessageReplyModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('content', $content);
        $this->assign('page', $show);
        $this->assign('messageReplys', $messageReplys);
        $this->display('index');
    }
    
    //留言回复编辑
    public function edit() {
        if (IS_POST) {
            $reply_data = I('post.');
            $reply_data['status'] = $reply_data['status']['id'];
            $messageReplyModel = D('MessageReply');
            if ($messageReplyModel->create($reply_data)) {
                $save_res = $messageReplyModel->save();
                if ($save_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑回复留言成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $messageReplyModel->getError();
            $this->ajaxReturn($data);
        }
        $messageReply_id = I('get.id');
        $messageReplyModel = M('MessageReply');
        $messageReply = $messageReplyModel->where(array('id' => $messageReply_id))->find();
        if (empty($messageReply)) {
            $this->error('要编辑的回复不存在');
        }
        $messageReply['content'] = htmlspecialchars_decode(stripslashes($messageReply['content']));
        $this->assign('messageReply', $messageReply);
        $this->assign('json_messageReply', json_encode($messageReply));
        $this->display('edit');
    }
    
    //删除留言回复
    public function delete(){
        if(IS_POST){
            $messageReply_id = I('post.id');
            $messageReplyModel = M('MessageReply');
            $messageReply = $messageReplyModel->where(array('id'=>$messageReply_id))->find();
            if(empty($messageReply)){
                $this->error('要删除的留言回复不存在');
            }
            $del_res = $messageReplyModel->where(array('id'=>$messageReply_id))->delete();
            if(!$del_res){
                $data['status'] = false;
                $data['message'] = $messageReplyModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除留言回复成功';
            $this->ajaxReturn($data);
        }
    }
    
    //禁公开留言回复
    public function disable() {
        $messageReply_id = I('post.id');
        $messageReplyModel = M('MessageReply');
        $messageReply_data['status'] = \Admin\Model\MessageReplyModel::$UNAVAILABLE;
        $messageReply_data['update_time'] = time();
        $disable_res = $messageReplyModel->where(array('id' => $messageReply_id))->save($messageReply_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //公开留言回复
    public function enable() {
        $messageReply_id = I('post.id');
        $messageReplyModel = M('MessageReply');
        $messageReply_data['status'] = \Admin\Model\MessageReplyModel::$AVAILABLE;
        $messageReply_data['update_time'] = time();
        $enable_res = $messageReplyModel->where(array('id' => $messageReply_id))->save($messageReply_data);
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
