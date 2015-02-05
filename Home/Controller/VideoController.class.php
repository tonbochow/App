<?php

/**
 * 前台视频控制器
 */

namespace Home\Controller;

use Think\Controller;

class VideoController extends BaseController {

    public function index() {
        $videoModel = M('Video');
        $cond['status'] = \Admin\Model\VideoModel::$AVAILABLE;
        $count = $videoModel->where($cond)->count();
//        $Page = new \Think\Page($count, C('SYSTEM.front_page_num'));
        import('Common.Extends.Page.BootstrapPage');
        $Page = new \BootstrapPage($count, 2);
        $show = $Page->show();
        $videos = $videoModel->where($cond)->order('create_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        if(!empty($contents)){
//            foreach($contents as $key=>$content){
//                $contents[$key]['title'] = htmlspecialchars_decode(stripslashes($content['title']));
//                $contents[$key]['content'] = htmlspecialchars_decode(stripslashes($content['content']));
//            }
//        }
//        dump($contents);
        $this->assign('page', $show);
        $this->assign('videos', $videos);
        $this->display('index');
    }

    public function view() {
        $this->display('view');
    }

}
