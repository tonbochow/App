<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends  Controller {
    public function index(){
        $masterModel = M('Master');
        $master = $masterModel->find();
        $system = M('Website')->find();
        $back_img = $system['background_img'];
        $this->assign('background_img',$back_img);
        $this->assign('master',$master);
        $this->display('index');
    }
}