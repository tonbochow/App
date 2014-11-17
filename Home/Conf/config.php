<?php
return array(
//    'SESSION_PREFIX' => 'home', // 前台session 前缀
    'USER_AUTH_GATEWAY' => '/Home/public/login', // 前台默认认证网关
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__COMMON__' => __ROOT__ . '/Public/Common',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),
);
