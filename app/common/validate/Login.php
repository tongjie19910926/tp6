<?php


namespace app\common\validate;


class Login extends \think\Validate
{

    protected $rule =   [
        'number|用户名'  => 'require',
        'password|密码'   => 'require|length:6,16',
        'vercode|验证码'=>'require|captcha'
    ];

    protected $scene = [
        'login'  =>  ['number','password'],
    ];

}