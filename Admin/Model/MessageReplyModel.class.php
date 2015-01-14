<?php
/**
 * 留言回复功能模型
 * @author 周东宝 2015-01-13
 */
namespace Admin\Model;

use Think\Model;

Class MessageReplyModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('message_id','require','回复的留言ID必须！',self::MUST_VALIDATE),
        array('content','require','留言回复内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取留言回复状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //获取留言回复内容
    public static  function getContent($message_reply_id){
        if(empty($message_reply_id)){
            return ;
        }
        $messageReply = M('MessageReply')->where(array('id'=>$message_reply_id))->find();
        return $messageReply['content'];
    }
}

?>