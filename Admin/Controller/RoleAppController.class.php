<?php

namespace Admin\Controller;

use Think\Controller;

class RoleAppController extends BaseController {
    
    //角色权限管理列表
    public function index(){
        
        $this->display('index');
    }
}

?>