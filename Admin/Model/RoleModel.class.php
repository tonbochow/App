<?php

namespace Admin\Model;

use Think\Model;

Class RoleModel extends CommonModel {

    public $_validate = array(
        array('name', '', '角色名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    public $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //通过role_id 获取name
    public static function getRoleName($role_id){
        $role = M('Role')->where(array('id'=>$role_id))->find();
        return $role['name'];
    }
    
    //角色下拉列表
    public static  function getRoles($role_id =null){
        $roles = M('Role')->select();
        $role_arr = array(''=>'请选择');
        foreach ($roles as $role){
            $role_arr[$role['id']] = $role['name'];
        }
        if(!empty($role_id)){
            return $role_arr[$role_id];
        }
        return $role_arr;
    }
}

?>