<?php

namespace Admin\Controller;

use Think\Controller;

class RoleUserController extends BaseController {
    
    //用户角色管理列表
    public function index(){
        
        $this->display('index');
    }
}

?>