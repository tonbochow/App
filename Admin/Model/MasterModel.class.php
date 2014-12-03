<?php

namespace Admin\Model;

use Think\Model;

Class MasterModel extends CommonModel {

    public $_validate = array(
        array('name', '', '角色名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    public $_auto = array(
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

}

?>