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
        dump($role_user);
        
        $show = $Page->show(); // 分页显示输出
        $this->assign('page', $show);
        $this->display('index');
    }
}

?>