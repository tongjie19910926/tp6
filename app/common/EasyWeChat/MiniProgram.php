<?php
namespace app\common\EasyWeChat;
use think\Exception;

class MiniProgram extends  App    //微信小程序
{





    /*
   * 根据 jsCode 获取用户 session 信息
   */
    public function  info( $code){
        return  $this->factory->auth->session($code);
    }


    /**
     * 小程序发送订阅消息
     * @param string $openid 用户id
     * @param string $template_id 模板id
     * @param array $data 模板数据格式
     * @param array $config 配置数据
     * @return bool|mixed
     */
    public function send( string  $openid, string $template_id,array $data,array $config = []){
        try {
            $send = [
                'template_id' => $template_id, // 所需下发的订阅模板id
                'touser' => $openid,     // 接收者（用户）的 openid
                'data' => $data,
                'page' => $config['page']??'',       // 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
                'miniprogram_state' => $config['miniprogram_state']??'formal',
                'lang' => $config['lang']??'zh_CN',
            ];
            $result = $this->factory->subscribe_message->send($send);
            return $result['errcode'] != 0 ? $result['errmsg'] : true;
        }catch (Exception $e){
            return  $e->getMessage();
        }
    }

    /**
     * 消息解密
     * @param $session
     * @param $iv
     * @param $encryptedData
     * @return mixed
     */
    public function  decryptData($session,$iv,$encryptedData){
        return $this->factory->encryptor->decryptData($session, $iv, $encryptedData);
    }






}