<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



// [ 应用入口文件 ]
namespace think;

//ini_set('display_errors',1);            //错误信息
//ini_set('display_startup_errors',1);    //php启动错误信息
//error_reporting(-1);                    //打印出所有的 错误信息
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); //将出错信息输出到一个文本文件


require __DIR__ . '/../vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
