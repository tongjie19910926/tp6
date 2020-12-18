<?php


namespace app\facade\EasyWeChat;

use think\Facade;


/**
 * @see \app\common\EasyWeChat\OfficialAccount
 * @package think\facade
 * @mixin \app\common\EasyWeChat\OfficialAccount
 * @method static MiniProgram config() 获取配置信息
 * @method static MiniProgram oauth(string $url) 前后端网页授权 ---所用场景公众号网页
 * @method static MiniProgram news() 公众号消息
 * @method static MiniProgram interim_code(string $content,bool $type = false, bool $echo = false) 生成二维码 图片 链接
 */

class OfficialAccount   extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\EasyWeChat\OfficialAccount';
    }



}