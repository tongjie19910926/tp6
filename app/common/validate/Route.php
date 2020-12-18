<?php


namespace app\common\validate;


class Route extends \think\Validate
{
    protected $rule =   [
        'pid|父级菜单'  => 'requireCallback:check_pid',
        'type|权限类型'   => 'require|integer',
        'title|名称'=>'require',
        'super|名称'=>'require|integer',
        'icon|图标'=>'requireIf:type,3',
        'weigh|权重'=>'require|integer',
        'controller|渲染控制器'=>'requireCallback:check_pid',
        'method|渲染方法'=>'requireCallback:check_pid',
        'auth|操作权限'=>'requireCallback:check_pid',
        'id|id'=>'require|integer',
    ];

    protected $scene = [
        'add'  =>  ['pid','type','title','icon','weigh','controller','method','auth','super'],
        'edit'  =>  ['pid','type','title','icon','weigh','controller','method','auth','super','id'],
    ];

    public function  __construct()
    {
        parent::__construct();
        $id =  $this->request->post('id',0);
        $this->rule['title|标题'] = 'require|unique:route,title^pid,'.$id .',id';
    }



    function check_pid($value, $data){
        return in_array($data['type'],[4]);
    }


}