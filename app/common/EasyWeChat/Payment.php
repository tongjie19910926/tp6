<?php


namespace app\common\EasyWeChat;
class Payment extends  App   //微信支付
{

    /**
     *
     * $result = $app->order->unify([
        'body' => '腾讯充值中心-QQ会员充值',
        'out_trade_no' => '20150806125346',
        'total_fee' => 88,
        'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
        'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
        'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
        ]);
     https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
     *
     * h5  小程序 下单
     * @param $data
     * @return array
     */
    public function order(array $data)
    {
        $result =  $this->factory->order->unify($data);
        if($result['return_code'] !== 'SUCCESS' || $result['return_msg'] !== 'OK' || $result['result_code'] !== 'SUCCESS' ) return $result['err_code_des']??'失败';
        $parameters = $this->jssdk($result['prepay_id'],'wx');
        $parameters['out_trade_no'] = $data['out_trade_no'];
        return $parameters;
    }

    /**
     * 生成签名
     * @param array $signArray
     * @return string
     */
    private function sign(array $signArray = [])
    {
        if (empty($signArray)) {
            foreach ($this as $key => $value) {
                if ($key != 'key' && $value) $signArray[$key] = $value;
            }
        }
        ksort($signArray);
        $signString = '';
        foreach ($signArray as $key => $value) {
            $signString .= $key . '=' . $value . '&';
        }
        $signString .= 'key=' . $this->config['key'];
        $sign_type = 'MD5';
        $this->sign = strtoupper($sign_type($signString));
        return $this->sign;
    }

    /**
     * 生成jssdk
     * @param $prepayId  下单之后返回的prepayId
     * @param string $type  \\调用的类型
     * [
     *  'wx',  //公众号
     *  'h5', //h5页面
     *  'app', //小程序
     * ]
     */
    protected  function jssdk($prepayId,$type = 'wx'){
        switch ($type){
            case 'wx':
                return   $this->factory->jssdk->bridgeConfig($prepayId,false); // 返回 json 字符串，如果想返回数组，传第二个参数 false
            case 'h5':
                return   $this->factory->jssdk->sdkConfig($prepayId); // 返回数组
            case 'app':
                return   $this->factory->jssdk->appConfig($prepayId); // 返回数组
            default:
                return false;
        }
    }

    /**
     * 查询订单号
     * @param  string $order  订单号
     * @param bool $type  false 为商户系统内部的订单号     true 为微信订单号
     * 返回数据 :  https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_2&index=2
     */
    public function inquiry_order(string $order,$type = false)
    {
        return  $type ? $this->factory->order->queryByTransactionId($order) :  $this->factory->order->queryByOutTradeNumber($order);

    }




    /**
     * 关闭订单
     * @param string $order  商户系统内部的订单号（out_trade_no）
     * 返回数据 :  https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_3&index=3
     */
    public  function  close_order(string $order)
    {
        return  $this->factory->order->close($order);
    }


    /**
     * 微信退款
     * @param array $data
     * $data = [
     *      'order_id' => string   订单号      $type  false 为 商户订单号退款    true 为  微信订单号退款
     *      'reimburse_id'=>string    退款时生成的  退款订单号
     *      'total_fee' =>int  订单金额'
     *      'refund_fee' =>int 退款金额
     *      'config'=>[]  array   //其他参数   数组   // https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_4&index=4
     *  ]
     * @param bool $type  // $type  false 为 商户订单号退款    true 为  微信订单号退款
     */
    public  function  reimburse(array $data,$type = false)    // $type  false 为 商户订单号退款    true 为  微信订单号退款
    {

        return $type ?
            $this->factory->refund->byTransactionId($data['order_id'],$data['reimburse_id'],$data['total_fee'],$data['refund_fee'],  $data['config']??[])  //根据微信订单号退款
            :
            $this->factory->refund->byOutTradeNumber($data['order_id'], $data['reimburse_id'], $data['total_fee'], $data['refund_fee'], $data['config']??[]); //根据商户订单号退款
    }



