<?php


namespace app\common\controller;


use sdModule\layuiSearch\Form;
use app\common\controller\DataWrite;
use think\facade\Session;
use think\helper\Arr;

class Admin extends  App
{
    use DataWrite;
    protected $limit = 20;//默认每条数据

    protected $middleware = [
        \app\middleware\CheckSession::class,
    ];

    /**
     * 获取管理员session的值
     * @param null $key 等于 null 时取出全部
     * @return mixed
     */
    public static function getAdminSession($key = null)
    {
      return  \app\common\model\Admin::getAdminSession($key);
    }


    /**
     * 添加
     * @return \think\response\Json
     */
    public function addHandle(){
        return $this->dataHandle();
    }

    /**
     * 修改
     * @return \think\response\Json
     */
    public function editHandle(){
        return $this->dataHandle('edit');
    }


    /**
     * 修改数据某个值
     * @return \think\response\Json
     */
    public function upda(){
        $post = $this->filter($this->request->post());
        $data = model($this->request->controller())
            ->where(['id'=>$post['id']])
            ->update(Arr::except($post,['id']));
        return $data ? self::apiJson('成功',200) :  self::apiJson('失败',404);
    }


    /**
     * layui 文件上传
     * @return string|string[]|\think\response\Json
     */
    public function upload(){

        try {
            return json(['code'=>0,'msg'=>'上传成功','data'=>['src'=>parent::upload()]]);
        }catch (\Exception $e){
            return   json(['code'=>1,'msg'=>'上传失败','data'=>[]]);
        }
    }



    /**
     * @param $data  paginate分页
     * @return \think\response\Json
     */
    protected static function json($data){
        return json(['code'=>0,'msg'=>'成功','count'=>$data['total']??0,'data'=>$data['data']??$data]);
    }


    public function __call($method,$vars) {
        switch($method){
            case 'index':
                if (method_exists($this, 'setSearchForm')) {
                    $assign['search'] = Form::CreateHTML($this->setSearchForm());
                }
               break;
            case 'add':
                if(in_array($this->primary,array_keys($vars))){//如果带id 就是修改
                    $data = model($this->request->controller())
                        ->field(true)
                        ->where($this->primary, $vars[$this->primary])
                        ->findOrEmpty()
                        ->toArray();

                    $assign['data'] = $data;
                }
                break;
        }
        return $this->fetch($method,$assign ?? []);
    }



}