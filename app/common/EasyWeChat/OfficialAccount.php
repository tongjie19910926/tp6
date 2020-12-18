<?php
namespace app\common\EasyWeChat;
use think\facade\Env;
use app\Request;
class OfficialAccount extends  App    //微信公众号
{







    /**
     * 配置菜单
     * @return mixed
     */
    public function menu(array $buttons =[]){

        return  $this->factory->menu->create($buttons??[]);
    }



    /**
     * 服务端验证配置
     * @param $data
     * @return bool
     */
    private function checkSignature()
    {
        $response = $this->factory ->server->serve();
        $response->send();
        exit;
    }



    /**
     * 前后端网页授权 ---所用场景公众号网页
     * @param string $url
     * @return \think\response\Json
     */
    public function oauth( string $url)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->config['app_id']}&redirect_uri={$url}&response_type=code&scope=SCOPE&state=STATE#wechat_redirect";
        return  $url;
    }


    /**
     *生成二维码 图片 链接
     * @param $content   图片内容
     * @param bool $type  临时为false   永久为true
     * @param bool $echo  false不生成图片（系统路径）   true生成图片(微信网络图片)
     * @return bool
     */
    public  function   interim_code(string $content,bool $type = false, bool $echo = false)
    {
        $result  =  $type ?  $result = $this ->factory->qrcode->forever($content)  : $this ->factory->qrcode->temporary($content, 30 * 24 * 3600);
        $url = $this ->factory->qrcode->url( $result['ticket']);
        if($echo)  return  $url ;
        $path = 'upload'.DS .date('Ymd');
        $suffix  = md5(file_get_contents($url)).'.jpg';
        $list['url'] = Env::get('root_path').'public'.DS.$path.DS.$suffix;
        if (!is_dir(dirname($list['url'])))   mkdir(dirname($list['url']), 777, true);
        return  file_put_contents( $list['url'], $content) ? '/'.str_replace(DS,'/',$path.DS.$suffix): false; // 写入文件
    }


    /**
     * 公众号消息
     * @return bool
     */
    public function news()
    {

        if((new Request)->isGet() && !empty((new Request)->get())) return $this->checkSignature();  //配置
        $this->factory->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    if($message['Event'] == 'subscribe' ){  //关注
                        //  return   $this->add($message['FromUserName']) ? '您已关注成功' : '系统错误';
                    }elseif ($message['Event'] == 'unsubscribe')  //取消关注
                    {
                        // $this->unsubscribe($message['FromUserName']);
                    }
                    // return '您已关注成功';
                    break;
                case 'text':
                    // return "" ;
                    break;
                case 'image':
                    //  return "收到图片消息.你发送的图片的MediaId\r\n" . $message['MediaId'] . "\r\n图片链接地址为\r\n" . $message['PicUrl'];
                    break;
                case 'voice':
                    //return "收到语音消息\r\n" . $message['Recognition'];
                    break;
                case 'video':
                    // return '收到视频消息';
                    break;
                case 'location':
                    //return '收到坐标消息' . $message['Location_X'] . '|' . $message['Location_Y'];
                    break;
                case 'link':
                    //return '收到链接消息' . $message['Url'];
                    break;
                // ... 其它消息
                default:
                    //  return '收到其它消息';
                    break;
            }

        });
        $this->factory->server->serve()->send();
    }

}