<?php


namespace app\common\model;


use think\facade\Cache;
use think\facade\Request;

class Config extends App
{


    const DEPLOY = [
        'system'=>'网站设置',
    //    'api'=>'系统设置',
        'graphic'=>'图文'
    ];




    /**
     * 获取网站配置
     * @param string $group
     * @param string $name
     * @throws \Throwable
     */
    public static function  info(string $group = '',string $name = ''){
        $data = Cache::remember('Config', function(Request $request){
            $data = self::field(true)->select()->toArray();
            $list = [];
            foreach ($data as $index => $item){
                $list[$item['group']][$item['name']] = $item['value'];
            }
            return $list;
        });
        switch (true){
            case  $group && $name:
                return $data[$group][$name]??'';
            case  $group  && empty($name):
                return $data[$group]??'';
            case  $name  && empty($group):
                throw new \Exception('必须传第一个参数');
            default :
                return  $data;
        }
    }

}