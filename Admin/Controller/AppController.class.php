<?php

namespace Admin\Controller;

use Think\Controller;

class AppController extends BaseController {

    //应用管理
    public function index() {

        $this->display('index');
    }

    //添加应用
    public function add() {
        if (IS_POST) {
            
        }
        $this->display('add');
    }

    //编辑应用
    public function edit() {
        
    }

    //删除应用
    public function delete() {
        
    }

}

?>