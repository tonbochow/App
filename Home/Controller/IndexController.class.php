<?php
/**
 * 前台首页
 */
namespace Home\Controller;

use Think\Controller;

class IndexController extends BaseController {

    public function index() {
        $contentModel = M('Content');
        $contents = $contentModel->order('create_time desc')->limit(C('SYSTEM.front_page_num'))->select();
//        if(!empty($contents)){
//            foreach($contents as $key=>$content){
//                $contents[$key]['title'] = htmlspecialchars_decode(stripslashes($content['title']));
//                $contents[$key]['content'] = htmlspecialchars_decode(stripslashes($content['content']));
//            }
//        }
        
//        dump($contents);
        $this->assign('contents',$contents);
        $this->display('index');
    }

}
