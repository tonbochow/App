<?php

namespace Admin\Controller;

use Think\Controller;

class RoleController extends BaseController {

    //角色列表
    public function index() {
        $role_id = I('get.role_id');
        $roles_arr = \Admin\Model\RoleModel::getRoles();
        $roleModel = M('Role');
        if (!empty($role_id)) {
            $roles = $roleModel->where(array('id' => $role_id))->select();
            $role_count = $roleModel->where(array('id' => $role_id))->count();
        } else {
            $roles = $roleModel->select();
            $role_count = $roleModel->count();
        }
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($role_count, 1);
        if (!empty($role_id)) {
            $roles = $roleModel->limit($Page->firstRow . ',' . $Page->listRows)->where(array('id' => $role_id))->select();
        } else {
            $roles = $roleModel->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        $show = $Page->show(); // 分页显示输出
        $this->assign('roles_arr', $roles_arr);
        $this->assign('role_id', $role_id);
        $this->assign('page', $show);
        $this->assign('roles', $roles);
        $this->display('index');
    }

    //角色添加
    public function add() {
        if (IS_POST) {//添加角色
            $roleModel = D('Role');
            $res = $roleModel->create();
            if ($res) {
                $roleModel->add();
                $data['status'] = true;
                $data['success'] = '创建成功';
            } else {
                $data['message'] = $roleModel->getError();
                $data['status'] = false;
            }
            $this->ajaxReturn($data);
        }
        $backendLogin_arr[] = array('id'=>  \Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN,'backend'=>  \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN));
        $backendLogin_arr[] = array('id'=>  \Admin\Model\RoleModel::$DENY_BACKEND_LOGIN,'backend'=>  \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$DENY_BACKEND_LOGIN));
        $this->assign('backendLogin_arr',json_encode($backendLogin_arr));
        $this->display('add');
    }

    //角色编辑
    public function edit() {
        if (IS_POST) {
            $roleModel = D('Role');
            $data = I('post.');
            $data['status'] = I('post.status')['id'];
            $res = $roleModel->create($data);
            if ($res) {
                $roleModel->save();
                $data['status'] = true;
                $data['success'] = '编辑成功';
            } else {
                $data['message'] = $roleModel->getError();
                $data['status'] = false;
            }
            $this->ajaxReturn($data);
        }
        $role_id = I('get.id');
        $role = M('Role')->where(array('id' => $role_id))->find();
        if (empty($role)) {
            $this->error('未检索到该角色');
        }
        $backendLogin_arr[] = array('id'=>  \Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN,'backend'=>  \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN));
        $backendLogin_arr[] = array('id'=>  \Admin\Model\RoleModel::$DENY_BACKEND_LOGIN,'backend'=>  \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$DENY_BACKEND_LOGIN));
        $this->assign('backendLogin_arr',json_encode($backendLogin_arr));
        $this->assign('role', $role);
        $this->display('edit');
    }

    //角色删除
    public function delete() {
        $role_id = I('post.role_id');
        $role = M('Role')->where(array('id' => $role_id))->find();
        if (empty($role)) {
            $this->error('未检索到该角色');
        }
        //删除role角色表数据
        $roleModel = M('Role');
        $roleModel->startTrans();
        $role_data['id'] = $role_id;
        $role = $roleModel->where($role_data)->delete();
        if (!$role) {
            $roleModel->rollback();
            $data['status'] = false;
            $data['message'] = '删除角色表数据失败';
            $this->ajaxReturn($data);
        }
        //删除role_use 角色用户表数据
        $roleUserModel = M('RoleUser');
        $roleUser_data['role_id'] = $role_id;
        $role_user = $roleUserModel->where($roleUser_data)->delete();
        if ($role_user === false) {
            $roleModel->rollback();
            $data['status'] = false;
            $data['message'] = '删除role_user表数据失败';
            $this->ajaxReturn($data);
        }
        //删除access权限表数据
        $accessModel = M('Access');
        $access_data['role_id'] = $role_id;
        $access = $accessModel->where($access_data)->delete();
        if ($access === false) {
            $roleModel->rollback();
            $data['status'] = false;
            $data['message'] = '删除access表数据失败';
            $this->ajaxReturn($data);
        }
        //删除role_app角色应用表数据
        $roleAppModel = M('RoleApp');
        $roleApp_del = $roleAppModel->where(array('role_id' => $role_id))->delete();
        if ($roleApp_del === false) {
            $roleModel->rollback();
            $data['status'] = false;
            $data['message'] = '删除role_app表数据失败';
            $this->ajaxReturn($data);
        }
        $data['status'] = true;
        $data['success'] = '删除成功';
        $roleModel->commit();
        $this->ajaxReturn($data);
    }

}

?>