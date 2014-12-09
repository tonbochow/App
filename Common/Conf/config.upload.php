<?php

return array(
    /* 后台用户头像图片上传配置 */
    'ADMIN_HEADIMG_UPLOAD' => array(
        'maxSize' => 5120000, //1M =1024000 (限制5M)
        'rootPath' => './upload',
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''),
        'exts' => array('jpg', 'jpeg', 'png', 'bmp', 'gif'),
        'autoSub' => false,
        'replace' => true, //存在同名是否覆盖
    ),
);
