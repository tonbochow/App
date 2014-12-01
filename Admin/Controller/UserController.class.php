<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends BaseController {
    
    public function index(){
        $this->display('index');
    }
    
    public function add(){
        $this->display('add');
    }
}
