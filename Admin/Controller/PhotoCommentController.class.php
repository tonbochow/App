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
        $music_name = I('get.music_name');
        $status = I('get.status');
        $musicModel = M('Music');
        $music_cond = array();
        if (!empty($music_name)) {
            $music_cond['music_name'] = array('like', '%' . $music_name . '%');
        }
        if ($status !== '') {
            $music_cond['status'] = $status;
        }
        $music_count = $musicModel->where($music_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($music_count, 1);
        $musics = $musicModel->limit($Page->firstRow . ',' . $Page->listRows)->where($music_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\MusicModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('music_name', $music_name);
        $this->assign('page', $show);
        $this->assign('musics', $musics);
        $this->display('index');
    }
    
    //相片评论编辑
    public function edit() {
        if (IS_POST) {
            $music_data = I('post.');
            $music_data['status'] = I('post.status')['id'];
            $musicModel = D('Music');
            if ($musicModel->create($music_data)) {
                $update_res = $musicModel->save();
                if ($update_res) {
                    $data['status'] = true;
                    $data['success'] = '编辑音乐成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $musicModel->getError();
            $this->ajaxReturn($data);
        }
        $music_id = I('get.id');
        $musicModel = M('Music');
        $music = $musicModel->where(array('id' => $music_id))->find();
        if (empty($music)) {
            $this->error('编辑的音乐不存在');
        }
        $this->assign('music', $music);
        $this->assign('json_music', json_encode($music));
        $this->display('edit');
    }
    
    //删除相片评论
    public function delete(){
        if(IS_POST){
            $music_id = I('post.id');
            $musicModel = M('Music');
            $music = $musicModel->where(array('id'=>$music_id))->find();
            if(empty($music)){
                $this->error('要删除的音乐不存在');
            }
            $music_url = $music['music_url'];
            $del_res = $musicModel->where(array('id'=>$music_id))->delete();
            if($del_res){
                @unlink(C('ROOT_PATH').$music_url);
            }else{
                $data['status'] = false;
                $data['message'] = $musicModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除音乐成功';
            $this->ajaxReturn($data);
        }
    }
    
    //禁公开相片评论
    public function disable() {
        $music_id = I('post.id');
        $musicModel = M('Music');
        $music_data['status'] = \Admin\Model\MusicModel::$UNAVAILABLE;
        $music_data['update_time'] = time();
        $disable_res = $musicModel->where(array('id' => $music_id))->save($music_data);
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
        $music_id = I('post.id');
        $musicModel = M('Music');
        $music_data['status'] = \Admin\Model\MusicModel::$AVAILABLE;
        $music_data['update_time'] = time();
        $enable_res = $musicModel->where(array('id' => $music_id))->save($music_data);
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
