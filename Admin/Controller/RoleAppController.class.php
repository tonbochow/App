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
        if(IS_POST){
            dump($_REQUEST);exit;
        }
        $roleModel = M('Role');//检索所有角色
        $roles = $roleModel->select();
        $role_select = array();
        $apps_arr = array();
        if(!empty($roles)){
            foreach($roles as $role){
                $role_select[]= array('role_id'=>$role['id'],'role_name'=>$role['name']);
            }
        }
        $appModel = M('App');//检索所有应用
        $apps = $appModel->select();
        $this->assign('apps',$apps);
        if(!empty($apps)){
            foreach($apps as $app){
                $apps_arr[] = array('app_id'=>$app['id'],'app_content'=>$app['url'].' | '.$app['name'],'checked'=>false);
            }
        }
        $this->assign('apps_json',  json_encode($apps_arr));
        $this->assign('role_select',  json_encode($role_select));
        $this->display('add');
    }
    
    //选择角色已经拥有的应用
    public function choiceApps(){
        if(IS_POST){
//            $apps[] = array('app_id'=>4,'app_content'=>'test','checked'=>true);
//            $this->ajaxReturn(json_encode($apps));
        }
    }
    
    //编辑角色应用
    public function edit(){
        
    }
    
    //删除角色应用
    public function delete(){
        
    }
}

?>