<?php


namespace app\common\validate;


class Admin extends \think\Validate
{

    protected $rule =   [
        'name|昵称'  => 'require',
        'number|账号'   => '',
        'password' => 'require|length:6,16',
        'password_j' => 'require|length:6,16',
        'password_rm' => 'require|confirm:password',
        'image|图片'=>'require',
        'phone|电话'=>'require|mobile',
        'email|邮箱'=>'require|email',
        'card|身份证'=>'require|idCard',
        'role|角色'=>'require|array',
    ];

    protected $scene = [
        'add'  =>  ['name','number','password','image','phone','email','card','role'],
        'edit'  =>  ['name','number','image','phone','email','card','id','role'],
        'defend'  =>  ['name','number','image','phone','email','card','id',],
        'pwd_edit'  => ['password_rm', 'password', 'password_j'],
    ];



    public function  __construct()
    {
        parent::__construct();
        $id =  $this->request->post('id',0);
        $this->rule['number|账号'] = 'require|unique:admin,number,'.$id .',id';
    }


}