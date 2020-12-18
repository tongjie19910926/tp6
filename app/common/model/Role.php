<?php


namespace app\common\model;


use sdModule\layui\Tag;

class Role extends App
{
    //status
    const STATUS = [

        1=>[
            'text'=>'正常',
            'color'=>'black'
        ],
        2=>[
            'text'=>'封停',
            'color'=>'red'
        ],
    ];

    public function getStatusTextAttr($value,$data)
    {
        $position =   self::STATUS[$data['status']] ?? $data['status'];
        $color = $position['color'] ?? '';
        return is_array($position) ?  Tag::init()->$color($position['text']) :  $position;
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function lists(){
       return self::where(['status'=>1])->field(['id','role'])->select()->toArray();
    }

  //  protected function



}