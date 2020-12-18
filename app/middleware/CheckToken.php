<?php
declare (strict_types = 1);

namespace app\middleware;

use app\common\controller\App;
use app\common\model\User;
use thans\jwt\facade\JWTAuth;

class CheckToken
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
        try {
            $data = User::field(true)->findOrEmpty(JWTAuth::getPayload()['uid']->getValue())->toArray();
            if (empty($data)) return  App::apiJson('没有此用户'.JWTAuth::getPayload()['uid']->getValue(),404);
            $request->user = $data;
        }catch (\Exception $e){
            return  App::apiJson($e->getMessage(),404);
        }
        return $next($request);
    }




}
