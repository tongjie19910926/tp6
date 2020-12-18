<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\controller\Admin;
use app\common\model\Admin as Admins;
use app\common\controller\App;
use think\facade\Config;
use think\facade\Session;
use think\Request;

class CheckSession
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        switch (true) {
//            未登录
            case !Admin::getAdminSession('id'):
                return $this->isNotLogin($request);
//            验证单点登录
            case !Admins::verifySinglePoint():
                return $this->singlePoint($request);
//             运营状态不可操作的
//            case self::isOperate() && $this->operate($request):
//                return App::apiJson('运营状态， 操作无效！', 205);
//          登录后的权限跳过判定
            case in_array(1, Admin::getAdminSession('AdminRole')) :
//            case $this->haveJudge($request):
            case $this->except($request):
                goto skip;
                break;
//          登录后的权限判定
            case $this->permissionVerify($request):
                return $this->permissionDenied($request);
            default:
                goto skip;
        }
        skip:
      //  if (!in_array($request->controller(),['Log'])) $this->log($request);
        return $next($request);

    }


    /**
     * 未登录
     * @param Request $request
     * @return \think\response\Json|\think\response\Redirect|void
     */
    public function isNotLogin(Request $request)
    {
        if($request->isAjax()) return App::apiJson('请求地址不存在', 404);
        Session::flash('login','login');
        return redirect((string) url('admin/login/login'));
    }



    /**
     * 不用验证权限的请求地址
     * @param Request $request
     * @return bool
     */
    protected function except(Request $request)
    {
        $except = Config::get('route.except');
        if (empty($except[parse_name($request->controller())])) return false;
        return in_array($request->action(), $except[parse_name($request->controller())]);
    }


    /**
     * 强制下线
     * @param Request $request
     * @return \think\response\Json|\think\response\Redirect|void
     */
    public function singlePoint(Request $request)
    {
        Session::delete(\app\common\model\Admin::SESSION_KEY);
        $errTip = '账号在其他地方登录，你被强制下线！如不是本人操作，请尽快更改密码！';
        return $request->isAjax() ? app::apiJson($errTip, 404) : redirect((string) url('index/login', ['tip' => $errTip]));
    }



    /**
     * 权限不足
     * @param Request $request
     * @return \think\response\Json|\think\response\Redirect|void
     */
    public function permissionDenied(Request  $request)
    {
        return $request->isAjax() ? app::apiJson('权限不足！', 404) : redirect((string) url('index/go404'));
    }



    /**
     * 权限验证
     * @param Request $request
     * @return bool
     */
    public function permissionVerify(Request $request)
    {

        $PowerCache = Admins::getAdminPowerCache() ? :[];
        switch (true){
            case  !isset($PowerCache[parse_name($request->controller(),1)]):
                return  true;
            case in_array('*',$PowerCache[parse_name($request->controller(),1)]):
                return  false;
            case in_array($request->action(),$PowerCache[parse_name($request->controller(),1)]):
                return  false;
            default :
                return  true;
        }
    }

}
