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
        
        $this->assign('website_json',  json_encode($website));
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
                    $data['status'] = true;
                    $data['success'] = '保存网站信息成功';
                    $this->ajaxReturn($data);
                }
            } else {
                $website_data['update_time'] = time();
                $update_res = $websiteModel->where(array('name'=>$website['name']))->save($website_data);
                if ($update_res) {
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
    public  function config(){
        if(IS_POST){
            
        }
    }
}
