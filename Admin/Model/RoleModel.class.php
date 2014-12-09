<?php

namespace Admin\Model;

use Think\Model;

Class RoleModel extends CommonModel {

    public static $ALLOW_BACKEND_LOGIN = 1;
    public static $DENY_BACKEND_LOGIN = 0;
    protected $_validate = array(
        array('name', '', '角色名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //通过role_id 获取name
    public static function getRoleName($role_id) {
        $role = M('Role')->where(array('id' => $role_id))->find();
        return $role['name'];
    }

    //角色下拉列表
    public static function getRoles($role_id = null, $has_null = true) {
        $roles = M('Role')->select();
        if ($has_null) {
            $role_arr = array('' => '请选择');
        } else {
            $role_arr = array();
        }
        foreach ($roles as $role) {
            $role_arr[$role['id']] = $role['name'];
        }
        if (!empty($role_id)) {
            return $role_arr[$role_id];
        }
        return $role_arr;
    }

    //后台登陆
    public static function getBackendLogin($backend_login = null) {
        $backend_arr = array('' => '请选择');
        $backend_arr[self::$ALLOW_BACKEND_LOGIN] = '允许';
        $backend_arr[self::$DENY_BACKEND_LOGIN] = '禁止';
        if ($backend_login !== null) {
            return $backend_arr[$backend_login];
        }
        return $backend_arr;
    }

}

?>