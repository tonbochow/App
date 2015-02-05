<?php

/**
 * 前台留言控制器
 */

namespace Home\Controller;

use Think\Controller;

class MessageController extends BaseController {

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
