<?php

namespace Admin\Controller;

use Think\Controller;

class RoleAppController extends BaseController {
    
    //角色权限管理列表
    public function index(){
        
        $this->display('index');
    }
    
    //添加角色应用
    public function add(){
        $roleModel = M('Role');//检索所有角色
        $roles = $roleModel->select();
        $role_select = array();
        if(!empty($roles)){
            foreach($roles as $role){
                $role_select[]= array('role_id'=>$role['id'],'role_name'=>$role['name']);
            }
        }
        $appModel = M('App');//检索所有应用
        $apps = $appModel->select();
        $this->assign('apps',$apps);
        $this->assign('role_select',  json_encode($role_select));
        $this->display('add');
    }
    
    //编辑角色应用
    public function edit(){
        
    }
    
    //删除角色应用
    public function delete(){
        
    }
}

?>