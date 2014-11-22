<?php

namespace Admin\Model;

use Think\Model;

Class RoleModel extends CommonModel {

    public $_validate = array(
        array('name', '', '角色名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    public $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

}

?>