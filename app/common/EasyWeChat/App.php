<?php
namespace app\common\EasyWeChat;
use EasyWeChat\Factory;
use think\helper\Str;
use think\facade\Config;
class App
{
    const  Default = 'default';  //默认使用
    const   MOUNT = false;  //tp 框架是否支持 EasyWeChat SDK
    public  $factory;  //实例
    public  $config;  //配置数据
    public function  __construct($default = self::Default){
        $class = explode('\\',static::class);
        $pop = array_pop($class);
        $this->config= array_merge(config::get('wechat.'.Str::snake($pop , $delimiter  =  '_').'.'.$default),config::get('wechat.default'));  //配置
        $pop = lcfirst($pop);  //首字母小写
        $this->factory = self::MOUNT ? Factory::$pop() : Factory::$pop($this->config);  // 实例化
    }


    public function config(){
        return $this->config;
    }


}