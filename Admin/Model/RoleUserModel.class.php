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
    
    //通过role_id 获取user_id
    public static function getRoleUserIds($role_id){
        $role_user  = M('RoleUser')->where(array('role_id'=>$role_id))->select();
        if(!empty($role_user)){
            foreach($role_user as $roleUser){
                $userId_arr[] = $roleUser['user_id'];
            }
            return array_unique($userId_arr);
        }
        return false;
    }
}

?>