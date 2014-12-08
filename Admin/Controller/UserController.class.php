<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends BaseController {

    public function index() {
        $role_id = I('get.role_id');
        $is_superuser = I('get.is_superuser');
        $roles_arr = \Admin\Model\RoleModel::getRoles();
        $superuser_arr = \Admin\Model\UserModel::checkSuperUser();
        $userModel = M('User');
        $user_cond = array();
        if (!empty($role_id)) {
            $userIds = \Admin\Model\RoleUserModel::getRoleUserIds($role_id);
            if ($userIds !== false) {
                $user_cond['id'] = array("in" , $userIds);
            }
        }
        if($is_superuser !==''){
            $user_cond['is_superuser'] = $is_superuser;
        }
        $user_count = $userModel->where($user_cond)->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($user_count, 1);
        $users = $userModel->limit($Page->firstRow . ',' . $Page->listRows)->where($user_cond)->select();
        $show = $Page->show(); // 分页显示输出
        $this->assign('superuser_arr', $superuser_arr);
        $this->assign('roles_arr', $roles_arr);
        $this->assign('is_superuser', $is_superuser);
        $this->assign('role_id', $role_id);
        $this->assign('page', $show);
        $this->assign('users', $users);
        $this->display('index');
    }

    //用户添加
    public function add() {
        $roles = \Admin\Model\RoleModel::getRoles();
        if(!empty($roles)){
            foreach($roles as $role_id=>$role_name){
                $roles_arr[]['id'] = $role_id;
                $roles_arr[]['role_name'] = $role_name;
            }
        }
        $this->assign('roles_arr',  json_encode($roles_arr));
        $backendLogin_arr[] = array('id' => \Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN, 'backend' => \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN));
        $backendLogin_arr[] = array('id' => \Admin\Model\RoleModel::$DENY_BACKEND_LOGIN, 'backend' => \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$DENY_BACKEND_LOGIN));
        $this->assign('backendLogin_arr', json_encode($backendLogin_arr));
        $this->display('add');
    }

    
    //用户编辑
    public function edit(){
        $this->display('edit');
    }
}
