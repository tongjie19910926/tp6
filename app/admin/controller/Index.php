<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\controller\Admin;
use app\common\model\Config;
use app\common\model\Route;
use app\facade\redis\Lock;
use sdModule\common\Infinite;
use think\facade\View;



class Index extends  Admin
{


    public function index(Route $route){
        $data = $route->getMenu();

        return $this->fetch('', [
            'left' => Infinite::init()->handle($data, ['type' => \app\common\model\Route::LEFT_FIRST_MENU]),
            'top' => Infinite::init()->handle($data, ['type' => Route::TOP_FIRST_MENU]),
            'user' => self::getAdminSession(),
            'config'=>Config::info()
        ]);
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function system(){
        return $this->fetch('',[
            'list'=>Config::DEPLOY,
            'data'=>Config::info(),
        ]);
    }





}
