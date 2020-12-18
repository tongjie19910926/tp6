<?php


namespace app\admin\controller;


use app\common\controller\Admin;

use think\facade\Route;
use think\facade\Session;
use think\helper\Arr;

class Login  extends  Admin
{

    protected $middleware = [ //
        \app\middleware\CheckSession::class =>[
            'except' 	=> [
                'login',
                'loginAjax',
            ]
        ],
    ];



    /**
     * 登录逻辑
     * @return \think\response\Json
     */
    public function loginAjax(\app\common\model\Admin $admin){
        $verify = $this->verify($this->filter($this->request->post()),'login');
        if(is_string($verify)) return self::apiJson($verify,404);
        $data = $admin->login(Arr::except($verify,['vercode']));
        return $data === true ? self::apiJson((string)Route::buildUrl('/'),200): self::apiJson($data,404);
    }



    /**
     * 退出登录
     * @return \think\response\Redirect|void
     */
    public function loginOut()
    {

        \app\common\model\Admin::deleteAdminPowerCache();
        Session::delete(\app\common\model\Admin::SESSION_KEY);

        return redirect((string) url('login'));
    }


}