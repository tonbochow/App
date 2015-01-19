<?php

/**
 * 后台视频功能
 * @author 周东宝 2014-12-24
 */

namespace Admin\Controller;

use Think\Controller;

class VideoController extends BaseController {

    //视频管理列表
    public function index() {
        $video_name = I('get.video_name');
        $status = I('get.status');
        $videoModel = M('Video');
        $video_cond = array();
        if (!empty($video_name)) {
            $video_cond['video_name'] = array('like', '%' . $video_name . '%');
        }
        if ($status !== '') {
            $video_cond['status'] = $status;
        }
        $video_count = $videoModel->where($video_cond)->order('create_time desc')->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($video_count, C('PER_PAGE_NUM'));
        $videos = $videoModel->limit($Page->firstRow . ',' . $Page->listRows)->where($video_cond)->order('create_time desc')->select();
        $show = $Page->show(); // 分页显示输出
        if ($status !== '') {
            $this->assign('status', intval($status));
        } else {
            $this->assign('status', $status);
        }
        $status_arr = \Admin\Model\VideoModel::getStatus();
        $this->assign('status_arr', $status_arr);
        $this->assign('video_name', $video_name);
        $this->assign('page', $show);
        $this->assign('videos', $videos);
        $this->display('index');
    }

    //视频添加
    public function add() {
        if (IS_POST) {
            $video_data = I('post.');
            $videoModel = D('Video');
            $videoModel->startTrans();
            if ($videoModel->create($video_data)) {
                $video_id = $videoModel->add();
                if ($video_id) {
                    //将说说同时保存如content表
                    $contentModel = D('Content');
                    $content_data['tapv_id'] = $video_id;
                    $content_data['title'] = $video_data['video_name'];
                    $content_data['type'] = \Admin\Model\ContentModel::$TYPE_VIDEO;
                    $content_data['content'] = $video_data['video_name'];
                    $content_data['video_url'] = $video_data['video_url'];
                    $content_data['status`'] = \Admin\Model\ContentModel::$AVAILABLE;
                    if ($contentModel->create($content_data)) {
                        $content_id = $contentModel->add();
                        if ($content_id === false) {
                            $videoModel->rollback();
                            $data['status'] = false;
                            $data['message'] = $contentModel->getError();
                            $this->ajaxReturn($data);
                        }
                    } else {
                        $videoModel->rollback();
                        $data['status'] = false;
                        $data['message'] = $contentModel->getError();
                        $this->ajaxReturn($data);
                    }
                    $data['status'] = true;
                    $data['success'] = '保存视频成功';
                    $videoModel->commit();
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $videoModel->getError();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }

    //视频编辑
    public function edit() {
        if (IS_POST) {
            $video_data = I('post.');
            $video_data['status'] = I('post.status')['id'];
            $video = M('Video')->where(array('id' => $video_data['id']))->find();
            $videoModel = D('Video');
            $videoModel->startTrans();
            if ($videoModel->create($video_data)) {
                $update_res = $videoModel->save();
                if ($update_res) {
                    //将说说同时保存如content表
                    $contentModel = D('Content');
                    $content_data['tapv_id'] = $video_data['id'];
                    $content_data['title'] = $video_data['video_name'];
                    $content_data['type'] = \Admin\Model\ContentModel::$TYPE_VIDEO;
                    $content_data['content'] = $video_data['video_name'];
                    $content_data['video_url'] = $video_data['video_url'];
                    $content_data['status'] = $video_data['status'];
                    if ($contentModel->create($content_data)) {
                        $content_save = $contentModel->where(array('tapv_id' => $video_data['id'], 'type' => \Admin\Model\ContentModel::$TYPE_VIDEO))->save();
                        if ($content_save === false) {
                            $videoModel->rollback();
                            $data['status'] = false;
                            $data['message'] = $contentModel->getError();
                            $this->ajaxReturn($data);
                        }
                    } else {
                        $videoModel->rollback();
                        $data['status'] = false;
                        $data['message'] = $contentModel->getError();
                        $this->ajaxReturn($data);
                    }
                    if ($video['video_url'] != $video_data['video_url']) {//删除原视频
                        @unlink(C('ROOT_PATH') . $video['video_url']);
                    }
                    $data['status'] = true;
                    $data['success'] = '编辑视频成功';
                    $videoModel->commit();
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
            $this->error('编辑的视频不存在');
        }
        $this->assign('video', $video);
        $this->assign('json_video', json_encode($video));
        $this->display('edit');
    }

    //删除视频
    public function delete() {
        if (IS_POST) {
            $video_id = I('post.id');
            $videoModel = M('Video');
            $videoModel->startTrans();
            $video = $videoModel->where(array('id' => $video_id))->find();
            if (empty($video)) {
                $this->error('要删除的视频不存在');
            }
            $video_url = $video['video_url'];
            $del_res = $videoModel->where(array('id' => $video_id))->delete();
            if ($del_res) {
                $content_del = M('Content')->where(array('tapv_id' => $video_id, 'type' => \Admin\Model\ContentModel::$TYPE_VIDEO))->delete();
                if (!$content_del) {
                    $videoModel->rollback();
                    $data['status'] = false;
                    $data['message'] = '视频content表删除失败';
                    $this->ajaxReturn($data);
                }
                @unlink(C('ROOT_PATH') . $video_url);
            } else {
                $data['status'] = false;
                $data['message'] = $videoModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '删除视频成功';
            $videoModel->commit();
            $this->ajaxReturn($data);
        }
    }

    //禁公开视频
    public function disable() {
        $video_id = I('post.id');
        $videoModel = M('Video');
        $videoModel->startTrans();
        $video_data['status'] = \Admin\Model\VideoModel::$UNAVAILABLE;
        $video_data['update_time'] = time();
        $disable_res = $videoModel->where(array('id' => $video_id))->save($video_data);
        if ($disable_res) {
            $contentModel = M('Content');
            $content_data['status'] = \Admin\Model\ContentModel::$UNAVAILABLE;
            $content_data['update_time'] = time();
            $content_save = $contentModel->where(array('tapv_id' => $video_id, 'type' => \Admin\Model\ContentModel::$TYPE_VIDEO))->save($content_data);
            if (!$content_save) {
                $data['status'] = false;
                $data['message'] = '禁内容公开失败';
                $videoModel->rollback();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '禁公开成功';
            $videoModel->commit();
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁公开失败';
        $this->ajaxReturn($data);
    }

    //公开视频
    public function enable() {
        $video_id = I('post.id');
        $videoModel = M('Video');
        $videoModel->startTrans();
        $video_data['status'] = \Admin\Model\VideoModel::$AVAILABLE;
        $video_data['update_time'] = time();
        $enable_res = $videoModel->where(array('id' => $video_id))->save($video_data);
        if ($enable_res) {
            $contentModel = M('Content');
            $content_data['status'] = \Admin\Model\ContentModel::$AVAILABLE;
            $content_data['update_time'] = time();
            $content_save = $contentModel->where(array('tapv_id' => $video_id, 'type' => \Admin\Model\ContentModel::$TYPE_VIDEO))->save($content_data);
            if (!$content_save) {
                $data['status'] = false;
                $data['message'] = '内容公开失败';
                $videoModel->rollback();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '启用公开成功';
            $videoModel->commit();
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用公开失败';
        $this->ajaxReturn($data);
    }

    //视频上传
    public function upload() {
        set_time_limit(0);
        $targetFolder = '/video/';
        $verifyToken = md5('unique_salt' . I('post.timestamp'));
        $config = array(
            'maxSize' => 624000000, //1M =1024000 (限制600M)
            'rootPath' => 'upload',
            'savePath' => $targetFolder, //保存路径
            'saveName' => array('uniqid', ''),
            'exts' => array('mp4', 'flv', 'rtmp'),
            'autoSub' => false,
            'replace' => true, //存在同名是否覆盖
        );
        if (!empty($_FILES) && I('post.token') == $verifyToken) {
            $upload = new \Think\Upload($config);
            $res = $upload->upload($_FILES);
            if ($res !== false) {
                $data['status'] = true;
                $data['video_url'] = '/upload' . $targetFolder . $res['Filedata']['savename'];
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

}
