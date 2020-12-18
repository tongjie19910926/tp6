<?php


namespace app\common\controller;

use think\facade\Config;
use function json as jsonAlias;
use app\BaseController;
use  think\facade\Db;
use think\facade\View;
use think\response\Json;
use \think\facade\Filesystem;


class App extends  BaseController
{

    /**
     *  表主键 ,默认 id , 值里面包含 table 时，table 会替换成对应模型名
     * @var string
     */
    public $primary = 'id';

    //软删除字段
    public $deleteTime = 'delete_time';

    public  static $where = [
        'delete_time'=>0
    ];

    protected function initialize()
    {

    }


    /**
     * 接口式json数据返回
     * @param null $msgOrData
     *      为字符串时，代表错误提示， 数组或对象便是返回数据
     * @param int|bool $code
     *      返回码, 默认为：$msgOrData字符串且为真 code = 404（可自定义），否则 code = 200
     * @return Json
     */
    final public static function apiJson($msgOrData = null, $code = 200)
    {
        return jsonAlias([
            'code' => $code,
            'msg' => is_string($msgOrData)  ?  $msgOrData : ($code == 200 ? '成功':null),
            'data' => is_array($msgOrData) || is_object($msgOrData) || is_bool($msgOrData)? $msgOrData :[]
        ]);
    }



    /**
     * 数据过滤
     * @param      $data
     * @param bool $all
     * @return array
     */
    final protected function filter($data, $all = true)
    {
        $data = array_filter($data, function (&$value, $key) use ($all) {
            $value = is_array($value) && $all ? $this->filter($value) : trim($value);
           return ($value || (is_numeric($value) && $value == 0)) ?: false;
        }, ARRAY_FILTER_USE_BOTH);
        return $data;
    }


    /**
     * 添加
     * @param array $data  数据
     * @param array $field  写入字段
     * @param string $model  表名
     * @return mixed
     */
    final  protected  function insert($data = [], $field = [], $model = '')
    {
        $lower = strtolower($this->request->method()); //小写
        $way = 'is' . ucfirst($lower); //首字母大写
        if($this->request->$way() || !empty($this->filter($this->request->$lower()))){
            $data =  $data ?:  $this->request->$lower();
            $model = model($model ?: $this->request->controller());
            $field = $field?:array_keys($data);
            return  count($data) != count($data,1) && in_array(0,array_keys($data))? $model->allowField(true)->saveAll($data) :$model::create($data,$field);
        }
        throw new \Exception("添加失败");
    }




    /**
     * 修改
     * @param array $data 数据
     * @param array $where  条件
     * @param array $field  修改字段
     * @param string $model     表名
     * @return mixed
     */
    final  protected  function update($data = [], $where = [], $field = [],$model = '')
    {
        $lower = strtolower($this->request->method()); //小写
        $way = 'is' . ucfirst($lower); //首字母大写
        if($this->request->$way() || !empty($this->request->$lower())){
            $data =  $data ?  $data :  $this->request->$lower();
            $where = empty($where) && in_array($this->primary,array_keys($data)) ? [] : isset($data[$this->primary]) ? [$this->primary=>$data[$this->primary]] :$where;
            $model = model($model ? $model : $this->request->controller());
            return  count($data) != count($data,1) && in_array(0,array_keys($data)) ?  $model->saveAll($data) : $model::update($data,$where,$field);
        }
        throw new \Exception("修改失败");
    }

    /**
     * 删除数据
     * @param array $id 数组
     * @return Json
     */
    public function del($id = [])
    {
        if (method_exists(static::class, 'delete')) { //优先调用 delete 方法
            $result = $this->delete($id);
        } else {
            try {
                $model = explode('\\',static::class);
                //获取表字段
                $field = array_column(Db::query('show COLUMNS FROM ' . env('database.prefix', ''). parse_name(end($model))),'Field') ;
                    $result = in_array($this->deleteTime,$field) ?
                        $this->update([$this->deleteTime=>$_SERVER['REQUEST_TIME']],[['id','in',$id]]) //软删除
                        :
                        model($this->request->controller())->whereIn('id',$id)->delete(); //真删除
            } catch (\Exception $exception) {
                $result = $exception->getMessage();
            }
        }
        return self::apiJson($result ,is_string($result)? 404 :200);
    }


    /**
     * 验证
     * @param $data
     * @param $type
     * @return string
     */
    public function verify(array $data,string $type = ''){
        try {
            $this->validate($data, strtr(static::class,  ['controller' => 'validate','\admin\\'=>'\common\\','\api\\'=>'\common\\']) .($type ? '.'.$type : '') ,[],true);
        }catch (\Exception $exception) {
            return $exception->getMessage();
        }
        return  $data;
    }



    /**
     * @param string $exit
     * @return string|string[]
     */
    public  function upload(){
//        $file = $this->request->file('file');
//        $config = Config::get('filesystem.disks.qiniu');
//        $token =  (new Auth($config['accessKey'], $config['secretKey']))->uploadToken($config['bucket']);
//        $ket = 'upload/'.date('Ymd').'/'.$file->md5().'.'.explode('.',$file->getOriginalName())[1];
//        list($ret, $err) =  (new UploadManager())->putFile($token, $ket, $file->getPathname());
//        if ($err !== null) throw  new \Exception($err);
//        return  $ret['key'];
        $savename = Filesystem::disk('public')->putFile( 'upload', $this->request->file('file'));
        return  str_replace('\\','/', $savename);
    }

    /**
     * @param string $template  文件名
     * @param array $vars 输出变量
     * @return string
     * @throws \Exception
     */
    protected function  fetch(string $template = '',array $vars = []): string
    {
        return View::fetch($template, $vars);
    }



}