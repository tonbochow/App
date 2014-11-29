<?php

namespace Admin\Model;

use Think\Model;

Class RoleUserModel extends CommonModel {

    //通过user_id获取role_id
    public static function getUserRoleIds($user_id){
        $role_user = M('RoleUser')->where(array('user_id'=>$user_id))->select();
        if(!empty($role_user)){
            foreach($role_user as $roleUser){
                $roleId_arr[] = $roleUser['role_id'];
            }
            return $roleId_arr;
        }
        return false;
    }
}

?>