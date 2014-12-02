<?php

namespace Admin\Controller;

use Think\Controller;

class PublicController extends Controller {

    //后台登录页面
    public function login() {
        if (null !== session(C('USER_AUTH_KEY'))) {
            $this->error('已登录','/Admin/Index/index');
        }
        if (IS_POST) {
            $name = I('post.name');
            $password = I('post.password');
            $verifycode = I('post.verifycode');
            //检验验证码
            $check_verifycode = $this->checkVerify($verifycode);
            if (!$check_verifycode) {
                $data['status'] = false;
                $data['errorVerifycode'] = '验证码错误';
                $this->ajaxReturn($data);
            }
            //生成认证条件 使用用户名、密码和状态的方式进行认证
            $map = array();
            $map['name'] = $name;
            $map['password'] = md5($password);
            $map["status"] = array('eq', \Admin\Model\UserModel::$AVAILABLE);
            $map["backend_login"] = \Admin\Model\UserModel::$ALLOW_BACKEND_LOGIN;
            $authInfo = \Org\Util\Rbac::authenticate($map);
            if ($authInfo == false) {
                $data['status'] = false;
                $data['message'] = '帐号或密码不正确或帐号已禁用';
                $this->ajaxReturn($data);
            } else {
                session(C('USER_AUTH_KEY'), $authInfo['id']);
                session('name',$authInfo['name']);
                session('email', $authInfo['email']);
                if ($authInfo['name'] == 'admin') {//超级管理员登录后台
                    session(C('ADMIN_AUTH_KEY'), true);
                }
                //保存登录信息
                $User = M('User');
                $ip = get_client_ip();
                $time = time();
                $data = array();
                $data['id'] = $authInfo['id'];
                $data['update_time'] = $time;
//                $data['last_login_time'] = $time;
//                $data['last_login_ip'] = $ip;
                $User->save($data);

                // 缓存访问权限
                \Org\Util\Rbac::saveAccessList();
                //后台登录成功
                $data['status'] = true;
                $data['success'] = '登录成功';
                $this->ajaxReturn($data);
            }
        }
        $this->assign('verify_img', $verify_img);
        $this->assign('name', 'admin');
        $this->display('login');
    }

    //用户退出
    public function logout() {
        if (null !== (session(C('USER_AUTH_KEY')))) {
            session(null);
            $this->success('退出成功', __CONTROLLER__. '/login');
        } else {
            $this->error('已经退出');
        }
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
