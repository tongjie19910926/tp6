<?php


namespace app\common\model;

use sdModule\layui\Tag;
use think\Model;
class App  extends Model
{


    const SEX = [
        0=>[
            'text'=>'未知',
            'color'=>'orange'
        ],
        1=>[
            'text'=>'男',
            'color'=>'black'
        ],
        2=>[
            'text'=>'女',
            'color'=>'red'
        ],
    ];

    public function getSexTextAttr($value,$data)
    {
        $position =   self::SEX[$data['sex']] ?? $data['sex'];
        $color = $position['color'] ?? '';
        return is_array($position) ?  Tag::init()->$color($position['text']) :  $position;
    }






    public static function onAfterWrite($user)
    {

    }



}