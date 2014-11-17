<?php

namespace Admin\Controller;

use Think\Controller;

class PublicController extends Controller {

    //后台登录页面
    public function Login() {
        $this->assign('verify_img', $verify_img);
        $this->assign('name', 'admin');
        $this->display('login');
    }

    //生成验证码
    public function createVerify() {
        $verify = new \Think\Verify();
        $verify_img = $verify->entry();
    }

    //验证验证码
    public function checkVerify() {
        
    }

}
