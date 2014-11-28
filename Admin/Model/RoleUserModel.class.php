<?php

namespace Admin\Model;

use Think\Model;

Class RoleUserModel extends CommonModel {

    //通过user_id获取role_id
    public static function getRoleIdsNames($user_id){
        $role_user = M('RoleUser')->where(array('user_id'=>$user_id))->select();
        if(!empty($role_user)){
            foreach($role_user as $roleUser){
                $role_arr[]['role_id'] = $roleUser['role_id'];
                $role_arr[]['role_name'] = $roleUser['role_name'];
            }
            return $role_arr;
        }
        return false;
    }
}

?>