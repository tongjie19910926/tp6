<?php


namespace app\admin\controller;


class Power extends \app\common\controller\Admin
{


    /**
     * 树形结构的数据
     * @param \app\common\model\Route $route
     * @param \app\common\model\Power $power
     * @param int                    $role_id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function newTree(\app\common\model\Route $route, \app\common\model\Power $power, $role_id = 0)
    {
        $admin_role_id = self::getAdminSession('AdminRole');
        if (in_array(1,$admin_role_id)) {
            $data = $route->field('id, title label, pid')->select()->toArray();
        } else {
            $data = $route->alias('r')
                ->join('power p', 'p.route_id = r.id ')
                ->whereIn('p.role_id',$admin_role_id)
                ->field('r.id, r.title label, r.pid')
                ->select()
                ->toArray();
        }
        $havePower = $power->havePower($role_id);
        $data = \sdModule\common\Infinite::init()
            ->setCall(function ($value) use ($havePower) {
                $value['checked'] = empty($value['children']) && in_array($value['id'], $havePower);
                in_array($value['id'], $havePower) and $value['spread'] = true;
                return $value;
            },true)->handle($data);
        return self::apiJson($data);
    }







    /**
     * 权限设置
     * @param \app\common\model\Power $power
     * @param int                    $role_id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setHandle(\app\common\model\Power $power, $role_id = 0)
    {
        $data = $this->request->post('set');
       // pr(\app\common\model\Admin::check($role_id),1);
        switch (true) {
            case empty($role_id) :
                return self::apiJson('请选择角色',404);
            case !\app\common\model\Admin::check($role_id) :
                return self::apiJson('你的权限不够',404);
        }
        //$newData = \sdModule\common\Infinite::init()->reveres($data);
        $route_id = $data ? array_flip(array_flip(array_column(\sdModule\common\Infinite::init()->reveres($data), 'id'))) :[];
        $admin_role_id = self::getAdminSession('AdminRole');
        if (!in_array(1,$admin_role_id)) {
            $admin_role = $power->whereIn('role_id', $admin_role_id)
                ->column('route_id');
            if (array_diff($route_id??[], $admin_role)) return self::apiJson('权限不够',404);
        }
        $powerData = [];
        foreach ($route_id as $datum) {
            $powerData[] = [
                'route_id' => $datum,
                'role_id' => $role_id
            ];
        }
        $result = $power->setPower($powerData, $role_id);
        return self::apiJson($result ?: '设置失败，请重试！',$result ? 200 : 404);
    }


}