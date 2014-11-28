<?php

namespace Admin\Controller;

use Think\Controller;

class RoleUserController extends BaseController {
    
    //用户角色管理列表
    public function index(){
        $userModel = M('User');
        $users = $userModel->select();
        $user_count = $userModel->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($user_count, 2);
        $users = $userModel->limit($Page->firstRow . ',' . $Page->listRows)->select();
        if(!empty($users)){
            foreach ($users as $user){
                $user_ids[] = $user['id'];
            }
        }
        $roleUserModel = M('RoleUser');
        $role_user = $roleUserModel->where(array('user_id'=>array('between',$user_ids)))->select();
        $user_arr = array();
        if(!empty($role_user)){
            foreach($role_user as $roleUser){
                if(isset($user_arr[$roleUser['user_id']])){
                    $user_arr[$roleUser['user_id']]['role_id'] .= ','.$roleUser['role_id'];
                    $user_arr[$roleUser['user_id']]['role_name'] .= ','.$roleUser['role_name'];
                }else{
                    $user_arr[$roleUser['user_id']]['role_id'] = $roleUser['role_id'];
                    $user_arr[$roleUser['user_id']]['role_name'] =  $roleUser['role_name'];
                }
            }
        }
        if(!empty($users)){
            foreach($users as $key=>$user){
                $users[$key]['role_ids'] = $user_arr[$user['id']]['role_id'];
                $users[$key]['role_names'] = $user_arr[$user['id']]['role_name'];
            }
        }
        
        $show = $Page->show(); // 分页显示输出
        $this->assign('users',$users);
        $this->assign('page', $show);
        $this->display('index');
    }
}

?>