<?php

/**
 * 前台日志控制器
 */

namespace Home\Controller;

use Think\Controller;

class ArticleController extends BaseController {

    public function index() {
        $masterModel = M('Master');
        $master = $masterModel->find();
        $this->assign('master', $master);
        $this->display('index');
    }

    public function view() {
        $this->display('view');
    }

}
