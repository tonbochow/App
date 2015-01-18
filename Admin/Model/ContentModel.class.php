<?php
/**
 * 内容功能模型
 * @author 周东宝 2015-01-18
 */
namespace Admin\Model;

use Think\Model;

Class ContentModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    public static $TYPE_TALK = 1;//说说类型
    public static $TYPE_ARTICLE= 2;//日志类型
    public static $TYPE_ALBUM = 3;//相册类型
    public static $TYPE_VIDEO = 4;//视频类型
    
    protected $_validate = array(
        array('tapy_id','require','内容必须！',self::MUST_VALIDATE),
        array('type','require','内容类型必须！',self::MUST_VALIDATE),
        array('title','require','内容标题必须！',self::MUST_VALIDATE),
        array('content','require','内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取内容状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //获取内容
    public static  function getContent($id){
        if(empty($id)){
            return ;
        }
        $content = M('Content')->where(array('id'=>$id))->find();
        return $content['content'];
    }
}

?>