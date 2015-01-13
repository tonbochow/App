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
    
    //留言编辑
    public function edit() {
        if (IS_POST) {
            $video_data = I('post.');
            $video_data['status'] = I('post.status')['id'];
            $video = M('Video')->where(array('id'=>$video_data['id']))->find();
            $videoModel = D('Video');
            if ($videoModel->create($video_data)) {
                $update_res = $videoModel->save();
                if ($update_res) {
                    if($video['video_url'] != $video_data['video_url']){//删除原视频
                        @unlink(C('ROOT_PATH').$video['video_url']);
                    }
                    $data['status'] = true;
                    $data['success'] = '编辑留言成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $videoModel->getError();
            $this->ajaxReturn($data);
        }
        $video_id = I('get.id');
        $videoModel = M('Video');
        $video = $videoModel->where(array('id' => $video_id))->find();
        if (empty($video)) {
            $this->error('编辑的留言不存在');
        }
        $this->assign('video', $video);
        $this->assign('json_video', json_encode($video));
        $this->display('edit');
    }
    
    //删除留言
    public function delete(){
        if(IS_POST){
            $video_id = I('post.id');
            $videoModel = M('Video');
            $video = $videoModel->where(array('id'=>$video_id))->find();
            if(empty($video)){
                $this->error('要删除的留言不存在');
            }
            $video_url = $video['video_url'];
            $del_res = $videoModel->where(array('id'=>$video_id))->delete();
            if($del_res){
                @unlink(C('ROOT_PATH').$video_url);
            }else{
                $data['status'] = false;
                $data['message'] = $videoModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除留言成功';
            $this->ajaxReturn($data);
        }
    }
    
    //禁公开留言
    public function disable() {
        $video_id = I('post.id');
        $videoModel = M('Video');
        $video_data['status'] = \Admin\Model\VideoModel::$UNAVAILABLE;
        $video_data['update_time'] = time();
        $disable_res = $videoModel->where(array('id' => $video_id))->save($video_data);
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
        $video_id = I('post.id');
        $videoModel = M('Video');
        $video_data['status'] = \Admin\Model\VideoModel::$AVAILABLE;
        $video_data['update_time'] = time();
        $enable_res = $videoModel->where(array('id' => $video_id))->save($video_data);
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
