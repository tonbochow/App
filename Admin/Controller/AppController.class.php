<?php

namespace Admin\Controller;

use Think\Controller;

class AppController extends BaseController {

    //应用管理
    public function index() {
        $name = I('get.name');
        $appModel = M('App');
        if (empty($name)) {
            $apps = $appModel->select();
            $app_count = $appModel->count();
        } else {
            $cond['name'] = array('like', "%$name%");
            $apps = $appModel->where($cond)->select();
            $app_count = $appModel->where($cond)->count();
        }
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($app_count, 1);
        if (!empty($name)) {
            $apps = $appModel->limit($Page->firstRow . ',' . $Page->listRows)->where($cond)->select();
        } else {
            $apps = $appModel->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }
        $show = $Page->show(); // 分页显示输出
        $this->assign('name', $name);
        $this->assign('page', $show);
        $this->assign('apps', $apps);
        $this->display('index');
    }

    //添加应用
    public function add() {
        if (IS_POST) {
            $appModel = D('App');
            $appModel->startTrans();
            $module = I('post.module');
            $controller = I('post.controller');
            $action = I('post.action');
            $nodeModel = M('Node');
            //1检索module是否已存在 不存在则生成
            $node_module['name'] = $module;
            $module_node = $nodeModel->where($node_module)->find();
            if (empty($module_node)) {
                $node_module['title'] = I('post.module_title');
                $node_module['status'] = I('post.status');
                $node_module['pid'] = 0;
                $node_module['level'] = 1;
                $module_node_id = $nodeModel->add($node_module);
                if ($module_node_id === false) {
                    $appModel->rollback();
                }
            }
            //2检索controller是否已存在 不存在则生成 条件:name + pid + level
            $node_controller['name'] = $controller;
            $node_controller['pid'] = !empty($module_node['id']) ? $module_node['id'] : $module_node_id;
            $node_controller['level'] = 2;
            $controller_node = $nodeModel->where($node_controller)->find();
            if (empty($controller_node)) {
                $node_controller['title'] = I('post.controller_title');
                $node_controller['status'] = I('post.status');
                $controller_node_id = $nodeModel->add($node_controller);
                if ($controller_node_id === false) {
                    $appModel->rollback();
                }
            }
            //3检索action是否已存在 不存在则生成 条件:name + pid + level
            $node_action['name'] = $action;
            $node_action['pid'] = !empty($controller_node['id']) ? $controller_node['id'] : $controller_node_id;
            $node_action['level'] = 3;
            $action_node = $nodeModel->where($node_action)->find();
            if (empty($action_node)) {
                $node_action['title'] = I('post.action_title');
                $node_action['status'] = I('post.status');
                $action_node_id = $nodeModel->add($node_action);
                if ($action_node_id === false) {
                    $appModel->rollback();
                }
            }
            //生成app
            $app_data = I('post.');
            $app_data['module_node_id'] = !empty($module_node['id']) ? $module_node['id'] : $module_node_id;
            $app_data['controller_node_id'] = !empty($controller_node['id']) ? $controller_node['id'] : $controller_node_id;
            $app_data['action_node_id'] = $action_node_id;
            if ($appModel->create($app_data)) {
                $app_id = $appModel->add($app_data);
                if ($app_id) {
                    $data['status'] = true;
                    $data['success'] = '创建成功';
                } else {
                    $data['status'] = false;
                    $data['message'] = $appModel->getError();
                    $appModel->rollback();
                }
            } else {
                $data['status'] = false;
                $data['message'] = $appModel->getError();
                $appModel->rollback();
            }
            $appModel->commit();
            $this->ajaxReturn($data);
        }
        $this->display('add');
    }

