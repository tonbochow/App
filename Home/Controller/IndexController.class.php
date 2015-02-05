<?php

/**
 * 前台首页
 */

namespace Home\Controller;

use Think\Controller;

class IndexController extends BaseController {

    public function index() {
        $contentModel = M('Content');
        $cond['status'] = \Admin\Model\ContentModel::$AVAILABLE;
        $count = $contentModel->where($cond)->count();
//        $Page = new \Think\Page($count, C('SYSTEM.front_page_num'));
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($count, 2);
        $show = $Page->show();
        $contents = $contentModel->where($cond)->order('create_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        if(!empty($contents)){
//            foreach($contents as $key=>$content){
//                $contents[$key]['title'] = htmlspecialchars_decode(stripslashes($content['title']));
//                $contents[$key]['content'] = htmlspecialchars_decode(stripslashes($content['content']));
//            }
//        }
//        dump($contents);
        $this->assign('page',$show);
        $this->assign('contents', $contents);
        $this->display('index');
    }

}
