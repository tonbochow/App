<?php
namespace Home\Controller;
use Think\Controller;
class TalkController extends  Controller {
    public function index(){
        $masterModel = M('Master');
        $master = $masterModel->find();
        $this->assign('master',$master);
        $this->display('index');
    }
}