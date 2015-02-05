<?php

/**
 * 前台留言控制器
 */

namespace Home\Controller;

use Think\Controller;

class MessageController extends BaseController {

    public function index() {
        $messageModel = M('Message');
        $cond['status'] = \Admin\Model\MessageModel::$AVAILABLE;
        $count = $messageModel->where($cond)->count();
//        $Page = new \Think\Page($count, C('SYSTEM.front_page_num'));
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($count, 2);
        $show = $Page->show();
        $messages = $messageModel->where($cond)->order('create_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        if(!empty($contents)){
//            foreach($contents as $key=>$content){
//                $contents[$key]['title'] = htmlspecialchars_decode(stripslashes($content['title']));
//                $contents[$key]['content'] = htmlspecialchars_decode(stripslashes($content['content']));
//            }
//        }
//        dump($contents);
        $this->assign('page', $show);
        $this->assign('messages', $messages);
        $this->display('index');
    }

    public function view() {
        $this->display('view');
    }

}
