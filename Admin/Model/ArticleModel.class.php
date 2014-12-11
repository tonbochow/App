<?php
/**
 * 后台日志功能模型
 * @author 周东宝 2014-12
 */
namespace Admin\Model;

use Think\Model;

Class ArticleModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    public static $COMMENT_ALLOW = 1;
    public static $COMMENT_DENY = 0;
    
    protected $_validate = array(
        array('title', '', '日志标题已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取日志状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '正常';
        $status_arr[self::$UNAVAILABLE] = '禁用';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }

    //获取日志是否允许评论
    public static function getCommentStatus($status = null){
        $status_arr = array('' => '请选择');
        $status_arr[self::$COMMENT_ALLOW] = '允许';
        $status_arr[self::$COMMENT_DENY] = '禁止';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
}

?>