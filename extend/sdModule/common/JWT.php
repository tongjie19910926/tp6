<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:35
 */

namespace sdModule\common;
/**
 * Class JWT
 * @example
 *         JWT::getToken($var); 直接获取token ，$var 为自己的参数可不传
 *         JWT::getRefresh(7, $var)::getToken($var);需要获取refreshToken参数时
 *         JWT::setExp(86400)::getToken($var); 设置过期时间
 * @package app\interactive\controller
 */

class JWT
{
    /** @var string 秘钥 */
    const SECRET = 'SD_CL';

    /** @var string token类型 */
    const TYPE = 'JWT';

    /** @var string 加密算法类型 */
    const ALG = 'sha256';

    /** @var string 更新token的秘钥 */
    const REFRESH = 'SD_LO_VE_CL';

    /**
     * token的基本数据
     * @var array
     */
    public static $payload;

    /**
     * @var array refreshToken 数据
     */
    public static $refresh;

    /**
     * @param array $data  载荷，有效信息
     * @example
     *        以下为data的默认参数，可以有额外参数
     *           $data = [
     *              'iss' => 'jwt_admin',               // 签发者
     *              'iat' => time(),                    // 签发时间
     *              'exp' => time()+7200,               //  jwt的过期时间，这个过期时间必须要大于签发时间
     *              'nbf' => time()+60,                 // 定义在什么时间之前，该jwt都是不可用的
     *              'sub' => 'www.admin.com',           // 主题
     *              'aud' => 'www.admin.com',           //  接收jwt的一方
     *              'jti' => md5(uniqid('JWT').time())  // 该Token唯一标识
     *              'rsh' => 'asdasd'                   // 需要刷新操作的时候,refreshToken的唯一标识jti值，必须
     *           ]
     *         // 生成签发时间，生效时间，过期时间,签发者，唯一标识
     *          self::setIat()::setNbf()::setExp()::setIss()::setJti();
     * @return array
     */
    public static function getToken(array $data = [])
    {
        // 生成签发时间，生效时间，过期时间,唯一标识
        self::setIat()::setNbf()::setExp()::setIss()::setJti();
        $payload = self::base64UrlEncode(array_merge(self::$payload, $data));
        $header = self::getHeader();
        $sign = hash_hmac(self::ALG, $header . '.' . $payload, self::SECRET);
        $token = implode([$header, $payload, self::base64UrlEncode($sign)], '.');

        $tokenData = [
            'token' => $token,
            'exp' => self::$payload['exp']
        ];
        $tokenData['refresh_token'] = self::$refresh;
        return $tokenData;
    }

    /**
     * 获取刷新token 的 refreshToken，当token过期的时候可以用此refreshToken来刷新token
     * 返回refreshToken 以及他的唯一标识 jti
     * @param int|bool $exp 过期时间（单位：天）
     * @param array $fill 额外参数
     * @return self
     */
    public static function getRefresh($exp = 7, $fill = [])
    {
        $data = [
            'exp' => $_SERVER['REQUEST_TIME'] + 60 * 60 * 24 * $exp,
            'jti' => md5(uniqid(self::REFRESH) . mt_rand(0, 99))
        ];

        $data = array_merge($data, $fill);
        // 加密
        $base64UrlEncode = self::base64UrlEncode($data);
        //        签名
        $sign = hash_hmac(self::ALG, $base64UrlEncode, self::REFRESH);

        $refreshToken = $base64UrlEncode . '.' . self::base64UrlEncode($sign);

        self::$refresh = $refreshToken;
        self::$payload['rsh'] = $data['jti'];
        return new self();
    }

    /**
     * 刷新token并返回新的token
     * @param string $refreshToken 刷新token 需要用到的refreshToken参数
     * @param string $token 原token
     * @return bool|mixed
     */
    public static function refreshToken($refreshToken, $token)
    {
        $refreshToken = explode('.', $refreshToken);
        $token_payload = self::verify($token, false);

        // 数据格式不对 或 token 验证失败
        if (count($refreshToken) != 2 || empty($token_payload)) {
            return false;
        }

        list($data, $sign) = $refreshToken;
        $data = json_decode(self::base64UrlDecode($data), true);

        // 已超时
        if (empty($data['exp']) || $data['exp'] < $_SERVER['REQUEST_TIME']) {
            return false;
        }
        // refreshToken的唯一ID和当前的token对不上
        if (empty($data['jti']) || empty($token_payload['rsh']) || $data['jti'] != $token_payload['rsh']) {
            return false;
        }
        $refreshSign = hash_hmac(self::ALG, self::base64UrlEncode($data), self::REFRESH);

        // 签名不对
        if (self::base64UrlEncode($refreshSign) != $sign) {
            return false;
        }

        unset($token_payload['iat'],$token_payload['exp'],$token_payload['nbf']);
        if (self::$refresh) {
            unset($token_payload['rsh']);
        }

        return self::getToken($token_payload);
    }

