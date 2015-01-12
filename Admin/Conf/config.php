<?php
return array(
//    'SESSION_PREFIX' => 'admin', // 后台session 前缀
    'USER_AUTH_GATEWAY' => '/Admin/Public/login', // 后台默认认证网关
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__COMMON__' => __ROOT__ . '/Public/Common',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),
    'PER_PAGE_NUM' => 20,//后台应用每页显示数量
);
