<?php
// + ========================================
// 创建文件的基础配置
// + ========================================

return [
//    网站根目录
    'root_path' => \think\facade\App::getRootPath(),
//    应用目录
    'app_path' => \think\facade\App::getAppPath(),
//  controller 文件夹名
    'controller_dir' => '', // 默认 controller， 文件相对于 app_path
//   该应用 model 文件夹名
    'model_dir' => '', // 默认 model， 文件相对于 app_path
//   该应用 validate 文件夹名
    'validate_dir' => '', // 默认 validate， 文件相对于 app_path
//    公用基类 model 文件夹
    'common_model_dir' =>  '', // 默认 common/model， 文件相对于应用文件夹
//    view 文件位置
    'view_dir' => '', // 默认 view/admin,  文件相对于 root_path
//    顶级命名空间
    'namespace' => '',  // 默认 app
//    对应应用名
    'app' => '', // 默认 admin
];




