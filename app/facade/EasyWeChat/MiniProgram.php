<?php


namespace app\facade\EasyWeChat;

use think\Facade;


/**
 * @see \app\common\EasyWeChat\MiniProgram
 * @package think\facade
 * @mixin \app\common\EasyWeChat\MiniProgram
 * @method static MiniProgram config() 获取配置信息
 * @method static MiniProgram info(string $code) 获取openid session_key
 * @method static MiniProgram send(string $openid, string $template_id,array $data,array $config = []) 发送模板消息
 * @method static MiniProgram decryptData($session,$iv,$encryptedData) 消息解密
 */

class MiniProgram extends Facade
{

    protected static function getFacadeClass()
    {
        return 'app\common\EasyWeChat\MiniProgram';
    }

}