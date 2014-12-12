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
        array('title', 'require', '日志标题已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('title','require','日志标题必须！',self::MUST_VALIDATE),
        array('content','require','日志内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取日志状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '显示';
        $status_arr[self::$UNAVAILABLE] = '隐藏';
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