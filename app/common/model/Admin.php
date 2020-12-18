<?php


namespace app\common\model;


use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Session;
use think\helper\Arr;

class Admin extends  App
{
    //SESSION   名称
    const SESSION_KEY = 'admin_user';

    //缓存
    const CACHE_KEY_PREFIX = 'admin_user';

    /** @var int 登录允许错误次数 */
    const ERROR_NUMBER = 10;

    const  STATUS =  [  //状态
        'enable'=>1,//正常
        'disable'=>0,//禁用
    ];

    /** @var string 密码前缀 */
    const PREFIX = 'AAA-LP-SD';


    public function AdminRole()
    {
        return $this->hasMany(AdminRole::class);
    }









    //=====================================登录-Start==============================================
    /**
     * @param $login
     */
    public function login($login) {
        $user = $this->LoginData($login['number']);
        switch(true)  {
            case Env::get('APP_DEBUG') && is_string($user):
                return $user;
            case !$user :
                return '账户不存在或密码错误';
            case ($errorLimit = $this->errorLimit($user, $login)) !== true:
                return $errorLimit;
            case $user['status'] == self::STATUS['disable']:
                return '该账号被冻结，请联系相关管理人员。';
        }
        $this->startTrans();
        try {
            self::update([
                'error_number' => 0,
                'lately' => date('Y-m-d H:i:s')
            ],[
                'id'=>$user['id']
            ]);
            $route = new Route();
            self::setAdminSession(Arr::except($user,['password'])); //用户信息存入session
            self::setAdminPowerCache($route->getAdminRoute($user['id']));//设置权限缓存
            self::setSinglePoint();// 设置单点登录
          //   $route->getAllRoute();
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollback();
            return $exception->getMessage();
        }
        return true;
    }


    /**
     * 用户登录错误限制
     * @param $user
     * @param $data
     * @return bool|string
     */
    private function errorLimit($user, $data)
    {
        if(self::passwordHandle($data['password'], $user['password'])) return  true;//密码正确时
        $error['error_time'] = time();
        $error['error_number'] = ($user['error_number'] ?: 0) + 1;
        try {
            $this->where('id', $user['id'])->update($error);
        } catch (\Exception $exception) {
            return '账户不存在或密码错误';
        }
        return '账户不存在或密码错误';
    }



    /**
     * 查询用户
     * @param $number
     */
    private  function  LoginData($number) {
        try {
           $data =  $this->alias('a')
                ->join('admin_role ar','ar.admin_id = a.id')
                ->where(['a.number'=>$number])
                ->with([
                    'AdminRole'=>function($query){
                        $query->field(true);
                    }
                ])
                ->field([
                    'a.id',
                    'a.name',
                    'a.number',
                    'a.password',
                    'ar.role_id',
                    'a.status',
                    'a.error_number',
                    'a.error_time',
                ])
                ->findOrEmpty()
                ->toArray();
            $data and  $data['AdminRole'] =  array_column($data['AdminRole'],'role_id');
            return  $data;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     *
     *
     * 密码加密、验证
     * @param        $password  密码
     * @param string $verify   加密的密码  为空时将第一个参数进行加密 不为空时验证密码是否正确
     * @return string
     */
    public static function passwordHandle($password, $verify = '')
    {
        $password = self::PREFIX . md5(self::PREFIX . substr($password, -3) . substr($password, 0, -3));
        return $verify ? password_verify($password, $verify) : password_hash($password, PASSWORD_DEFAULT);
    }

//=====================================登录--end==============================================



    /**
     * 检验非超管操作权限
     * @param int  $id     角色id
     * @return bool
     */
    public static function check($id)
    {
        $admin_role_id = self::getAdminSession('AdminRole');
        if (in_array(1,$admin_role_id)) return true;
        if (in_array($id,$admin_role_id)) return true;
        $admin_id = Role::where(['id' => $id])->value('pid');
        return $admin_id == self::getAdminSession('id');
    }




    public function setPasswordAttr($value)
    {
        return self::passwordHandle($value);
    }





    /*=====================session=======================*/
    /**
     * 设置管理员session的值
     * @param $value
     */
    public static function setAdminSession($value) {
        Session::set(self::SESSION_KEY, $value);
    }


    /**
     * 获取管理员session的值
     * @param null $key 等于 null 时取出全部
     * @return mixed
     */
    public static function getAdminSession($key = null)
    {
        return is_null($key) ? Session::get(self::SESSION_KEY) : Session::get(implode('.', [self::SESSION_KEY, $key]));
    }

    //=============================权限缓存==============================
    /**
     * 设置管理员的权限缓存
     * @param $power
     * @return bool|mixed
     */
    public static function setAdminPowerCache($power)
    {
        return Cache::set(self::CACHE_KEY_PREFIX . '_' .self::getAdminSession('id'), $power, 86400);
    }



    /**
     * 获取管理员的权限缓存
     * @return mixed
     */
    public static function getAdminPowerCache()
    {
        return Cache::get(self::CACHE_KEY_PREFIX . '_'.self::getAdminSession('id'));
    }

    /**
     * 删除管理员的权限缓存
     */
    public static function deleteAdminPowerCache()
    {
        Cache::delete(self::CACHE_KEY_PREFIX . '_'.self::getAdminSession('id'));
    }

    /*=====================单点登陆=======================*/
    /**
     * 设置单点登录
     */
    public static function setSinglePoint()
    {
        Cache::set(self::CACHE_KEY_PREFIX . '_' . self::SESSION_KEY . '_' . self::getAdminSession('id'), Session::getId(), Config::get('session.expire'));
    }

    /**
     * 验证单点登录
     * @return bool
     */
    public static function verifySinglePoint()
    {
        $singPoint = Cache::get(self::CACHE_KEY_PREFIX . '_' . self::SESSION_KEY . '_' .self::getAdminSession('id'));
        return !$singPoint || $singPoint == Session::getId();
    }




}