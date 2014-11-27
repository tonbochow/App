<?php

namespace Admin\Controller;

use Think\Controller;

class RoleAppController extends BaseController {

    //角色权限管理列表
    public function index() {

        $this->display('index');
    }

    //添加角色应用
    public function add() {
        if (IS_POST) {
//            dump($_REQUEST);exit;
            $role_id = I('post.role_id');
            $app_ids = I('post.app_ids');
            $roleAppModel = M('RoleApp');
            $cond['role_id'] = $role_id;
            $exist = $roleAppModel->where($cond)->count();
            $data['role_name'] = \Admin\Model\RoleModel::getRoleName($role_id);
            $data['app_ids'] = json_encode($app_ids);
            $data['status'] = 1;
            $data['create_time'] = time();
            if($exist){
                $role_app = $roleAppModel->where($cond)->save($data);
            }else{
                $data['role_id'] = $role_id;
                $role_app = $roleAppModel->add($data);
            }
            
            if ($role_app === false) {
                $data['status'] = false;
                $data['message'] = $roleAppModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '角色分配应用成功';
            $this->ajaxReturn($data);
        }
        $roleModel = M('Role'); //检索所有角色
        $roles = $roleModel->select();
        $role_select = array();
        $apps_arr = array();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $role_select[] = array('role_id' => $role['id'], 'role_name' => $role['name']);
            }
        }
        $appModel = M('App'); //检索所有应用
        $apps = $appModel->select();
        $this->assign('apps', $apps);
        if (!empty($apps)) {
            foreach ($apps as $app) {
                $apps_arr[] = array('app_id' => $app['id'], 'app_content' => $app['url'] . ' | ' . $app['name'], 'checked' => false);
            }
        }
        $this->assign('apps_json', json_encode($apps_arr));
        $this->assign('role_select', json_encode($role_select));
        $this->display('add');
    }

    //选择角色已经拥有的应用
    public function choiceApps() {
        if (IS_POST) {
//            dump(I('post.'));exit;
            $role_id = I('post.role_id');
            $roleAppModel = M('RoleApp');
            $role_app = $roleAppModel->where(array('role_id' => $role_id))->find();
            $app_ids = json_decode($role_app['app_ids'], true);
            $appModel = M('App');
//                $cond['id'] = array('not between',$app_ids);
            $apps = $appModel->where($cond)->select();
            if (!empty($apps)) {
                foreach ($apps as $app) {
                    if (in_array($app['id'], $app_ids)) {
                        $apps_arr[] = array('app_id' => $app['id'], 'app_content' => $app['url'] . ' | ' . $app['name'], 'checked' => true);
                    } else {
                        $apps_arr[] = array('app_id' => $app['id'], 'app_content' => $app['url'] . ' | ' . $app['name'], 'checked' => false);
                    }
                }
                $this->ajaxReturn(json_encode($apps_arr));
            }
        }
    }

    //编辑角色应用
    public function edit() {
        
    }

    //删除角色应用
    public function delete() {
        
    }

}

?>