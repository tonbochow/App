<?php
namespace Admin\Controller;
use Think\Controller;
class RoleController extends BaseController{
    
    //角色列表
    public function index(){
        $roleModel = M('Role');
        $roles = $roleModel->select();

        $this->assign('roles',$roles);
        $this->display('index');
    }
}
?>