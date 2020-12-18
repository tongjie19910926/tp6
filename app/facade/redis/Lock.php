<?php


namespace app\facade\redis;

use think\Facade;


/**
 * @see \app\common\redis\Lock
 * @package think\facade
 * @mixin \app\common\redis\Lock
 * @method static Lock lock($scene,$callback) 并发锁
 */
class Lock extends Facade
{


    protected static function getFacadeClass()
    {
        return 'app\common\redis\Lock';
    }
}