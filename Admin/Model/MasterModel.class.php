<?php

namespace Admin\Model;

use Think\Model;

Class MasterModel extends CommonModel {

    protected $_validate = array(
        array('name', '', '角色名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        /* 验证手机号码 */
        array('mobile', 'checkMobile', '手机号码不正确', self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),
        array('mobile', '', '手机号码不正确!', self::EXISTS_VALIDATE, 'unique',self::MODEL_INSERT), //手机号被占用 
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );


    //性别下拉列表
    public static function getSex($sex = null) {
        $sex_arr = array(
            '' => '请选择',
            1 => '男',
            0 => '女'
        );
        if (!empty($sex)) {
            return $sex_arr[$sex];
        }
        return $sex_arr;
    }
    
    //验证手机号是否符合规则
    public static function checkMobile($mobile) {
        $match = '/^[(86)|0]?(13\d{9})|(15\d{9})|(17\d{9})|(18\d{9})$/';
        $v = trim($mobile);
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