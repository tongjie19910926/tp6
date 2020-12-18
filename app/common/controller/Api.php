<?php


namespace app\common\controller;


use app\common\model\ShopCar;

class Api extends App
{


    /**
     * 清除购物车
     * @param $ord_fot
     * @param $user_id
     */
    protected function del_car($ord_fot,$user_id){
          $car = ShopCar::where([
              'delete_time'=>0,
              'user_id'=>$user_id
          ])
          ->field([
              'commodity_format_id',
              'quantity'
          ])
          ->column('quantity','commodity_format_id');  //用户购物车
        $shop = array_column($ord_fot,'quantity','commodity_format_id');// 下单 商品规格 数量
        $del = [];
        foreach ($shop as $index => $value){
            $cal = $car[$index] <=> $value;
            if ($cal <= 0){
                $del[]=$index;
                continue;
            }
            ShopCar::where(['user_id'=>$user_id,'commodity_format_id'=>$index])
                ->dec('quantity',$value)
                ->update();
        }
        if ($del) ShopCar::where(['delete_time'=>0])->whereIn('commodity_format_id',$del)->delete();
    }



    public function upload()
    {
        try {
            return   self::apiJson(parent::upload(),200);
        }catch (\Exception $e){
            return  self::apiJson($e->getMessage(),404);
        }
    }





}