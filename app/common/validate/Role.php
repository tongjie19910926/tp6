<?php


namespace app\common\validate;


class Role extends \think\Validate
{

    protected $rule =   [
        'role|角色名称'  => '',
        'id|id'=>'require|integer',
    ];

    protected $scene = [
        'add'  =>  ['role'],
        'edit'  =>  ['role','id'],
    ];

    public function  __construct()
    {
        parent::__construct();
        $id =  $this->request->post('id',0);
        $this->rule['role|角色名称'] = 'require|unique:role,role,'.$id .',id';
    }


}