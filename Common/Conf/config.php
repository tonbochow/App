<?php

return array(
    'WEB_SITE_TITLE'=>'baby',
    'URL_MODEL' => 2,
    'DB_TYPE' => 'pdo',
    'DB_USER' => 'root',
    'DB_PWD' => 'root',
    'DB_PORT' => '3306',
    'DB_DSN' => 'mysql:host=localhost;dbname=baby;charset=utf8',
    'DB_PREFIX' => '',
    'SESSION_AUTO_START' => true,//默认开启session
    'SHOW_PAGE_TRACE' => true,//显示调试信息
    'USER_AUTH_ON' => true, //开启认证
    'USER_AUTH_TYPE' => 2, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    'ADMIN_AUTH_KEY' => 'administrator',
    'USER_AUTH_MODEL' => 'User', // 默认验证数据表模型
    'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式
//    'USER_AUTH_GATEWAY' => '/Public/login', // 默认认证网关 在前后台配置文件分别设置
    'NOT_AUTH_MODULE' => 'Index,Public', // 默认无需认证模块(控制器)
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块(控制器)
    'NOT_AUTH_ACTION' => '', // 默认无需认证操作(方法)
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作(方法)
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    'RBAC_ROLE_TABLE' => 'role',
    'RBAC_USER_TABLE' => 'role_user',
    'RBAC_ACCESS_TABLE' => 'access',
    'RBAC_NODE_TABLE' => 'node',
    'MODULE_ALLOW_LIST' => array('Home', 'Admin'), //设置访问列表
    'URL_CASE_INSENSITIVE' =>true,//url不区分大小写
    
    'DEFAULT_THEME' => 'default',//模版主题
    'ROOT_PATH' => $_SERVER['DOCUMENT_ROOT'],
    //加载其他配置文件　
    'LOAD_EXT_CONFIG' => 'config.upload,config.system,config.master', //加载扩展配置文件
);
