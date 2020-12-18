<?php


namespace app\admin\controller;

use app\common\model\Route as Routes;
use think\facade\Env;
use think\facade\View;
use think\helper\Arr;

class Route extends \app\common\controller\Admin
{




    /**
     * 数据
     * @param \app\common\model\Route $route
     * @return \think\response\Json
     */
    public function indexData(Routes $route)
    {
        $data = $route->alias('r')
            ->leftJoin('route r2','r2.id = r.pid')
            ->append(['super_text'])
            ->field([
                'r.id',
                'r.title',
                'r.type',
                'r.route',
                'r.pid',
                'r.weigh',
                'r.super',
                'r2.title'=>'parent',
            ])
            ->order(['r.weigh'=>$this->request->get('weigh','desc')])
            ->select()
            ->toArray();
        return self::json($data);
    }



    public function add(Routes $route,int $id = 0){
        if ($id){
            $data = Routes::where(['id'=>$id])->field(true)->findOrEmpty()->toArray();
            $data['route'] and  list($data['controller'],$data['method']) = explode('/',$data['route']);
            $data['auth'] = implode(',',array_filter(json_decode($data['auth'],true)?:[]));
            /**
             * 处理父级id
             */
            switch ($data['type']){
                case 4:
                    $pid = Routes::where(['type'=>3])->field(['title','id'])->select()->toArray();
                    View::assign('pid',$pid);
                    break;
                case 2:
                    $pid = Routes::where(['type'=>1])->field(['title','id'])->select()->toArray();
                    View::assign('pid',$pid);
                    break;
            }
            /**
             * 处理渲染方法
             */
            if ($data['route'] &&  $data['method']){
                $method = $this->meth($data['controller']);
                View::assign('method',$method);
            }
            View::assign('data',$data);
        }
        return $this->fetch('',[
            'controller'=>$this->dir()
        ]);
    }






    /**
     * 获取目录
     * @param \app\common\model\Route $route
     * @return \think\response\Json
     */
    public function firstMenu(\app\common\model\Route $route){
        $menu = $route->firstMenu();
        if (is_string($menu)) return self::apiJson($menu,404);//返回错误信息
        list($topFirst, $leftFirst, $all) = $menu;
        return self::apiJson(['topFirst' => $topFirst, 'leftFirst' => $leftFirst, 'all' => $all]);
    }



    /**
     * 数据写入之前处理一些数据
     * @param $data
     */
    protected function  beforeWrite(&$data){
        isset($data['controller']) and  isset($data['method']) and $data['method']  and $data['controller']  and $data['route'] = strtolower($data['controller']).'/'.$data['method'];
        isset($data['auth']) and $data['auth'] = json_encode(explode(',',$data['auth']),JSON_UNESCAPED_UNICODE);
    }


    /**
     * 添加
     * customAdd
     * @param $data
     */
    protected function customAdd($data){

        return  Routes::create(Arr::only($data,['type','title','icon','weigh','auth','route','pid','super'])) ? true : '失败';
    }

    /**
     * 修改
     * customEdit
     * @param $data
     */
    protected function customEdit($data){
        return  Routes::update(Arr::only($data,['type','title','icon','weigh','auth','route','pid','super','id'])) ? true : '失败';
    }

    /**
     *
     */
    protected function delete($id){
       if (!Env::get('APP_DEBUG')) return  '运营状态不可操作';
       return Routes::whereOr(['id'=>$id,'pid'=>$id])->delete() ? true :'失败';
    }








    /**
     * 获取当前目录下 所有php文件名字
     * @return array
     */
    private function dir(){
        $list = [];
        foreach (scandir( dirname(__FILE__)) as $value){
            if (stristr($value,'php')){
                $list[] = explode('.php',$value)[0];
            }
        }
        return $list;
    }



    /**
     * 获取控制中的方法
     * @param $controller
     */
    private function meth($controller){
        $data =  array_diff(get_class_methods(__NAMESPACE__.'\\'.parse_name($controller,1))?:[],[
            'beforeWrite',
            'verification',
            'dataHandle',
            'validate',
            '__construct',
            'fetch',
            'verify',
            'update',
            'insert',
            'filter',
            'apiJson',
            'initialize',
            '__call',
            'json',
            'upload',
            'getAdminSession',
            'dir',
        ]);
        $data  and  !in_array('add',$data) and $data[] = 'add';
        $data  and  !in_array('index',$data) and $data[] = 'index';
        $data  and   array_unshift($data,'*');
        return $data;
    }








    /**
     * 获取控制中的方法
     * @param $controller
     */
    public function methods($controller,int $id =0){
        $data  =  $this->meth($controller);
        if ($id){
            $au = Routes::where(['id'=>$id])->field(['auth'])->value('auth');
             $au and  $auth  = json_decode($au,true);
        }
        $list = [];
        foreach ($data as $value){
            $list[] = [
              'name'=>$value,
                'value'=> $value,
                'selected'=> isset($auth) && in_array(parse_name($controller,1).'/'.$value,$auth)
            ];
        }
       return self::apiJson($list,200);
    }






}