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
        $Page = new \BootstrapPage($album_count, C('PER_PAGE_NUM'));
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
            $album = M('Album')->where(array('id' => $album_id))->find();
            $thumb_url = $album['thumb_url'];
            $albumModel = D('Album');
            if ($albumModel->create($album_data)) {
                $update_res = $albumModel->save();
                if ($update_res) {
                    @unlink(C('ROOT_PATH') . $thumb_url);
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
            $album_id = I('post.id');
            $albumModel = M('Album');
            $albumModel->startTrans();
            $album = $albumModel->where(array('id' => $album_id))->find();
            if (empty($album)) {
                $this->error('要删除的相册不存在');
            }
            $album_del = $albumModel->where(array('id' => $album_id))->delete();
            if ($album_del === false) {
                $albumModel->rollback();
                $data['status'] = true;
                $data['success'] = '相册删除失败';
                $this->ajaxReturn($data);
            }
            //删除相册内所有相片
            $photos = M('Photo')->field('id,photo_url')->where(array('album_id' => $album_id))->select();
            if (!empty($photos)) {
                foreach ($photos as $photo) {
                    $photo_ids [] = $photo['id'];
                    $photo_urls [] = $photo['photo_url'];
                }
                $photo_del = M('Photo')->where(array('album_id' => $album_id))->delete();
                if ($photo_del === false) {
                    $albumModel->rollback();
                    $data['status'] = true;
                    $data['success'] = '相册内相片删除失败';
                    $this->ajaxReturn($data);
                }
            }
            //删除相片所有评论
            $photoComments = M('PhotoComment')->where(array('album_id' => $album_id))->select();
            if (!empty($photoComments)) {
                $photoComment_del = M('PhotoComment')->where(array('album_id'=>$album_id))->delete();
                if ($photoComment_del === false) {
                    $albumModel->rollback();
                    $data['status'] = true;
                    $data['success'] = '相片所有评论删除失败';
                    $this->ajaxReturn($data);
                }
            }
            //删除相册封面图片
            @unlink(C('ROOT_PATH') . $album['thumb_url']);
            //删除所有相片
            $album_dirname = C('ROOT_PATH') . "/upload/album/$album_id";
            delFileUnderDir($album_dirname);
            rmdir(C('ROOT_PATH') . "/upload/album/$album_id");
            $albumModel->commit();
            $data['status'] = true;
            $data['success'] = '删除成功';
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
                $image->open(C('ROOT_PATH') . $pathname);
                @unlink(C('ROOT_PATH') . $pathname);
                $image->thumb(250, 150, \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH') . $pathname);
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

    //相册添加相片
    public function addPhoto() {
        if (IS_POST) {
            $photo_data = I('post.');
            $photoModel = D('Photo');
            if ($photoModel->create($photo_data)) {
                $photo_id = $photoModel->add();
                if ($photo_id) {
                    $data['status'] = true;
                    $data['success'] = '添加相片成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $photoModel->getError();
            $this->ajaxReturn($data);
        }
        $album_id = I('get.id');
        $this->assign('album_id', $album_id);
        $this->display('addPhoto');
    }

    //相片上传
    public function photoUpload() {
        $album_id = I('post.album_id');
        $targetFolder = "/album/$album_id/";
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
                $image->open(C('ROOT_PATH') . $pathname);
                $image2 = new \Think\Image();
                $image2->open(C('ROOT_PATH') . $pathname);
                $image->thumb(250, 150, \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH') . $pathname);
                $high_pathname = '/upload' . $targetFolder . 'high_' . $res['Filedata']['savename'];
                $image2->thumb(1280, 720, \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH') . $high_pathname);
                $data['status'] = true;
                $data['photo_url'] = $pathname;
                $data['exttype'] = $res['Filedata']['type'];
                $data['extfield'] = $res['Filedata']['md5'];
                echo json_encode($data);
                exit;
            }
        }
        $data['status'] = false;
        $data['info'] = '上传失败';
        echo json_encode($data);
        exit;
    }

    //相册列表
    public function show() {
        $album_id = I('get.id');
        $photoModel = M('Photo');
        $photos_count = $photoModel->where(array('album_id' => $album_id))->order('list_order asc,create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($photos_count, 10);
        $photos = $photoModel->limit($Page->firstRow . ',' . $Page->listRows)->where(array('album_id' => $album_id))->order('list_order asc,create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if (!empty($photos)) {
            foreach ($photos as $key => $photo) {
                $photoUrl_arr = explode('/', $photo['photo_url']);
                $high_photo = 'high_' . end($photoUrl_arr);
                array_pop($photoUrl_arr);
                array_push($photoUrl_arr, $high_photo);
                $new_photUrl = implode('/', $photoUrl_arr);
                $highPhoto_arr[] = $new_photUrl;
                $photos[$key]['high_photo_url'] = $new_photUrl;
            }
        }
        $this->assign('page', $show);
        $this->assign('highPhotos', $highPhoto_arr);
        $this->assign('photos', $photos);
        $this->display('show');
    }

    //相片明细
    public function photoDetail() {
        $photo_id = I('get.id');
        $photoModel = M('Photo');
        $photo = $photoModel->where(array('id' => $photo_id))->find();
        if (empty($photo)) {
            $this->error('查看的相片不存在');
        }
        //检索相片评论
        $commentModel = M('PhotoComment');
        $comment_count = $commentModel->where(array('photo_id' => $photo_id))->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($comment_count, 10);
        $comments = $commentModel->limit($Page->firstRow . ',' . $Page->listRows)->where(array('photo_id' => $photo_id))->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if (!empty($photo)) {
            $photoUrl_arr = explode('/', $photo['photo_url']);
            $high_photo = 'high_' . end($photoUrl_arr);
            array_pop($photoUrl_arr);
            array_push($photoUrl_arr, $high_photo);
            $high_photoUrl = implode('/', $photoUrl_arr);
        }

        $this->assign('page', $show);
        $this->assign('comments', $comments);
        $this->assign('high_photoUrl', $high_photoUrl);
        $this->assign('photo', $photo);
        $this->display('photoDetail');
    }

    //相片编辑
    public function photoEdit() {
        if (IS_POST) {
            $photo_data = I('post.');
            $photo_data['status'] = I('post.status')['id'];
            $photo_data['allow_comment'] = I('post.allow_comment')['id'];
            $photo_id = I('post.id');
            $photo = M('Photo')->where(array('id' => $photo_id))->find();
            $photoModel = D('Photo');
            if ($photoModel->create($photo_data)) {
                $photo_update = $photoModel->save();
                if ($photo_update) {
                    if ($photo['photo_url'] != $photo_data['photo_url']) {//删除原来相片
                        $photoUrl_arr = explode('/', $photo['photo_url']);
                        $high_photo = 'high_' . end($photoUrl_arr);
                        array_pop($photoUrl_arr);
                        array_push($photoUrl_arr, $high_photo);
                        $new_photUrl = implode('/', $photoUrl_arr);
                        @unlink(C('ROOT_PATH') . $photo['photo_url']);
                        @unlink(C('ROOT_PATH') . $new_photUrl);
                    }
                    $data['status'] = true;
                    $data['success'] = '编辑相册成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $photoModel->getError();
            $this->ajaxReturn($data);
        }
        $photo_id = I('get.id');
        $photoModel = M('Photo');
        $photo = $photoModel->where(array('id' => $photo_id))->find();
        if (empty($photo)) {
            $this->error('编辑的相片不存在');
        }

        $this->assign('json_photo', json_encode($photo));
        $this->assign('photo', $photo);
        $this->display('photoEdit');
    }

    //相片删除
    public function photoDelete() {
        $photo_id = I('post.id');
        $photoModel = M('Photo');
        $photo = $photoModel->where(array('id' => $photo_id))->find();
        if (empty($photo)) {
            $data['status'] = false;
            $data['message'] = $photoModel->getError();
            $this->ajaxReturn($data);
        }
        $del_res = $photoModel->where(array('id' => $photo_id))->delete();
        if ($del_res === false) {
            $data['status'] = false;
            $data['message'] = '相片删除失败';
            $this->ajaxReturn($data);
        }
        $photoUrl_arr = explode('/', $photo['photo_url']);
        $high_photo = 'high_' . end($photoUrl_arr);
        array_pop($photoUrl_arr);
        array_push($photoUrl_arr, $high_photo);
        $new_photUrl = implode('/', $photoUrl_arr);
        @unlink(C('ROOT_PATH') . $photo['photo_url']);
        @unlink(C('ROOT_PATH') . $new_photUrl);
        $data['status'] = true;
        $data['success'] = '相片删除成功';
        $this->ajaxReturn($data);
    }

}
