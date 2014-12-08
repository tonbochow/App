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
        array('name', '/^[a-z]\w{3,}$/i', '帐号格式错误'),
        array('password', 'require', '密码必须'),
        array('repassword', 'require', '确认密码必须'),
        array('repassword', 'password', '确认密码不一致', self::EXISTS_VALIDATE, 'confirm'),
        array('name', '', '帐号已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    protected $_auto = array(
        array('password', 'pwdHash', self::MODEL_BOTH, 'callback'),
        array('regtime', 'time', self::MODEL_INSERT, 'function'),
        array('updatetime', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected function pwdHash() {
        $password = I('post.password');
        if (!empty($password)) {
            return pwdHash($password);
        } else {
            return false;
        }
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