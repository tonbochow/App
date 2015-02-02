<?php

/**
 * 系统设置功能
 * @author 周东宝 2014-12-26
 */

namespace Admin\Controller;

use Think\Controller;

class SystemController extends BaseController {

    //后台系统设置
    public function setting() {
        $websiteModel = M('Website');
        $website = $websiteModel->find();

        $configModel = M('Config');
        $config = $configModel->select();
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $new_config[$value['name']] = $value['value'];
            }
        }

        $this->assign('website', $website);
        $this->assign('config', $new_config);
        $this->assign('json_config', json_encode($new_config));
        $this->assign('website_json', json_encode($website));
        $this->display('setting');
    }

    //网站信息设置
    public function website() {
        if (IS_POST) {
            $website_data = I('post.');
            $websiteModel = M('Website');
            $website = $websiteModel->find();
            if (empty($website)) {
                $website_data['create_time'] = time();
                $add_res = $websiteModel->add($website_data);
                if ($add_res) {
                    if (!empty($website_data['photo'])) {
                        delFileUnderDir(C('ROOT_PATH') . '/upload/master', C('ROOT_PATH') . $website_data['photo']);
                    }
                    unset($website_data['create_time']);
                    unset($website_data['update_time']);
                    writeConfig(APP_PATH.'Common/Conf/config.system.php', $website_data, 'SYSTEM');
                    $data['status'] = true;
                    $data['success'] = '保存网站信息成功';
                    $this->ajaxReturn($data);
                }
            } else {
                $website_data['update_time'] = time();
                $update_res = $websiteModel->where(array('name' => $website['name']))->save($website_data);
                if ($update_res) {
                    if (!empty($website_data['background_img'])) {
                        delFileUnderDir(C('ROOT_PATH') . '/upload/system', C('ROOT_PATH') . $website_data['background_img']);
                    }
                    unset($website_data['create_time']);
                    unset($website_data['update_time']);
                    writeConfig(APP_PATH.'Common/Conf/config.system.php', $website_data, 'SYSTEM');
                    $data['status'] = true;
                    $data['success'] = '保存网站信息成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $websiteModel->getError();
            $this->ajaxReturn($data);
        }
    }

    //网站内容配置
    public function config() {
        if (IS_POST) {
            $post_data = I('post.');
            if (!empty($post_data)) {
                foreach ($post_data as $name => $value) {
                    $config_name = strtoupper($name);
                    $configModel = M('Config');
                    $config = $configModel->where(array('name' => $config_name))->find();
                    if (empty($config)) {
                        $add_data['name'] = $config_name;
                        $add_data['value'] = $value;
                        $add_data['create_time'] = time();
                        $add_res = $configModel->add($add_data);
                    } else {
                        $save_data['name'] = $config_name;
                        $save_data['value'] = $value;
                        $save_data['update_time'] = time();
                        $save_res = $configModel->where(array('name' => $config_name))->save($save_data);
                    }
                }
            }
        }
    }

    //清空缓存
    public function clear() {
        if (IS_POST) {
            $clear_cache = I('post.clear');
            if ($clear_cache) {
                $del_res = delFileUnderDir(RUNTIME_PATH);
                if ($del_res) {
                    $data['status'] = true;
                    $data['success'] = '清除缓存成功';
                    $this->ajaxReturn($data);
                }
            }
        }
        $this->display('clear');
    }

    //网站背景图片上传
    public function upload() {
        set_time_limit(0);
        $targetFolder = '/system/';
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
                $background_url = '/upload' . $targetFolder . $res['Filedata']['savename'];
                $image = new \Think\Image();
                $image->open(C('ROOT_PATH') . $background_url);
                @unlink(C('ROOT_PATH') . $background_url);
                $image->thumb($image->width(), $image->height(), \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH') . $background_url);
                echo $background_url;
                exit;
            }
        }
        $this->error("上传失败");
    }

}
