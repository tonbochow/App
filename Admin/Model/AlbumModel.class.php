<?php
/**
 * 相册功能模型
 * @author 周东宝 2014-12-20
 */
namespace Admin\Model;

use Think\Model;

Class AlbumModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('title','require','相册标题必须！',self::MUST_VALIDATE),
        array('thumb_url','require','相册封面url必须！',self::MUST_VALIDATE),
        array('list_order', '', '排序已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取相册状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //获取相册名
    public static  function getName($album_id){
        if(empty($album_id)){
            return ;
        }
        $album = M('Album')->where(array('id'=>$album_id))->find();
        return $album['title'];
    }
}

?>