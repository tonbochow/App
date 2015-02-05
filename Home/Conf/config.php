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
    'NOT_AUTH_MODULE' => 'Index,Public,Talk,Article,Album,Video,Message', // 默认无需认证模块(控制器)
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块(控制器)
    'NOT_AUTH_ACTION' => '', // 默认无需认证操作(方法)
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作(方法)
);
