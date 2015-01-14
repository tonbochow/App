<?php
/**
 * 后台留言板功能
 * @author 周东宝 2015-01-13
 */
namespace Admin\Controller;

use Think\Controller;

class MessageController extends BaseController {

    //留言管理列表
    public function index() {
        $content = I('get.content');
        $status = I('get.status');
        $messageModel = M('Message');
        $message_cond = array();
        if (!empty($title)) {
            $message_cond['content'] = array('like', '%' . $content . '%');
        }
        if ($status !== '') {
            $message_cond['status'] = $status;
        }
        $message_count = $messageModel->where($message_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($message_count, C('PER_PAGE_NUM'));
        $messages = $messageModel->limit($Page->firstRow . ',' . $Page->listRows)->where($message_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\MessageModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('content', $content);
        $this->assign('page', $show);
        $this->assign('messages', $messages);
        $this->display('index');
    }
    
    //留言回复
    public function reply() {
        if (IS_POST) {
            $reply_data = I('post.');
            $reply_data['status'] = \Admin\Model\MessageReplyModel::$AVAILABLE;
            $user_id = session(C('USER_AUTH_KEY'));
            $reply_data['user_id'] = $user_id;
            $user = M('User')->where(array('id'=>$user_id))->find();
            $reply_data['user_name'] = $user['nickname'];
            $reply_data['ip_address'] = get_client_ip();
            $messageReplyModel = D('MessageReply');
            if ($messageReplyModel->create($reply_data)) {
                $add_res = $messageReplyModel->add();
                if ($add_res) {
                    $data['status'] = true;
                    $data['success'] = '回复留言成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $messageReplyModel->getError();
            $this->ajaxReturn($data);
        }
        $message_id = I('get.id');
        $messageModel = M('Message');
        $message = $messageModel->where(array('id' => $message_id))->find();
        if (empty($message)) {
            $this->error('要回复的留言不存在');
        }
        $this->assign('message', $message);
        $this->assign('json_message', json_encode($message));
        $this->display('reply');
    }
    
    //删除留言
    public function delete(){
        if(IS_POST){
            $message_id = I('post.id');
            $messageModel = M('Message');
            $message = $messageModel->where(array('id'=>$message_id))->find();
            if(empty($message)){
                $this->error('要删除的留言不存在');
            }
            $del_res = $messageModel->where(array('id'=>$message_id))->delete();
            if(!$del_res){
                $data['status'] = false;
                $data['message'] = $messageModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除留言成功';
            $this->ajaxReturn($data);
        }
    }
    
    //禁公开留言
    public function disable() {
        $message_id = I('post.id');
        $messageModel = M('Message');
        $message_data['status'] = \Admin\Model\MessageModel::$UNAVAILABLE;
        $message_data['update_time'] = time();
        $disable_res = $messageModel->where(array('id' => $message_id))->save($message_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //公开留言
    public function enable() {
        $message_id = I('post.id');
        $messageModel = M('Message');
        $message_data['status'] = \Admin\Model\MessageModel::$AVAILABLE;
        $message_data['update_time'] = time();
        $enable_res = $messageModel->where(array('id' => $message_id))->save($message_data);
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
