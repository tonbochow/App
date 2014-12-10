<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends BaseController {

    public function index() {
        $name = I('get.name');
        $role_id = I('get.role_id');
        $is_superuser = I('get.is_superuser');
        $roles_arr = \Admin\Model\RoleModel::getRoles();
        $superuser_arr = \Admin\Model\UserModel::checkSuperUser();
        $userModel = M('User');
        $user_cond = array();
        if (!empty($name)) {
            $user_cond['name'] = array('like', '%' . $name . '%');
        }
        if (!empty($role_id)) {
            $userIds = \Admin\Model\RoleUserModel::getRoleUserIds($role_id);
            if ($userIds !== false) {
                $user_cond['id'] = array("in", $userIds);
            }
        }
        if ($is_superuser !== '') {
            $user_cond['is_superuser'] = $is_superuser;
        }
        $user_count = $userModel->where($user_cond)->count();
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($user_count, 1);
        $users = $userModel->limit($Page->firstRow . ',' . $Page->listRows)->where($user_cond)->select();
        $show = $Page->show(); // 分页显示输出
        $this->assign('superuser_arr', $superuser_arr);
        $this->assign('roles_arr', $roles_arr);
        if ($is_superuser !== '') {
            $this->assign('is_superuser', intval($is_superuser));
        } else {
            $this->assign('is_superuser', $is_superuser);
        }
        $this->assign('name', $name);
        $this->assign('role_id', $role_id);
        $this->assign('page', $show);
        $this->assign('users', $users);
        $this->display('index');
    }

    //禁用用户
    public function disable() {
        $user_id = I('post.user_id');
        $userModel = M('User');
        $user_data['status'] = \Admin\Model\UserModel::$UNAVAILABLE;
        $user_data['update_time'] = time();
        $disable_res = $userModel->where(array('id' => $user_id))->save($user_data);
        if ($disable_res) {
            $data['status'] = true;
            $data['success'] = '禁用成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '禁用失败';
        $this->ajaxReturn($data);
    }

    //启用用户
    public function enable() {
        $user_id = I('post.user_id');
        $userModel = M('User');
        $user_data['status'] = \Admin\Model\UserModel::$AVAILABLE;
        $user_data['update_time'] = time();
        $enable_res = $userModel->where(array('id' => $user_id))->save($user_data);
        if ($enable_res) {
            $data['status'] = true;
            $data['success'] = '启用成功';
            $this->ajaxReturn($data);
        }
        $data['status'] = false;
        $data['message'] = '启用失败';
        $this->ajaxReturn($data);
    }

    //用户添加
    public function add() {
        if (IS_POST) {
            $user_data = I('post.');
            $role_id = $user_data['role_id'];
            unset($user_data['role_id']);
            $userModel = D('User');
            $userModel->startTrans();
            if ($userModel->create($user_data)) {
                $user_id = $userModel->add();
                if ($user_id) {
                    $roleUserModel = M('RoleUser');
                    $roleUser_data['role_id'] = $role_id;
                    $roleUser_data['user_id'] = $user_id;
                    $roleUser_data['role_name'] = \Admin\Model\RoleModel::getRoleName($role_id);
                    $role_user = $roleUserModel->add($roleUser_data);
                    if (empty($role_user)) {
                        $data['status'] = false;
                        $data['message'] = $roleUserModel->getError();
                        $userModel->rollback();
                        $this->ajaxReturn($data);
                    }
                    $data['status'] = true;
                    $data['success'] = '用户添加成功';
                    $userModel->commit();
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $userModel->getError();
            $userModel->rollback();
            $this->ajaxReturn($data);
        }
        $roles = \Admin\Model\RoleModel::getRoles(null, false);
        if (!empty($roles)) {
            foreach ($roles as $role_id => $role_name) {
                $roles_arr[] = array('id' => $role_id, 'role_name' => $role_name);
            }
        }
        $this->assign('roles_arr', json_encode($roles_arr));
        $backendLogin_arr[] = array('id' => \Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN, 'backend' => \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN));
        $backendLogin_arr[] = array('id' => \Admin\Model\RoleModel::$DENY_BACKEND_LOGIN, 'backend' => \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$DENY_BACKEND_LOGIN));
        $this->assign('backendLogin_arr', json_encode($backendLogin_arr));
        $this->display('add');
    }

    //上传用户头像
    public function uploadHeadimg() {
        $user_id = I('post.user_id');
        $verifyToken = md5('unique_salt' . I('post.timestamp'));
        $config = C('ADMIN_HEADIMG_UPLOAD');
        $config['savePath'] = '/user/' . $user_id . '/headimg/';
        if (!empty($_FILES) && I('post.token') == $verifyToken) {
            $upload = new \Think\Upload($config);
            $res = $upload->upload($_FILES);
            if ($res !== false) {
                $photo_url = '/upload/user/' . $user_id . '/headimg/' . $res['Filedata']['savename'];
                $image = new \Think\Image();
                $image->open(C('ROOT_PATH') . $photo_url);
                @unlink(C('ROOT_PATH') . $photo_url);
                $image->thumb(120, 120, \Think\Image::IMAGE_THUMB_SCALE)->save(C('ROOT_PATH') . $photo_url);
                echo $photo_url;
                exit;
            }
        }
        $this->error("上传失败");
    }

    //用户编辑
    public function edit() {
        if (IS_POST) {
            $user_data = I('post.');
            $user_data['status'] = I('post.status')['id'];
            $user_data['backend_login'] = I('post.backend_login')['id'];
            $user_data['is_superuser'] = I('post.is_superuser')['id'];
            $userModel = D('User');
            if ($userModel->create($user_data)) {
                $update_res = $userModel->save();
                if ($update_res) {
                    if (!empty($user_data['headimg'])) {
                        delFileUnderDir(C('ROOT_PATH') . '/upload/user/' . $user_data['id'] . '/headimg', C('ROOT_PATH') . $user_data['headimg']);
                    }
                    $data['status'] = true;
                    $data['success'] = '编辑用户成功';
                    $this->ajaxReturn($data);
                }
            }
            $data['status'] = false;
            $data['message'] = $userModel->getError();
            $this->ajaxReturn($data);
        }
        $user_id = I('get.id');
        $userModel = M('User');
        $user = $userModel->where(array('id' => $user_id))->find();
        if (empty($user)) {
            $this->error('编辑的用户不存在');
        }
        $backendLogin_arr[] = array('id' => \Admin\Model\RoleModel::$DENY_BACKEND_LOGIN, 'backend' => \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$DENY_BACKEND_LOGIN));
        $backendLogin_arr[] = array('id' => \Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN, 'backend' => \Admin\Model\RoleModel::getBackendLogin(\Admin\Model\RoleModel::$ALLOW_BACKEND_LOGIN));
        $this->assign('backendLogin_arr', json_encode($backendLogin_arr));
        $this->assign('user', $user);
        $this->assign('json_user', json_encode($user));
        $this->display('edit');
    }

}
