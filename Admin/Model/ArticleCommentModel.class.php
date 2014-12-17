<?php
/**
 * 后台日志评论功能模型
 * @author 周东宝 2014-15
 */
namespace Admin\Model;

use Think\Model;

Class ArticleCommentModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    protected $_validate = array(
        array('article_id','require','评论日志必须！',self::MUST_VALIDATE),
        array('article_title','require','评论日志标题必须！',self::MUST_VALIDATE),
        array('content','require','日志评论内容必须！',self::MUST_VALIDATE),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取日志评论状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }
    
    //验证日志分类是否必选
    public static function checkCategoryId($category_id){
        $match = '/^[1-9]\d*$/';
        $v = trim($category_id);
        if (empty($v)) {
            return false;
        }
        $matches = preg_match($match, $v);
        if(empty($matches)){
            return false;
        }
        return true;
    }
    
}

?>