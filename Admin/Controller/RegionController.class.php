<?php

namespace Admin\Controller;

use Think\Controller;

class RegionController extends BaseController {

    /**
     * 区域显示首页
     * 默认显示 parent=86 的所有省份信息
     */
    public function index() {
        $model = D('Region');
        $province = $model->getRegion(86);
        $this->assign('province', $province);
        $this->display();
    }

    public function getRegion() {
        $model = D('Region');
        $parent = intval($_REQUEST['pid']);
        $list = $model->getRegion($parent);
        echo json_encode($list);
    }

}
