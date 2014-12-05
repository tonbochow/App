<?php

namespace Admin\Controller;

use Think\Controller;

class MasterController extends BaseController {

    public function index() {
        if (IS_POST) {
            dump(I('post.'));
            exit;
        }
        $masterModel = M('Master');
        $master = $masterModel->find();

        $region = D('Common/Region');
        $province = $region->getRegion(86);
        $citys = $region->getRegion($master['province']);
        $towns = $region->getRegion($master['city']);
        $this->assign('citys', $citys);
        $this->assign('towns', $towns);
        $this->assign('province', $province);
        $this->assign('master', $master);
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
                $masterModel = M('Master');
                $master = $masterModel->find();
                if($master !==false && !empty($master['photo'])){
                    unlink($_SERVER['DOCUMENT_ROOT'].$master['photo']);
                    $data['update_time'] = time();
                    $data['photo'] = $photo_url;
                    $update_res = $masterModel->where(array('id'=>$master['id']))->save($data);
                }
                $data['status'] = true;
                $data['info'] = $photo_url;
                $this->ajaxReturn($data);
            } else {
                $data['status'] = false;
                $data['info'] = $upload->getError();
                $this->ajaxReturn($data);
            }
        }
        $this->error("上传失败");
    }

}
