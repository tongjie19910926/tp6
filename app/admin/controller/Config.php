<?php


namespace app\admin\controller;


use app\common\model\Config as Configs;
use think\facade\Cache;
use think\facade\Db;
use think\helper\Arr;

class Config extends \app\common\controller\Admin
{


    /**
     * 修改配置
     * @param \app\common\model\Config $config
     * @return \think\response\Json
     */
    public function systemEdit(Configs $config){
        Db::startTrans();
        try {
            $post = $this->request->post();
            $list = $config->where(['group'=>$post['type']])->field(['name'])->column('name');

            foreach (Arr::except($post,['type']) as $item => $value){
                if (in_array($item,$list) ){ //如果有
                    $config->where(['group'=>$post['type'],'name'=>$item])->update(['value'=>trim($value)]);
                    continue;
                }
                $config::create(['group'=>$post['type'],'name'=>$item,'value'=>trim($value)]);
            }
            Cache::delete('Config');//删除网站缓存
            Db::commit();
            return self::apiJson('成功',200);
        }catch (\Exception $e){
            Db::rollback();
            return self::apiJson($e->getMessage(),404);
        }

    }

}