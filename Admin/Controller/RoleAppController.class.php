<?php

namespace Admin\Controller;

use Think\Controller;

class RoleAppController extends BaseController {

    //角色权限管理列表
    public function index() {
        $roleAppModel = M('RoleApp');
        $role_app = $roleAppModel->select();
        $role_app_count = $roleAppModel->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($role_app_count, 1);
        $role_app = $roleAppModel->limit($Page->firstRow . ',' . $Page->listRows)->select();
        if (!empty($role_app)) {
            foreach ($role_app as $key => $val) {
                $app_ids = implode(',', json_decode($val['app_ids'], true));
                $role_app[$key]['app_ids'] = $app_ids;
            }
        }

        $show = $Page->show(); // 分页显示输出
        $this->assign('page', $show);
        $this->assign('role_app', $role_app);
        $this->display('index');
    }

    //给角色添加权限
    private function _addAccess($roleAppModel, $app_ids, $role_id) {
        foreach ($app_ids as $app_id) {
            $appModel = M('App');
            $app = $appModel->where(array('id' => $app_id))->find();
            $module_node_id = $app['module_node_id'];
            $controller_node_id = $app['controller_node_id'];
            $action_node_id = $app['action_node_id'];
            $accessModel = M('Access');
            $access_module['role_id'] = $role_id;
            $access_module['node_id'] = $module_node_id;
            $access_module['level'] = 1;
            $access_controller['role_id'] = $role_id;
            $access_controller['node_id'] = $controller_node_id;
            $access_controller['level'] = 2;
            $access_action['role_id'] = $role_id;
            $access_action['node_id'] = $action_node_id;
            $access_action['level'] = 3;
            $exist_module_access = $accessModel->where($access_module)->find();
            if (empty($exist_module_access)) {
                $module_access_id = $accessModel->add($access_module);
                if ($module_access_id === false) {
                    $roleAppModel->rollback();
                    $data['status'] = false;
                    $data['message'] = '分配模块权限时失败';
                    $this->ajaxReturn($data);
                }
            }
            $exist_controller_access = $accessModel->where($access_controller)->find();
            if (empty($exist_controller_access)) {
                $controller_access_id = $accessModel->add($access_controller);
                if ($controller_access_id === false) {
                    $roleAppModel->rollback();
                    $data['status'] = false;
                    $data['message'] = '分配控制器权限时失败';
                    $this->ajaxReturn($data);
                }
            }
            $exist_action_access = $accessModel->where($access_action)->find();
            if (empty($exist_action_access)) {
                $action_access_id = $accessModel->add($access_action);
                if ($action_access_id === false) {
                    $roleAppModel->rollback();
                    $data['status'] = false;
                    $data['message'] = '分配操作权限时失败';
                    $this->ajaxReturn($data);
                }
            }
        }
    }

