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
    
    //角色添加
    public function add(){
        if(IS_POST){//添加角色
            $roleModel = D('Role');
            $res = $roleModel->create();
            if($res){
                $roleModel->add();
                $data['status'] = true;
                $data['success'] = '创建成功';
            }else{
                $data['message'] = $roleModel->getError();
                $data['status'] = false;
            }
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }
    
    //角色编辑
    public function edit(){
        $role_id = I('get.id');
        $role = M('Role')->where(array('id'=>$role_id))->find();
        if(empty($role)){
            $this->error('未检索到该角色');
        }
        $this->assign('role',$role);
        $this->display('edit');
    }
    
    //角色删除
    public function delete(){
        
    }
}
?>