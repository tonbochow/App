<?php

namespace Admin\Controller;

use Think\Controller;

class RoleUserController extends BaseController {

    //用户角色管理列表
    public function index() {
        $userModel = M('User');
        $users = $userModel->select();
        $user_count = $userModel->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($user_count, 1);
        $users = $userModel->limit($Page->firstRow . ',' . $Page->listRows)->select();
        if (!empty($users)) {
            foreach ($users as $user) {
                $user_ids[] = $user['id'];
            }
        }
        $roleUserModel = M('RoleUser');
        $role_user = $roleUserModel->where(array('user_id' => array('in', $user_ids)))->select();
        $user_arr = array();
        if (!empty($role_user)) {
            foreach ($role_user as $roleUser) {
                if (isset($user_arr[$roleUser['user_id']])) {
                    $user_arr[$roleUser['user_id']]['role_id'] .= ',' . $roleUser['role_id'];
                    $user_arr[$roleUser['user_id']]['role_name'] .= ',' . $roleUser['role_name'];
                } else {
                    $user_arr[$roleUser['user_id']]['role_id'] = $roleUser['role_id'];
                    $user_arr[$roleUser['user_id']]['role_name'] = $roleUser['role_name'];
                }
            }
        }
        if (!empty($users)) {
            foreach ($users as $key => $user) {
                $users[$key]['role_ids'] = $user_arr[$user['id']]['role_id'];
                $users[$key]['role_names'] = $user_arr[$user['id']]['role_name'];
            }
        }

        $show = $Page->show(); // 分页显示输出
        $this->assign('users', $users);
        $this->assign('page', $show);
        $this->display('index');
    }

    //用户所有角色编辑
    public function edit() {
        if (IS_POST) {
            $user_id = I('post.user_id');
            $user_roleids = \Admin\Model\RoleUserModel::getUserRoleIds($user_id);
            $role_ids = I('post.role_ids');
            $userModel = M('User');
            $userModel->startTrans();
            //为空表示把用户的所有角色都去掉了(该用户可能无法再登录)
            if (empty($role_ids)) {
                $roleUserModel = M('RoleUser');
                $roleUser_del = $roleUserModel->where(array('user_id' => $user_id))->delete();
                if ($roleUser_del === false) {
                    $data['status'] = false;
                    $data['message'] = '删除用户所有角色失败';
                    $userModel->rollback();
                    $this->ajaxReturn($data);
                }
                $data['status'] = true;
                $data['success'] = '删除用户所有角色成功';
                $userModel->commit();
                $this->ajaxReturn($data);
            }
            //用户需要去掉的角色
            if (!empty($user_roleids)) {
                $del_roleids = array_diff($user_roleids, $role_ids);
                if (!empty($del_roleids)) {
                    $userRoleModel = M('RoleUser');
                    $del_cond['user_id'] = $user_id;
                    $del_cond['role_id'] = array('in',$del_roleids);
                    $user_role_del = $userRoleModel->where($del_cond)->delete();
                    if ($user_role_del === false) {
                        $data['status'] = false;
                        $data['message'] = '删除用户某个角色失败';
                        $userModel->rollback();
                        $this->ajaxReturn($data);
                    }
                }
            }
            //用户需要添加的角色
            foreach ($role_ids as $role_id) {
                $role_user = M('RoleUser')->where(array('user_id' => $user_id, 'role_id' => $role_id))->find();
                if (empty($role_user)) {//用户增加此角色
                    $roleUserModel = M('RoleUser');
                    $roleUser_data['role_id'] = $role_id;
                    $roleUser_data['role_name'] = \Admin\Model\RoleModel::getRoleName($role_id);
                    $roleUser_data['user_id'] = $user_id;
                    $roleUser_add = $roleUserModel->add($roleUser_data);
                    if ($roleUser_add === false) {
                        $data['status'] = false;
                        $data['message'] = \Admin\Model\RoleModel::getRoleName($role_id) . '添加失败';
                        $userModel->rollback();
                        $this->ajaxReturn($data);
                    }
                }
            }
            $data['status'] = true;
            $data['success'] = '用户角色编辑成功';
            $userModel->commit();
            $this->ajaxReturn($data);
        }
        $user_id = I('get.id');
        $userModel = M('User');
        $user = $userModel->where(array('id' => $user_id))->find();
        if (empty($user)) {
            $this->error('未检索到用户信息');
        }
        $roleUserModel = M('RoleUser');
        $role_user = $roleUserModel->where(array('user_id' => $user_id))->select();
        if (!empty($role_user)) {
            foreach ($role_user as $roleUser) {
                $role_ids[] = $roleUser['role_id'];
            }
        }

        $roles = M('Role')->select();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if (in_array($role['id'], $role_ids)) {
                    $roles_arr[] = array('id' => $role['id'], 'name' => $role['name'], 'checked' => true);
                } else {
                    $roles_arr[] = array('id' => $role['id'], 'name' => $role['name'], 'checked' => false);
                }
            }
        }
        $this->assign('roles', json_encode($roles_arr));
        $this->assign('user', $user);
        $this->display('edit');
    }

}

?>