    /**
     * 撤销订单 == 全额退款
     * 调用支付接口后请勿立即调用撤销订单API，建议支付后至少15s后再调用撤销订单接口
     * @param $order string 订单号
     * @param bool $type  // $type  false 为 通过内部订单号撤销订单    true 为  通过微信订单号撤销订单
     * @return mixed  //返回数据  https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_11&index=3
     *
     */
    public function  undo_order(string $order,$type =false)
    {
        return $type? $this->factory->reverse->byTransactionId($order) : $this->factory->reverse->byOutTradeNumber($order);
    }



    /**
     * 查询退款  ---  未使用 不知道是否可行
     * @param $order 订单号
     * @param bool $is_refund  是否为退款单号  false 为 否   true 为 是
     * @param bool $is_wx  是否为微信方的订单号   false 为否    true 为是
     */
    public  function  query_refund(string $order,$is_refund = false,$is_wx =false)
    {
        switch (true){
            case $is_refund== false && $is_wx ==false :  //商户订单号
                return  $this->factory->refund->queryByOutTradeNumber($order);
            case $is_refund== false && $is_wx ==true :  //微信订单号
                return  $this->factory->refund->queryByTransactionId($order);
            case $is_refund== true && $is_wx ==true :  //微信退款单号
                return  $this->factory->refund->queryByRefundId($order);
            case $is_refund== true && $is_wx ==false :  //商户退款单号
                return  $this->factory->refund->queryByOutRefundNumber($order);
            default :
                throw new \Exception('数据错误');
        }
    }







    /**
     * 红包
     * @param array $data 传递数据
     * @param bool $type  是否为裂变红包  false 为否   true为 是
     *
     *   $type ==   false 时传递的数据
     *
     * $redpackData = [
            'mch_billno'   => 'xy123456',  //商户订单号
            'send_name'    => '测试红包',   //商户名称
            're_openid'    => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',  //用户openid
            'total_num'    => 1,  //固定为1，可不传
            'total_amount' => 100,  //单位为分，不小于100
            'wishing'      => '祝福语',  //红包祝福语
            'act_name'     => '测试活动',  //活动名称
            'remark'       => '测试备注', //备注

    ];
    $type 为true 是时传递的数据
    $redpackData = [
        'mch_billno'   => 'xy123456',
        'send_name'    => '测试红包',
        're_openid'    => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
        'total_num'    => 3,  //不小于3
        'total_amount' => 300,  //单位为分，不小于300
        'wishing'      => '祝福语',
        'act_name'     => '测试活动',
        'remark'       => '测试备注',
        'amt_type'     => 'ALL_RAND',  //可不传
        // ...
    ];
     */
    public  function  envelope(array $data ,bool $type =false){
        if (isset($data['amt_type']) && $type==false) unset($data['amt_type']);
        return $type  ?  $this->factory->redpack->sendGroup($data) : $this->factory->redpack->sendNormal($data);
    }






    /**
     * 提现
     * @param array $data
     * @param bool $type 是否付款为银行卡    false 为付款到零钱   true为 付款到银行卡
    $type ==   false 时传递的数据
    [
    'partner_trade_no' => '1233455', // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
    'openid' => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    'check_name' => 'FORCE_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
    're_user_name' => '王小帅', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
    'amount' => 10000, // 企业付款金额，单位为分
    'desc' => '理赔', // 企业付款操作说明信息。必填
    ]
    $type 为true 是时传递的数据
    [
    'partner_trade_no' => '1229222022', 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
    'enc_bank_no' => '6214830901234564', // 银行卡号
    'enc_true_name' => '安正超',   // 银行卡对应的用户真实姓名
    'bank_code' => '1001', // 银行编号
    'amount' => 100,  // 单位：分
    'desc' => '测试',
    ]
     */
    public  function toBalance(array $data ,bool $type = false ){
        return  $type ? $this->factory->transfer->toBankCard($data) :$this->factory->transfer->toBalance($data) ;
    }



    /**
     * 查询付款到零钱或者银行的订单
     * @param array $data  数据
     * @param bool $type 是否付款为银行卡    false 为付款到零钱   true为 付款到银行卡
     */
    public  function balance_order(string $data ,bool $type =false ){
        return  $type ? $this->factory->transfer->queryBankCardOrder($data) : $this->factory->transfer->queryBalanceOrder($data) ;
    }





}