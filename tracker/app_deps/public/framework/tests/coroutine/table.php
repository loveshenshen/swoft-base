<?php
define('DEBUG', 'on');
define('WEBPATH', realpath(__DIR__ . '/../../'));
//包含框架入口文件
require WEBPATH . '/libs/lib_config.php';

Swoole\Core::$enableCoroutine = true;
Swoole::go(function () {
    $res = table('userinfo')->gets(['limit' => 5]);
    var_dump($res);
});