    /**
     * token验证外部接口
     * @param string $token
     * @param string $type 返回类型，成功返回token里面的数据，失败：type = 404 时 直接输出 404 页面，type = data时
     *                     不验证时间返回payload数据
     *                     否则返回 false
     * @return bool
     */
    public static function tokenVerify(string $token = '', $type = '')
    {
        if ('404' == $type) {
            $verify = self::verify($token);
            if ($verify){
                return $verify;
            }else{
                header('HTTP/1.1 404 Not Found');
                exit(404);
            }
        } else if ($type == 'data'){
            return self::verify($token, false);
        }else{
            return self::verify($token);
        }
    }

    /**
     * 设置签发时间
     * @param int $time
     * @return JWT
     */
    public static function setIat(int $time = 0)
    {
        if(empty($time)){
            self::$payload['iat'] = self::$payload['iat'] ?? $_SERVER['REQUEST_TIME'];
        }else{
            self::$payload['iat'] = $_SERVER['REQUEST_TIME'] + $time;
        }
        return new self();
    }

    /**
     * 设置过期时间
     * @param int $time 单位秒
     * @return JWT
     */
    public static function setExp(int $time = 0)
    {
        if (empty($time)){
            $exp = (self::$payload['iat'] ?? $_SERVER['REQUEST_TIME']) + 60;
            self::$payload['exp'] = self::$payload['exp'] ?? $exp;
        }else{
            self::$payload['exp'] = $_SERVER['REQUEST_TIME'] + $time;
        }
        return new self();
    }

    /**
     * 设置生效时间
     * @param int $time
     * @return JWT
     */
    public static function setNbf(int $time = 0)
    {
        if (!empty($time)) {
            self::$payload['nbf'] = $time;
        }
        return new self();
    }

    /**
     * 设置token唯一标识
     * @param string $jti
     * @return JWT
     */
    public static function setJti($jti = '')
    {
        if (empty($jti)){
            self::$payload['jti'] = self::$payload['jti'] ?? uniqid('jti') . mt_rand(0, 99);
        }else{
            self::$payload['jti'] = $jti;
        }
        return new self();
    }

    /**
     * 设置签发者
     * @param string $iss
     * @return JWT
     */
    public static function setIss($iss = '')
    {
        if(empty($iss)){
            self::$payload['iss'] = self::$payload['iss'] ?? 'SD_CL';
        }else{
            self::$payload['iss'] = $iss;
        }
        return new self();
    }
    /**
     * token验证并返回payload数据
     * @param $token
     * @param bool $timeVerify 为true时表示完全验证。为false时表示不验证token时间性，用来获取token里面的数据
     * @return string|array
     */
    private static function verify($token, $timeVerify = true)
    {
        $data = explode('.', $token);

        //  格式对不上，失败
        if (count($data) != 3) {
            return 'token 无效';
        }

        list($header, $payload, $sign) = $data;
        $header = json_decode(self::base64UrlDecode($header), true);
        $payload = json_decode(self::base64UrlDecode($payload), true);

        // 数据对不上， 失败
        if (!$header || empty($header['alg']) || !$payload){
            return '数据解析错误！';
        }

        // 未达到使用时间
        if (!empty($payload['nbf']) && $payload['nbf'] > $_SERVER['REQUEST_TIME'] && $timeVerify) {
            return 'token 未到使用时间，不可用';
        }

        // 时间已过期
        if (!empty($payload['exp']) && $payload['exp'] < $_SERVER['REQUEST_TIME'] && $timeVerify){
            return 'token 过期';
        }

        $sign_base = hash_hmac($header['alg'], self::base64UrlEncode($header) . '.' . self::base64UrlEncode($payload), self::SECRET);

        // 和我们自己的签名对不上
        if (self::base64UrlEncode($sign_base) != $sign){
            return '签名错误！';
        }

        return $payload;
    }
    /**
     * 获取头部信息
     * @return string
     */
    private static function getHeader()
    {
        $header = [
            'alg' => self::ALG,
            'typ' => self::TYPE
        ];

        return self::base64UrlEncode($header);
    }

    /**
     * 对数据进行 base64Url 加密
     * @param array|string $data
     * @return string
     */
    private static function base64UrlEncode($data)
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        $base64 = base64_encode($data);

        return strtr(rtrim($base64, '='), ['/' => '_', '+' => '-']);
    }

    /**
     * baseUrl 解密
     * @param string $data
     * @return bool|string
     */
    private static function base64UrlDecode(string $data)
    {
        $data = $data . str_repeat('=', strlen($data) % 4);
        $base64 = strtr($data, ['_' => '/', '-' => '+']);
        return base64_decode($base64);
    }
}
