<?php
/**
 * 配置文件
 *
 * @author 耐小心<i@naixiaoxin.com>
 * @copyright 2017-2018 耐小心
 */
use think\facade\Env;
return [
    /*
      * 默认配置，将会合并到各模块中
      */
    'default'         => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/object/raw/自定义类名
         */
        'response_type' => 'array',
        /*
         * 使用 ThinkPHP 的缓存系统
         */
        'use_tp_cache'  => true,
        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log'           => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'permission' => 0777,
            'file' => env('WECHAT_LOG_FILE', app()->getRuntimePath()."log".DIRECTORY_SEPARATOR."wechat.log"),
        ],
    ],

    //公众号
    'official_account' => [
        'default' => [
            // AppID
            'app_id' =>'', //wx448256802fdb41c0
            // AppSecret
            'secret' =>'',  //bc626514b693ca531759562616e9b9cc
            // Token
            'token' =>'',
            // EncodingAESKey
            'aes_key' => '',
            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
//            'oauth' => [
//                'scopes'   => array_map('trim',
//                    explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
//                'callback' => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
//            ],
        ],
    ],

    //第三方开发平台
    'open_platform'    => [
        'default' => [
            'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
            'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
            'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
            'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
        ],
    ],

   // 小程序
    'mini_program'     => [
        'default' => [
            'app_id'  =>Env::get('WECHAT.WECHAT_MINI_PROGRAM_APPID', ''),
            'secret'  =>Env::get('WECHAT.WECHAT_MINI_PROGRAM_SECRET', ''),
            'token'   =>Env::get('WECHAT.WECHAT_MINI_PROGRAM_TOKEN', ''),
            'aes_key' =>Env::get('WECHAT.WECHAT_MINI_PROGRAM_AES_KEY', ''),
        ],
    ],

//    支付
    'payment'          => [
        'default' => [
            'sandbox'    => Env::get('WECHAT.WECHAT_PAYMENT_SANDBOX', false),
            'app_id'     =>Env::get('WECHAT.WECHAT_MINI_PROGRAM_APPID', ''),
            'mch_id'     => '1603308819',
            'key'        => '1531605228cdb94998f66593a8d3gkpt',
            'cert_path'  => app()->getRootPath().'cert'. DS.'apiclient_cert.pem',    // XXX: 绝对路径！！！！
            'key_path'   => app()->getRootPath().'cert'.DS.'apiclient_key.pem',     // XXX: 绝对路径！！！！
            'notify_url' => '',                           // 默认支付结果通知地址
            'rsa_public_key_path' => '', // <<<------------------------
        ],
        // ...
    ],

    //企业微信
    'work'             => [
        'default' => [
            'corp_id'  => 'xxxxxxxxxxxxxxxxx',
            'agent_id' => 100020,
            'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
            //...
        ],
    ],
];
