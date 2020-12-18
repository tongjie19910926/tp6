<?php


namespace app\common\controller;


use think\facade\Config;
use think\Request;

/**
 * @property-read Request $request
 * Trait DataWrite
 * @package app\common\controller
 */
trait DataWrite
{

    /** @var bool|array 写入数据时验证是否，值为布尔或包含要验证的场景值数组  */
    public $validate = true;


    /**
     * 数据操作
     * @param string $type 类型，add | edit
     * @return \think\response\Json
     */
    final protected function dataHandle($type = 'add')
    {
        $data = $this->verification($type);
        if (is_string($data)) return self::apiJson($data,404);
        if (method_exists($this, 'beforeWrite')){
            $this->beforeWrite($data);
        }
//==================== 存在 customAdd 或 customEdit 方法时 ，会调用并返回此函数，否则调用对应模型的addHandle 或 editHandle方法
        if (method_exists($this, 'custom' . ucfirst($type))) {
            $method = 'custom' . ucfirst($type);
            $result = $this->$method($data);
        } else {
            try {
                $action =$type . 'Handle';
                $result =  model($this->request->controller())->$action($data);
            } catch (\Exception $e) {
                $result = $type == 'edit' ?  $this->update($data) : $this->insert($data);
//                ============= 对应模型没有addHandle 或 editHandle 时
               // $result = $this->directWrite($data, $type);
            }
        }
        return self::apiJson($result ,is_string($result) ? 404 :200);
    }

    /**
     * 验证
     * @param string $type 场景
     * @return array|mixed|string 验证后的数据或错误提示字符串
     */
    protected function verification($type)
    {
        $data = $this->request->post();
        if(!in_array($type,['edit'])) $data = $this->filter($data); //修改时不用过滤

        if ($this->validate === true || (is_array($this->validate) && in_array($type, $this->validate))) {
            $validate = strtr(static::class,['controller' => 'validate','\admin\\'=>'\common\\']);
            try {
                $this->validate($data, $validate . '.' . $type);
            } catch (\Exception $exception) {

                return $exception->getMessage();
            }
        }
        return $data;
    }

//    /**
//     * 数据写入之前处理一些数据
//     * @param $data
//     */
//    protected function beforeWrite(&$data){}
}

