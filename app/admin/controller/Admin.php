<?php


namespace app\admin\controller;

use app\common\model\Admin as Admins;
use app\common\model\AdminRole;
use sdModule\layuiSearch\SearchForm;
use think\facade\View;
use think\facade\Db;
use think\helper\Arr;

class Admin extends \app\common\controller\Admin
{

    /**
     * @param Admins $admin
     * @return \think\response\Json
     * @throws \think\db\exception\DbException
     */
    public function indexData(Admins $admin){
        $data = $admin->where([
            'delete_time'=>0
        ])
         ->field([
            'id',
            'name',
            'number',
            'image',
            'lately',
            'phone',
            'email',
            'card',
            'status',
         ])
          ->paginate($this->request->get('limit',$this->limit))
          ->toArray();
        return self::json($data);
    }


    /**
     * 添加页面
     * @param Admins $admin
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function add(Admins $admin,int $id = 0){
        if ($id){
           $data =  $admin->field([
                   'id',
                   'name',
                   'number',
                   'image',
                   'phone',
                   'email',
                   'card',
               ])
               ->with(['AdminRole'])
               ->findOrEmpty($id)
               ->toArray();
           $data and  $data['AdminRole'] = array_column($data['AdminRole'],'role_id');
            View::assign('data',$data);
       }
        return $this->fetch('',[
            'role'=>\app\common\model\Role::lists()
        ]);
    }

    /**
     * 添加
     * @param $data
     * @return bool|string
     */
    protected  function  customAdd($data){
        Db::startTrans();
        try {
            $result = Admins::create(Arr::except($data,['role']));
            $lst = $result->AdminRole()->saveAll($data['role']);
            Db::commit();
            return $lst ? true : '失败';
        }catch (\Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @param $data
     * @return bool|string
     */
    protected  function  customEdit($data){
        Db::startTrans();
        try {
           Admins::update(Arr::except($data,['role']));
           AdminRole::where(['admin_id'=>$data['id']])->delete();
           $role = [];
           foreach ($data['role'] as $value){
               $role[] = [
                    'admin_id'=>$data['id'],
                   'role_id'=>$value['role_id']
               ];
           }
           $result = (new AdminRole)->saveAll($role);
            Db::commit();
            return $result ? true : '失败';
        }catch (\Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @param Admins $admin
     * @return string
     * @throws \Exception
     */
    public function defend(Admins  $admin){
        $data =  $admin->field([
            'id',
            'name',
            'number',
            'image',
            'phone',
            'email',
            'card',
        ])
            ->with(['AdminRole'])
            ->findOrEmpty(self::getAdminSession('id'))
            ->toArray();
        $data and  $data['AdminRole'] = array_column($data['AdminRole'],'role_id');
        return $this->fetch('',[
            'data'=>$data
        ]);
    }


    public function defendHandle(){
        $post =  $this->verify($this->filter($this->request->post()),'defend');
        if (is_string($post)) return self::apiJson($post,404);
        return  Admins::update($post) ? self::apiJson('成功') :  self::apiJson('失败',404);
    }



    /**
     * 修改密码
     * @param \app\admin\model\Admin $admin
     * @return string
     * @throws \Exception
     */
    public function passwordEdit(Admins $admin)
    {
        if ($this->request->isPost()) {
            $data = $this->verify($this->request->post(),'pwd_edit');
            if(is_string($data)) return self::apiJson($data,404);
            $admin->startTrans();
            try {
                $password = $admin->where('id', self::getAdminSession('id'))->value('password');
                if (!$admin::passwordHandle($data['password_j'], $password)) return self::apiJson('旧密码不对，请重试！',404);
                $result =$admin::update(['password'=>$data['password']],['id'=> self::getAdminSession('id')]);
                $admin->commit();
            } catch (\Exception $exception) {
                $admin->rollback();
                return self::apiJson($exception->getMessage() ?: '修改失败',404);
            }
            return self::apiJson($result ? '' : '修改失败！',$result ? 200 :404);
        }
        return $this->fetch();
    }


    /**
     * 设置搜索框
     * @return array
     */
    public function setSearchForm()
    {
        return  [];
        return [
            SearchForm::Text('name','姓名')->html(),
            SearchForm::Text('number','登录账号')->html(),
            SearchForm::Text('phone','手机')->html(),
            SearchForm::Text('email','邮箱')->html(),
            SearchForm::Text('card','身份证')->html(),
        ];

    }

}