<?php

namespace Admin\Controller;

use Think\Controller;

class PublicController extends Controller {

    //后台登录页面
    public function Login() {
        if (IS_POST) {
            $name = I('post.name');
            $password = I('post.password');
            $verifycode = I('post.verifycode');
            //检验验证码
            $check_verifycode = $this->checkVerify($verifycode);
            if (!$check_verifycode) {
                $data['errorVerifycode'] = '验证码错误';
                $this->ajaxReturn($data);
            }
            $data['message'] = '用户名或密码错误';
            $this->ajaxReturn($data);
        }
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
    public function checkVerify($verifycode) {
        $verify = new \Think\Verify();
        return $verify->check($verifycode);
    }

}