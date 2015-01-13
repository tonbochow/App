<?php
/**
 * 留言功能模型
 * @author 周东宝 2015-01-13
 */
namespace Admin\Model;

use Think\Model;

Class MessageModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('content','require','留言内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取留言状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //获取留言内容
    public static  function getContent($message_id){
        if(empty($message_id)){
            return ;
        }
        $message = M('Message')->where(array('id'=>$message_id))->find();
        return $message['content'];
    }
}

?>