<?php
/**
 * 传承短信发送
 */
namespace sdModule\common;

/**
 * Class CcSms
 * @package app\common\controller
 */

class CcSms
{
    private const URL = 'http://182.140.233.19/sms/send/index.php';
    private const APPKEY = 'f402ea7c175ec6aedb716cc15bba3ceb';

    private static $name='';
    /**
     * @var string 账号
     */
    private static $account = 'mls_wuliu_tz';

    /**
     * @var string 密码
     */
    private static $password = 'mls@1234';

    /**
     * @var string 模板ID
     */
    private static $template;

    /***
     * @var string 类型，1 是短信，其他的自己看去
     */
    private static $types = '1';

    /**
     * @var string 参数
     */
    private static $parameter;

    /**
     * @var string 内容,可直接复制过来
     */
    private static $content;

    /**
     * @var string 时间戳
     */
    private static $timestamp;

    /**
     * @var string 签名
     */
    private static $sign;

    /**
     * @var string 定时发送 eq: 2018-1-1 12:00:00 ,为空则是即时
     */
    private static $sendtime;

    /**
     * @var string 手机号
     */
    private static $mobile;
    /**
     * 通知方名称
     * @var string
     */

    /**
     * 验证码类型
     * @param $phone string 手机号码
     * @param $code string 验证码
     * @throws \ReflectionException
     */
    public static function phoneCode($phone, $code)
    {
//      复制模板内容
        self::$content = '【'.self::$mobile.'】尊敬的用户，你的验证码：{val}，你正在进行身份验证，请不要泄露，十分钟内有效。';
//      模板ID
        self::$template = '3746';

//        发送短信
        self::send($phone, $code);
    }

    /**
     * 纯通知类型，没有参数
     * @param $phone
     * @throws \ReflectionException
     */
    public static function notice($phone)
    {
//       复制模板内容
        self::$content = '【'.self::$mobile.'】您好，您有一个任务订单待接取，请到小程序内进行处理。';
//      模板ID
        self::$template = '3741';

//        发送短信
        self::send($phone);
    }

    /**
     * 带有一个参数的通知类型
     * @param $phone string 手机号码
     * @param $value string 变量值
     * @throws \ReflectionException
     */
    public static function noticeOne($phone, $value)
    {
//       复制模板内容
        self::$content = '【'.self::$mobile.'】您好，您在马斯特技术支持平台发布的任务：{val},已被抢单，请上线查看。';
//      模板ID
        self::$template = '3740';

//        发送短信
        self::send($phone, $value);
    }

    /**
     * 带有一个参数的通知类型
     * @param $phone string 手机号码
     * @param $value string 变量值
     * @throws \ReflectionException
     */
    public static function noticeOneS($phone, $value)
    {
//       复制模板内容
        self::$content = '【'.self::$mobile.'】您好，您有一个任务订单待接取，请在{val}之前到小程序内进行处理。';
//      模板ID
        self::$template = '3741';

//        发送短信
        self::send($phone, $value);
    }
    /**
     * 发送短信
     * @param $phone string|integer 手机号码
     * @param mixed ...$value 值，多个值依次传入即可
     * @example self::reloadValue($a, $b, $c, $d)
     * @throws \ReflectionException
     */
    private static function send($phone, ...$value)
    {
//        属性重新赋值
        self::$parameter = $value ? '["' . implode('","', $value) . '"]' : '';
        self::$mobile = $phone;
        self::$content = sprintf(strtr(self::$content, ['{val}' => '%s']), ...$value);
        self::$timestamp = $_SERVER['REQUEST_TIME'];
        self::$sign = md5(self::$mobile . self::$timestamp . self::APPKEY);

//      发送短信请求,测试失败时可打印返回的数据查看错误原因
        $result = self::http_post_data(self::URL, self::sendData());
//        pr($result);
    }

    /**
     * 发送请求
     * @param $url
     * @param $data_string
     * @return array
     */
    private static function http_post_data($url, $data_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }

    /**
     * 组装发送请求数据及返回
     * @return string
     * @throws \ReflectionException
     */
    private static function sendData()
    {
        $object = new \ReflectionClass(self::class);

        $sendData = '';

        foreach ($object->getStaticProperties() as $staticProperty => $value) {
            $sendData .= $staticProperty . '=' . urlencode($value) . '&';
        }

        return substr($sendData, 0, -1);
    }
}

