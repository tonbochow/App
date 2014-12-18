<?php
/**
 * 后台说说功能模型
 * @author 周东宝 2014-12-18
 */
namespace Admin\Model;

use Think\Model;

Class TalkModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    public static $COMMENT_ALLOW = 1;
    public static $COMMENT_DENY = 0;
    
    protected $_validate = array(
        array('content','require','说说内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取说说状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }

    //获取说说是否允许评论
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