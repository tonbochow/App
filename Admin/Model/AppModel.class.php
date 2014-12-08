<?php

namespace Admin\Model;

use Think\Model;

class AppModel extends CommonModel {

    protected $_validate = array(
        array('url', '', '应用url已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

}

?>