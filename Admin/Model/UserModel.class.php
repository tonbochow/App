<?php

namespace Admin\Model;

use Think\Model;

Class UserModel extends CommonModel {
    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    public static $ALLOW_BACKEND_LOGIN = 1;
    public static $DENY_BACKEND_LOGIN = 0;
    
    public static $IS_SUPERUSER = 1;
    public static $NOT_SUPERUSER = 0;

    protected $_validate = array(
        array('name', '', '用户名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('name', 'checkName', '用户名格式不正确', self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),
        array('password', 'require', '密码必须'),
        array('repassword', 'require', '确认密码必须'),
        array('repassword', 'password', '确认密码不一致', self::EXISTS_VALIDATE, 'confirm'),
        array('email', '', '邮箱已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('email', 'checkEmail', '邮箱格式不正确', self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),
        array('mobile', 'checkMobile', '手机号码不正确', self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),
        array('mobile', '', '手机已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('qq', 'checkQq', 'QQ号不正确', self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('password', 'pwdHash', self::MODEL_BOTH, 'callback'),
        array('regtime', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected function pwdHash() {
        $password = I('post.password');
        if (!empty($password)) {
            return pwdHash($password);
        } else {
            return false;
        }
    }
    
    //验证用户名是否符合规则
    public static function checkName($name) {
        $match = '/^[a-z][a-zA-Z\d_]{3,20}$/';
        $v = trim($name);
        if (empty($v)) {
            return false;
        }
        $matches = preg_match($match, $v);
        if(empty($matches)){
            return false;
        }
        return true;
    }
    
    //验证邮箱是否符合规则
    public static function checkEmail($email) {
        $match = '/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i';
        $v = trim($email);
        if (empty($v)) {
            return false;
        }
        $matches = preg_match($match, $v);
        if(empty($matches)){
            return false;
        }
        return true;
    }
    
    //验证手机号是否符合规则
    public static function checkMobile($mobile) {
        $match = '/^[(86)|0]?(13\d{9})|(15\d{9})|(17\d{9})|(18\d{9})$/';
        $v = trim($mobile);
        if (empty($v)) {
            return false;
        }
        $matches = preg_match($match, $v);
        if(empty($matches)){
            return false;
        }
        return true;
    }
    
    //验证手机号是否符合规则
    public static function checkQq($qq) {
        $match = '/^[1-9]*[1-9][0-9]*$/';
        $v = trim($qq);
        if (empty($v)) {
            return false;
        }
        $matches = preg_match($match, $v);
        if(empty($matches)){
            return false;
        }
        return true;
    }
    
    //获取用户状态
    public static function getStatus($status = null){
        $status_arr = array(''=>'请选择');
        $status_arr[self::$AVAILABLE] ='正常';
        $status_arr[self::$UNAVAILABLE] = '禁用';
        if($status !== null){
            return $status_arr[$status];
        }
        return $status_arr;
    }

    //是否超级用户
    public static function checkSuperUser($is_superuser = null){
        $superUser_arr = array(''=>'请选择');
        $superUser_arr[self::$IS_SUPERUSER] ='是';
        $superUser_arr[self::$NOT_SUPERUSER] = '否';
        if($is_superuser !== null){
            return $superUser_arr[$is_superuser];
        }
        return $superUser_arr;
    }
}

?>