    //编辑应用
    public function edit() {
        if (IS_POST) {
            $app_cond['id'] = I('post.id');
            $app = M('App')->where($app_cond)->find();
            if (empty($app)) {
                $data['message'] = '应用不存在';
                $data['status'] = false;
                $this->ajaxReturn($data);
            }
            $data = I('post.');
            $data['status'] = I('post.status')['id'];
            $appModel = D('App');
            $appModel->startTrans();
            //若module改变则更新node表中module 
            if (strtolower($app['module']) != strtolower(I('post.module'))) {
                $nodeModel = M('Node');
                $node_cond['id'] = I('post.module_node_id');
                $node_data['name'] = I('post.module');
                $node_data['title'] = I('post.module_title');
                $node_data['status'] = $data['status'];
                $module_res = $nodeModel->where($node_cond)->save($node_data);
                if ($module_res === false) {
                    $appModel->rollback();
                }
            }
            //若controller改变或者app.status改变则更新node表中controller
            if (strtolower($app['controller']) != strtolower(I('post.controller')) || $app['status'] != $data['status']) {
                $nodeModel = M('Node');
                $node_cond['id'] = I('post.controller_node_id');
                $node_data['name'] = I('post.controller');
                $node_data['title'] = I('post.controller_title');
                $node_data['status'] = $data['status'];
                $controller_res = $nodeModel->where($node_cond)->save($node_data);
                if ($controller_res === false) {
                    $appModel->rollback();
                }
            }
            //若action改变或者app.status改变则更新node表中action
            if (strtolower($app['action']) != strtolower(I('post.action')) || $app['status'] != $data['status']) {
                $nodeModel = M('Node');
                $node_cond['id'] = I('post.action_node_id');
                $node_data['name'] = I('post.action');
                $node_data['title'] = I('post.action_title');
                $node_data['status'] = $data['status'];
                $action_res = $nodeModel->where($node_cond)->save($node_data);
                if ($action_res === false) {
                    $appModel->rollback();
                }
            }
            $res = $appModel->create($data);
            if ($res) {
                $app_res = $appModel->save();
                if ($res) {
                    $data['status'] = true;
                    $data['success'] = '编辑成功';
                } else {
                    $data['status'] = false;
                    $data['message'] = $appModel->getError();
                }
            } else {
                $data['message'] = $appModel->getError();
                $data['status'] = false;
            }
            $appModel->commit();
            $this->ajaxReturn($data);
        }
        $app_id = I('get.id');
        $app = M('App')->where(array('id' => $app_id))->find();
        if (empty($app)) {
            $this->error('未检索到该应用');
        }
        $this->assign('json_app', json_encode($app));
        $this->assign('app', $app);
        $this->display('edit');
    }

    //删除应用
    public function delete() {
        $app_id = I('post.app_id');
        $app_res = M('App')->where(array('id' => $app_id))->find();
        if (empty($app_res)) {
            $this->error('未检索到该应用');
        }
        $action_node_id = $app_res['action_node_id'];
        $controller_node_id = $app_res['controller_node_id'];
        //删除app应用数据
        $appModel = M('App');
        $appModel->startTrans();
        $app_data['id'] = $app_id;
        $app = $appModel->where($app_data)->delete();
        if (!$app) {
            $data['status'] = false;
            $data['message'] = '删除应用表数据失败';
            $appModel->rollback();
        }
        //检索node表中controller是否对应多个action
        $nodeModel = M('Node');
        $node_cond['pid'] = $controller_node_id;
        $node_cond['level'] = 3;
        $node_res = $nodeModel->where($node_cond)->select();
        if (count($node_res) == 1) {//删除node表action 及 controller
            $controller_res = $nodeModel->where(array('id' => $controller_node_id))->delete();
            if ($controller_res === false) {
                $data['status'] = false;
                $data['message'] = '删除node表controller失败';
                $appModel->rollback();
            }
        }
        $action_res = $nodeModel->where(array('id' => $action_node_id))->delete();
        if ($action_res === false) {
            $data['status'] = false;
            $data['message'] = '删除node表action失败';
            $appModel->rollback();
        }
        $data['status'] = true;
        $data['success'] = '删除成功';
        $appModel->commit();
        $this->ajaxReturn($data);
    }

}

?>