<?php
namespace app\common\redis;
use think\facade\Cache;
class Lock
{
        public  $redis;

        private  $lockId ;

        private $frequency = 5;  //尝试获取设置 封锁 5次

        private  $expired = 5;  //缓存过期时间 单位：秒

        public function __construct()
        {
            if( !extension_loaded('redis'))  throw new \think\exception\HttpException(404, 'redis没有安装');
           $this->redis =  Cache::store('redis')->handler();
        }


        /**  redis 悲观锁
         * @param $scene
         * @param $callback
         * @return mixed|void
         */
        public function  lock($scene,$callback){
            $frequency = $this->frequency;
            while ($frequency-- > 0){
                $value = session_create_id();
                $res = $this->redis->set($scene,$value,['NX','EX'=>$this->expired]);
                if($res) {
                  $data =  call_user_func_array($callback,[]);
                    $this->lockId[$scene] = $value;
                    break;
                }
                usleep(5000);
            }
            //解锁
            if (isset($this->lockId[$scene])){
                $id = $this->lockId[$scene];
                $value = $this->redis->get($scene);
                if($value === $id)  return $this->redis->del($scene) ?  $data :  abort(404,'系统繁忙');
                return abort(404,'系统繁忙');
            }
            return abort(404,'系统繁忙');
        }
}