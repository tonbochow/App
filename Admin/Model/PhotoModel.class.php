<?php
/**
 * 相片功能模型
 * @author 周东宝 2014-12-22
 */
namespace Admin\Model;

use Think\Model;

Class PhotoModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('album_id','require','相册id必须！',self::MUST_VALIDATE),
        array('photo_name','require','相片名必须！',self::MUST_VALIDATE),
        array('photo_url','require','相片url必须！',self::MUST_VALIDATE),
        array('list_order', '', '排序已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取相片状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //获取相片名
    public static  function getName($photo_id){
        if(empty($photo_id)){
            return ;
        }
        $photo = M('Photo')->where(array('id'=>$photo_id))->find();
        return $photo['photo_name'];
    }
}

?>