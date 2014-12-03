<?php
namespace Admin\Controller;
use Think\Controller;
class MasterController extends BaseController{
    
    public function index(){
        $masterModel = M('Master');
        $master = $masterModel->find();
        
        $this->assign('master',$master);
        $this->display('index');
    }
}