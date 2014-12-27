<?php
/**
 * 后台配置功能模型
 * @author 周东宝 2014-12-27
 */
namespace Admin\Model;

use Think\Model;

Class ConfigModel extends CommonModel {
    
    protected $_validate = array(
        array('name','require','配置名必须！',self::MUST_VALIDATE),
        array('value','require','配置值必须！',self::MUST_VALIDATE),
        array('name', '', '配置名已经存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function'),
    );

    //获取配置值
    public static function getConfigValue($name) {
        $configModel = M('Config');
        $config = $configModel->where(array('name'=>$name))->find();
        return $config['value'];
    }
    
}

?>