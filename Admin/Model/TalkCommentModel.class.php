<?php
/**
 * 后台说说评论功能模型
 * @author 周东宝 2014-12-18
 */
namespace Admin\Model;

use Think\Model;

Class TalkCommentModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('talk_id','require','说说ID必须！',self::MUST_VALIDATE),
        array('content','require','说说评论内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取说说评论状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
}

?>