    //添加角色应用
    public function add() {
        if (IS_POST) {
            $role_id = I('post.role_id');
            $app_ids = I('post.app_ids');
            if (!empty($app_ids)) {
                $roleAppModel = M('RoleApp');
                $roleAppModel->startTrans();
                // 向role_app 表中插入角色应用
                $cond['role_id'] = $role_id;
                $exist = $roleAppModel->where($cond)->count();
                $data['role_name'] = \Admin\Model\RoleModel::getRoleName($role_id);
                $data['app_ids'] = json_encode($app_ids);
                $data['status'] = 1;
                $data['create_time'] = time();
                if ($exist) {//更新角色应用  
                    // 更新access表中权限
                    $roleApp = $roleAppModel->where(array('role_id' => $role_id))->find();
                    $exist_app_ids = json_decode($roleApp['app_ids'], true);
                    $diff_app_ids = array_diff($app_ids,$exist_app_ids);
                    //新权限添加
                    if (!empty($diff_app_ids)) {
                        $this->_addAccess($roleAppModel, $diff_app_ids, $role_id);
                    }
                    $role_app = $roleAppModel->where($cond)->save($data);
                    if ($role_app === false) {
                        $data['status'] = false;
                        $data['message'] = $roleAppModel->getError();
                        $roleAppModel->rollback();
                        $this->ajaxReturn($data);
                    }
                } else {
                    $data['role_id'] = $role_id;
                    $role_app = $roleAppModel->add($data);
                    if ($role_app === false) {
                        $data['status'] = false;
                        $data['message'] = $roleAppModel->getError();
                        $roleAppModel->rollback();
                        $this->ajaxReturn($data);
                    }
                    //权限添加
                    $this->_addAccess($roleAppModel, $app_ids, $role_id);
                }

                $data['status'] = true;
                $data['success'] = '角色分配应用成功';
                $roleAppModel->commit();
                $this->ajaxReturn($data);
            }
            $data['status'] = false;
            $data['message'] = '请选择角色所拥有的应用后再添加';
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
            $role_id = I('post.role_id');
            $roleAppModel = M('RoleApp');
            $role_app = $roleAppModel->where(array('role_id' => $role_id))->find();
            $app_ids = json_decode($role_app['app_ids'], true);
            $appModel = M('App');
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
        if (IS_POST) {
            dump(I('post.'));
            exit;
            $role_id = I('post.role_id');
            $app_ids = I('post.app_ids');
            $roleAppModel = M('RoleApp');
            $cond['role_id'] = $role_id;
            $data['app_ids'] = json_encode($app_ids);
            $data['update_time'] = time();
            $role_app = $roleAppModel->where($cond)->save($data);
            if ($role_app === false) {
                $data['status'] = false;
                $data['message'] = $roleAppModel->getError();
                $this->ajaxReturn($data);
            }
            $data['status'] = true;
            $data['success'] = '编辑角色应用成功';
            $this->ajaxReturn($data);
        }
        $role_id = I('get.id');
        $roleAppModel = M('RoleApp');
        $role_app = $roleAppModel->where(array('role_id' => $role_id))->find();
        if (empty($role_app)) {
            $this->error('未检索到该角色应用');
        }
        $app_ids = json_decode($role_app['app_ids'], true);
        $appModel = M('App'); //检索所有应用
        $apps = $appModel->select();
        if (!empty($apps)) {
            foreach ($apps as $app) {
                if (in_array($app['id'], $app_ids)) {
                    $apps_arr[] = array('app_id' => $app['id'], 'app_content' => $app['url'] . ' | ' . $app['name'], 'checked' => true);
                } else {
                    $apps_arr[] = array('app_id' => $app['id'], 'app_content' => $app['url'] . ' | ' . $app['name'], 'checked' => false);
                }
            }
        }
        $this->assign('apps_json', json_encode($apps_arr));
        $this->assign('role_app', $role_app);
        $this->display('edit');
    }

    //删除角色应用
    public function delete() {
        $role_id = I('post.role_id');
        $roleAppModel = M('RoleApp');
        $roleAppModel->startTrans();
        $role_app = $roleAppModel->where(array('role_id' => $role_id))->find();
        if (empty($role_app)) {
            $data['status'] = false;
            $data['message'] = '未检索到要删除的角色应用';
            $this->ajaxReturn($data);
        }
        $roleApp_res = $roleAppModel->where(array('role_id' => $role_id))->delete();
        if ($roleApp_res === false) {
            $data['status'] = false;
            $data['message'] = '删除角色应用失败';
            $roleAppModel->rollback();
            $this->ajaxReturn($data);
        }
        //删除角色响应的权限
        $accessModel = M('Access');
        $access_del = $accessModel->where(array('role_id' => $role_id))->delete();
        if ($access_del === false) {
            $data['status'] = false;
            $data['message'] = '删除角色所有权限失败';
            $roleAppModel->rollback();
            $this->ajaxReturn($data);
        }
        $data['status'] = true;
        $data['success'] = '删除角色应用成功';
        $roleAppModel->commit();
        $this->ajaxReturn($data);
    }

}

?>