<?php
const DS = DIRECTORY_SEPARATOR; //目录分隔符

// 应用公共文件
if (!function_exists('pr')) {
    function pr($var,$exit='') {
        if (\think\facade\Env::get('APP_DEBUG')) {
            $template = PHP_SAPI !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
            printf($template, print_r($var, true));
            if(!empty($exit))   exit($exit);
        }
    }
}


// 应用公共文件
if (!function_exists('Getsession')) {
    function Getsession($data = '') {
        return  \app\common\model\User::getAdminSession($data);
    }
}

// 应用公共文件
if (!function_exists('model')) {
    function model($model) {
        return  app('\app\common\model\\'.parse_name($model,1));
    }
}


/**
 * 生成订单
 */
if (!function_exists('create_order')) {
    function create_order() {
        return   date('Ymd').substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999));
    }
}
