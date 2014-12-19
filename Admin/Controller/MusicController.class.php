<?php
/**
 * 后台音乐功能
 * @author 周东宝 2014-12-19
 */
namespace Admin\Controller;

use Think\Controller;

class MusicController extends BaseController {

    //音乐管理列表
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

     //音乐添加
    public function add() {
        if (IS_POST) {
            $music_data = I('post.');
            $musicModel = D('Music');
            if ($musicModel->create($music_data)) {
                $music_id = $musicModel->add();
                if ($music_id) {
                    $data['status'] = true;
                    $data['success'] = '保存音乐成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $musicModel->getError();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }
    
    
    //音乐编辑
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
    
    //删除音乐
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
    
    //禁公开音乐
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

    //公开音乐
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

    //音乐上传
    public function upload() {
        $targetFolder = '/music/';
        $verifyToken = md5('unique_salt' . I('post.timestamp'));
        $config = array(
            'maxSize' => 6240000, //1M =1024000 (限制6M)
            'rootPath' => 'upload',
            'savePath' => $targetFolder, //保存路径
            'saveName' => array('uniqid', ''),
            'exts' => array('mp3', 'mp4', 'aac','wav','ogg'),
            'autoSub' => false,
            'replace' => true, //存在同名是否覆盖
        );
        if (!empty($_FILES) && I('post.token') == $verifyToken) {
            $upload = new \Think\Upload($config);
            $res = $upload->upload($_FILES);
            if ($res !== false) {
                $data['status'] = true;
                $data['music_url'] = '/upload' . $targetFolder . $res['Filedata']['savename'];
                $data['exttype'] = $res['Filedata']['type'];
                $data['extfield'] = $res['Filedata']['md5'];
                echo json_encode($data);
                exit;
            }
        }
        $data['status'] = false;
        $data['info'] = '上传失败';
        echo json_encode($data);exit;
    }
}
