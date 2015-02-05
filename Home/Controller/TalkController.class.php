<?php
/**
 * 前台说说控制器
 */
namespace Home\Controller;

use Think\Controller;

class TalkController extends BaseController {

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
