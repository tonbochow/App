<?php

namespace Admin\Model;

use Think\Model;

Class UserModel extends CommonModel {

    public $_validate = array(
        array('name', '/^[a-z]\w{3,}$/i', '帐号格式错误'),
        array('password', 'require', '密码必须'),
        array('repassword', 'require', '确认密码必须'),
        array('repassword', 'password', '确认密码不一致', self::EXISTS_VALIDATE, 'confirm'),
        array('name', '', '帐号已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    public $_auto = array(
        array('password', 'pwdHash', self::MODEL_BOTH, 'callback'),
        array('regtime', 'time', self::MODEL_INSERT, 'function'),
        array('updatetime', 'time', self::MODEL_UPDATE, 'function'),
    );

    protected function pwdHash() {
        if (isset(I('post.password'))) {
            return pwdHash(I('post.password'));
        } else {
            return false;
        }
    }

}

?>