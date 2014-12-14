<?php
/**
 * 后台日志功能模型
 * @author 周东宝 2014-12
 */
namespace Admin\Model;

use Think\Model;

Class ArticleModel extends CommonModel {

    public static $AVAILABLE = 1;
    public static $UNAVAILABLE = 0;
    
    public static $COMMENT_ALLOW = 1;
    public static $COMMENT_DENY = 0;
    
    protected $_validate = array(
        array('title', 'require', '日志标题已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('title','require','日志标题必须！',self::MUST_VALIDATE),
        array('content','require','日志内容必须！',self::MUST_VALIDATE),
        array('category_id','require','日志所属分类必选！',self::MUST_VALIDATE,'',self::MODEL_BOTH),
        array('category_id', 'checkCategoryId', '日志分类为必选项', self::MUST_VALIDATE, 'callback',self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取日志状态
    public static function getStatus($status = null) {
        $status_arr = array('' => '请选择');
        $status_arr[self::$AVAILABLE] = '公开';
        $status_arr[self::$UNAVAILABLE] = '不公开';
        if ($status !== null) {
            return $status_arr[$status];
        }
        return $status_arr;
    }

    //获取日志是否允许评论
    public static function getCommentStatus($status = null){
        $status_arr = array('' => '请选择');
        $status_arr[self::$COMMENT_ALLOW] = '允许';
        $status_arr[self::$COMMENT_DENY] = '禁止';
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