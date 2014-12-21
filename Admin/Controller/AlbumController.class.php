<?php

/**
 * 后台相册功能
 * @author 周东宝 2014-12-20
 */

namespace Admin\Controller;

use Think\Controller;

class AlbumController extends BaseController {

    //相册管理列表
    public function index() {
        $title = I('get.title');
        $status = I('get.status');
        $AlbumModel = M('Album');
        $album_cond = array();
        if (!empty($title)) {
            $album_cond['title'] = array('like', '%' . $title . '%');
        }
        if ($status !== '') {
            $album_cond['status'] = $status;
        }
        $album_count = $AlbumModel->where($album_cond)->order('list_order asc,create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($album_count, 10);
        $albums = $AlbumModel->limit($Page->firstRow . ',' . $Page->listRows)->where($album_cond)->order('list_order asc,create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\AlbumModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('title', $title);
        $this->assign('page', $show);
        $this->assign('albums', $albums);
        $this->display('index');
    }

    //相册添加
    public function add() {
        if (IS_POST) {
            $album_data = I('post.');
            $albumModel = D('Album');
            if ($albumModel->create($album_data)) {
                $album_id = $albumModel->add();
                if ($album_id) {
                    $data['status'] = true;
                    $data['success'] = '创建相册成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $albumModel->getError();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }

    //相册编辑
    public function edit() {
        if (IS_POST) {
            $album_data = I('post.');
            $album_data['status'] = I('post.status')['id'];
            $album_id = $album_data['id'];
            $album = M('Album')->where(array('id'=>$album_id))->find();
            $thumb_url = $album['thumb_url'];
            $albumModel = D('Album');
            if ($albumModel->create($album_data)) {
                $update_res = $albumModel->save();
                if ($update_res) {
                    @unlink(C('ROOT_PATH').$thumb_url);
                    $data['status'] = true;
                    $data['success'] = '编辑相册成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $albumModel->getError();
            $this->ajaxReturn($data);
        }
        $album_id = I('get.id');
        $albumModel = M('Album');
        $album = $albumModel->where(array('id' => $album_id))->find();
        if (empty($album)) {
            $this->error('编辑的相册不存在');
        }
        $this->assign('album', $album);
        $this->assign('json_album', json_encode($album));
        $this->display('edit');
    }

    //删除相册
    public function delete() {
        if (IS_POST) {
            $music_id = I('post.id');
            $musicModel = M('Music');
            $music = $musicModel->where(array('id' => $music_id))->find();
            if (empty($music)) {
                $this->error('要删除的音乐不存在');
            }
            $music_url = $music['music_url'];
            $del_res = $musicModel->where(array('id' => $music_id))->delete();
            if ($del_res) {
                @unlink(C('ROOT_PATH') . $music_url);
            } else {
                $data['status'] = false;
                $data['message'] = $musicModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除音乐成功';
            $this->ajaxReturn($data);
        }
    }

    //禁公开相册
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

    //公开相册
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

    //相册封面上传
    public function upload() {
        $targetFolder = '/album/cover/';
        $verifyToken = md5('unique_salt' . I('post.timestamp'));
        $config = array(
            'maxSize' => 6240000, //1M =1024000 (限制6M)
            'rootPath' => 'upload',
            'savePath' => $targetFolder, //保存路径
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'jpeg', 'png', 'gif', 'bmp'),
            'autoSub' => false,
            'replace' => true, //存在同名是否覆盖
        );
        if (!empty($_FILES) && I('post.token') == $verifyToken) {
            $upload = new \Think\Upload($config);
            $res = $upload->upload($_FILES);
            if ($res !== false) {
                $pathname = '/upload' . $targetFolder . $res['Filedata']['savename'];
                $image = new \Think\Image();
                $image->open(C('ROOT_PATH').$pathname);
                @unlink(C('ROOT_PATH').$pathname);
                $image->thumb(250, 150, \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH').$pathname);
                $data['status'] = true;
                $data['thumb_url'] = $pathname;
                echo json_encode($data);
                exit;
            }
        }
        $data['status'] = false;
        $data['info'] = '上传失败';
        echo json_encode($data);
        exit;
    }

}
