<?php

namespace Admin\Model;

use Think\Model;

Class ArticleCategoryModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    protected $_validate = array(
        array('cate_name', '', '分类已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
        array('sort', '', '分类排序已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取日志分类状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '正常';
        $status_arr[self::$UNAVAILABLE] = '禁用';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }

    //获取所有日志分类
    public static function getCategorys() {
        $mod = M('ArticleCategory');
        $article_category = $mod->field('id,cate_name')->select();
        return $article_category;
    }

    //获取日志分类名
    public static function getCategoryName($cate_id){
        if(!$cate_id){
            return ;
        }
        $mod = M('ArticleCategory');
        $article_category = $mod->where(array('id'=>$cate_id))->field('cate_name')->find();
        return $article_category['cate_name'];
    }
}

?>