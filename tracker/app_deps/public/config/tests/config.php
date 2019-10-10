<?php
error_reporting(-1);
// set to the user defined error handler
set_error_handler("myErrorHandler");
require dirname(__DIR__)."/autoload.php";
$id = StatsCenter::getModuleId("test_module_kkkkk");
var_dump($id);
var_dump($a['key']);

function myErrorHandler($errno, $errstr, $errfile, $errline)
{

    echo "$errno, $errstr \n";
    return true;
}