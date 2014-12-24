<?php
/**
 * 相片评论功能模型
 * @author 周东宝 2014-12-23
 */
namespace Admin\Model;

use Think\Model;

Class PhotoCommentModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('album_id','require','评论相片所属相册ID必须！',self::MUST_VALIDATE),
        array('photo_id','require','评论相片ID必须！',self::MUST_VALIDATE),
        array('content','require','相片评论内容必须！',self::MUST_VALIDATE),
        array('list_order', '', '排序已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取相片评论状态
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