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
        $Page = new \BootstrapPage($talk_count, C('PER_PAGE_NUM'));
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
        $talk_id = I('post.id');
        $TalkModel = M('Talk');
        $talk_data['status'] = \Admin\Model\TalkModel::$UNAVAILABLE;
        $talk_data['update_time'] = time();
        $disable_res = $TalkModel->where(array('id' => $talk_id))->save($talk_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //显示说说
    public function enable() {
        $talk_id = I('post.id');
        $TalkModel = M('Talk');
        $talk_data['status'] = \Admin\Model\TalkModel::$AVAILABLE;
        $talk_data['update_time'] = time();
        $enable_res = $TalkModel->where(array('id' => $talk_id))->save($talk_data);
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
        $talk_id = I('post.id');
        $TalkModel = M('Talk');
        $talk_data['allow_comment'] = \Admin\Model\TalkModel::$COMMENT_DENY;
        $talk_data['update_time'] = time();
        $disable_res = $TalkModel->where(array('id' => $talk_id))->save($talk_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁说说评论成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁说说评论失败';
        $this->ajaxReturn($data);
    }
    
    //允许评论
    public function enableComment() {
        $talk_id = I('post.id');
        $TalkModel = M('Talk');
        $talk_data['allow_comment'] = \Admin\Model\TalkModel::$COMMENT_ALLOW;
        $talk_data['update_time'] = time();
        $enable_res = $TalkModel->where(array('id' => $talk_id))->save($talk_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '允许说说评论成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '允许说说评论失败';
        $this->ajaxReturn($data);
    }
    
    //说说编辑
    public function edit() {
        if (IS_POST) {
            $talk_data = I('post.');
            $talk_data['status'] = I('post.status')['id'];
            $talk_data['allow_comment'] = I('post.allow_comment')['id'];
            $talkModel = D('Talk');
            if ($talkModel->create($talk_data)) {
                $update_res = $talkModel->save();
                if ($update_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑说说成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $talkModel->getError();
            $this->ajaxReturn($data);
        }
        $talk_id = I('get.id');
        $talkModel = M('Talk');
        $talk = $talkModel->where(array('id' => $talk_id))->find();
        if (empty($talk)) {
            $this->error('编辑的说说不存在');
        }
        $talk['content'] = htmlspecialchars_decode(stripslashes($talk['content']));
        $this->assign('talk', $talk);
        $this->assign('json_talk', json_encode($talk));
        $this->display('edit');
    }

}
