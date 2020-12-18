<?php


namespace app\admin\controller;


use app\common\controller\Admin;
use app\common\model\Role as Roles;

class Role extends  Admin
{

    public function indexData(Roles $role){
        $data = $role->alias('r1')
            ->LeftJoin('role r2','r1.pid = r2.id')
            ->field([
                'r1.id',
                'r1.role',
                'r1.status',
                'r2.role'=>'p_role',
            ])
            ->append(['status_text'])
            ->order([
                'r1.id'=>'desc'
            ])
            ->paginate($this->request->get('limit',$this->limit))
            ->toArray();
        return self::json($data);
    }


    /**
     * 数据写入之前处理一些数据
     * @param $data
     */
   protected function  beforeWrite(&$data){
       $data['pid'] = self::getAdminSession('id');
   }

}