<?php
/**
 * 视频功能模型
 * @author 周东宝 2014-12-24
 */
namespace Admin\Model;

use Think\Model;

Class VideoModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('video_name','require','视频文件名必须！',self::MUST_VALIDATE),
        array('video_url','require','视频文件url必须！',self::MUST_VALIDATE),
        array('list_order', '', '排序已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取视频状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //获取视频名
    public static  function getName($video_id){
        if(empty($video_id)){
            return ;
        }
        $video = M('Video')->where(array('id'=>$video_id))->find();
        return $video['video_name'];
    }
}

?>