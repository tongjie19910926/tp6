<?php


namespace app\facade\EasyWeChat;

use think\Facade;


/**
 * @see \app\common\EasyWeChat\Payment
 * @package think\facade
 * @mixin \app\common\EasyWeChat\Payment
 * @method static MiniProgram config() 获取配置信息
 * @method static MiniProgram order(array $data) h5  小程序 下单
 * @method static MiniProgram inquiry_order(string $order,$type = false) 查询订单号
 * @method static MiniProgram close_order(string $order) 关闭订单
 * @method static MiniProgram reimburse(array $data,$type = false) 微信退款
 * @method static MiniProgram undo_order(string $order,$type = false) 撤销订单 == 全额退款
 * @method static MiniProgram query_refund(string $order,$is_refund = false,$is_wx =false) 查询退款
 * @method static MiniProgram envelope(array $data ,bool $type =false) 发送红包
 * @method static MiniProgram toBalance(array $data ,bool $type = false) 提现
 * @method static MiniProgram balance_order(string $data ,bool $type =false) 查询付款到零钱或者银行的订单
 */
class Payment extends Facade
{


    protected static function getFacadeClass()
    {
        return 'app\common\EasyWeChat\Payment';
    }
}