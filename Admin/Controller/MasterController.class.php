<?php

namespace Admin\Controller;

use Think\Controller;

class MasterController extends BaseController {

    public function index() {
        if (IS_POST) {
            $master_data = I('post.');
            $master_data['show_flag'] = I('post.show_flag')['id'];
            $master_data['sex'] = I('post.sex')['id'];
            $master_data['birthday'] = strtotime($master_data['birthday']);
            $masterModel = M('Master');
            $master = $masterModel->find();
            if (empty($master)) {
                $mod = D('Master');
                if ($mod->create($master_data, \Think\Model::MODEL_INSERT)) {
                    $add_res = $mod->add();
                    if ($add_res) {
                        if (!empty($master_data['photo'])) {
                            delFileUnderDir(C('ROOT_PATH') . '/upload/master', C('ROOT_PATH') . $master_data['photo']);
                        }
                        unset($master_data['create_time']);
                        unset($master_data['update_time']);
                        writeConfig(APP_PATH . 'Common/Conf/config.master.php', $master_data, 'MASTER');
                        $data['status'] = true;
                        $data['success'] = "保存站长信息成功";
                        $this->ajaxReturn($data);
                    }
                }
            } else {
                $mod = D('Master');
                if ($mod->create($master_data, \Think\Model::MODEL_UPDATE)) {
                    $update_res = $mod->save();
                    if ($update_res) {
                        if (!empty($master_data['photo'])) {
                            delFileUnderDir(C('ROOT_PATH') . '/upload/master', C('ROOT_PATH') . $master_data['photo']);
                        }
                        unset($master_data['create_time']);
                        unset($master_data['update_time']);
                        writeConfig(APP_PATH . 'Common/Conf/config.master.php', $master_data, 'MASTER');
                        $data['status'] = true;
                        $data['success'] = "保存站长信息成功";
                        $this->ajaxReturn($data);
                    }
                }
            }
            $data['status'] = false;
            $data['message'] = $mod->getError();
            $this->ajaxReturn($data);
        }
        $masterModel = M('Master');
        $master = $masterModel->find();
        if (empty($master)) {
            $master['sex'] = '';
            $master['show_flag'] = '';
        }
        if (!empty($master)) {
            $master['birthday'] = date('Y-m-d', $master['birthday']);
        }
        $region = D('Common/Region');
        $province = $region->getRegion(86);
        $citys = $region->getRegion($master['province']);
        $towns = $region->getRegion($master['city']);
        $this->assign('citys', $citys);
        $this->assign('towns', $towns);
        $this->assign('province', $province);
        $this->assign('master', $master);
        $this->assign('json_master', json_encode($master));
        $this->display('index');
    }

    public function getRegion() {
        $model = D('Common/Region');
        $parent = intval($_REQUEST['pid']);
        $list = $model->getRegion($parent);
        echo json_encode($list);
    }

    //头像上传
    public function upload() {
        $targetFolder = '/master/';
        $verifyToken = md5('unique_salt' . I('post.timestamp'));
        $config = array(
            'maxSize' => 5120000, //1M =1024000 (限制5M)
            'rootPath' => 'upload',
            'savePath' => $targetFolder, //保存路径
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'jpeg', 'png', 'bmp', 'gif'),
            'autoSub' => false,
            'replace' => true, //存在同名是否覆盖
        );
        if (!empty($_FILES) && I('post.token') == $verifyToken) {
            $upload = new \Think\Upload($config);
            $res = $upload->upload($_FILES);
            if ($res !== false) {
                $photo_url = '/upload' . $targetFolder . $res['Filedata']['savename'];
                $image = new \Think\Image();
                $image->open(C('ROOT_PATH') . $photo_url);
                @unlink(C('ROOT_PATH') . $photo_url);
                $image->thumb(120, 120, \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH') . $photo_url);
                echo $photo_url;
                exit;
            }
        }
        $this->error("上传失败");
    }

}
