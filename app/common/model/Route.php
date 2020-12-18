<?php


namespace app\common\model;


use sdModule\common\Infinite;
use sdModule\layui\Tag;

class Route extends App
{

    const TOP_FIRST_MENU = 1;
    const TOP_SECOND_MENU = 2;
    const LEFT_FIRST_MENU = 3;
    const LEFT_SECOND_MENU = 4;
    const ACTION_MENU = 5;

    //super
    const SUPER = [
        0=>[
            'text'=>'不可见',
            'color'=>'orange'
        ],
        1=>[
            'text'=>'可见',
            'color'=>'black'
        ],
    ];


    public function getSuperTextAttr($value,$data)
    {
        $position =   self::SUPER[$data['super']] ?? $data['super'];
        $color = $position['color'] ?? '';
        return is_array($position) ?  Tag::init()->$color($position['text']) :  $position;
    }




    /**
     * 获取用户的权限路由地址
     * @param $admin_id
     * @return array
     */
    public function getAdminRoute($admin_id)
    {
           $data = self::alias('r')
                ->LeftJoin('power p','r.id = p.route_id')
                ->LeftJoin('role rl','rl.id = p.role_id')
                ->LeftJoin('admin_role ar','ar.role_id = rl.id')
                ->where(['ar.admin_id'=>$admin_id])
               ->where('r.route','<>','')
                ->field([
                    'r.id',
                    'r.route',
                    'r.auth',
                ])
                ->select()
                ->toArray();

           $list = [];
           foreach ($data as $index => $value){

               $value['auth'] = $value['auth'] ? json_decode($value['auth'],true) : [];
               $value['auth'][] = parse_name($value['route'],1);
               foreach ($value['auth'] as $in => $va){
                    $ex = explode('/',$va);
                  isset($ex[1]) and  $list[$ex[0]][] = $ex[1];
               }
           }
           return  $list;
    }











    /**
     * 获取对应角色的权限
     * @param int $role_id 角色id
     * @return array
     */
    public function getMenu(){
        if (in_array(1,Admin::getAdminSession('AdminRole'))) return  $this->superMenu();
       // pr(Admin::getAdminSession('AdminRole'));
        return $this
            ->alias('r')
            ->LeftJoin('power p', 'p.route_id = r.id ')
            ->where('r.type', '<>', self::ACTION_MENU)
            ->whereIn('p.role_id', Admin::getAdminSession('AdminRole'))
            ->field([
                'r.id',
                'r.route',
                'r.title',
                'r.type',
                'r.pid',
                'r.icon',
                'p.role_id',
            ])
            ->order([
                'weigh'=>'desc',
                'id'=>'asc',
            ])
            ->select()
            ->toArray();
    }


    /**
     * 超级权限
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function superMenu()
    {
        return $this->alias('r')
            ->where('type', '<>', self::ACTION_MENU)
            ->where(['super'=>1])
            ->order([
                'weigh'=>'desc',
                'id'=>'asc',
            ])
            ->select()
            ->toArray();
    }




    /**
     * 菜单一级查询
     * @return array|string
     */
    public function firstMenu()
    {
        try {
            $data = $this->where(['pid' => 0])->field('id, title, type')->select();
            $all = $this->where('type', '<>', self::ACTION_MENU)->column('id, title, pid');
            $all = Infinite::init()->setInherit('title')->handle($all, 0, true);

            foreach ($all as $key => $item) {

                $all[$key]['title'] = implode(' / ', $item['inherit']);
            }
            $top = $left = [];

            foreach ($data as $value) {
                $value['type'] == self::TOP_FIRST_MENU and $top[] = $value;
                $value['type'] == self::LEFT_FIRST_MENU and $left[] = $value;
            }
            return [$top, $left, $all];
        } catch (\Exception $exception) {
            return  $exception->getMessage();
        }
    }



}