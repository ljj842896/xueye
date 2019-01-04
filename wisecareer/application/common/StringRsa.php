<?php

namespace app\common;

/**
 * Class StringRsa   加密解密类
 * @package app\common\model
 */
class StringRsa{

    const IV = 'aGVuZ3lpaml1emhvdWtleQ=='; //huisizhituokey
    static public $SERCET_KEY = 'hsztappkey'; // hsztappkey
    const MODULE_UNION = 'U';    // 统一ID

    public function __construct(){
        self::$SERCET_KEY = $_SERVER ['HTTP_HOST'];
    }

    /**
     * 简单对称加密
     * @param String $string 需要加密的字串
     * @param String $skey 加密EKY
     * @return String
     */
    public function encodeStr($string = '', $skey = 'APIHSZTCOM') {
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key < $strCount && $strArr[$key].=$value;
        return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
    }

    /**
     * 简单对称解密
     * @param String $string 需要解密的字串
     * @param String $skey   解密KEY
     * @return String
     */
    public function decodeStr($string = '', $skey = 'APIHSZTCOM') {
        $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
        return base64_decode(join('', $strArr));
    }

    /**
     * 创建用户时间戳token
     * @param $user_id
     * @param string $ticket
     * @param string $expire
     * @return string
     */
    public static function createUserToken($user_id,$ticket = 'hszt123456',$expire = 60 * 60 * 24){
        $time = strtotime(date('Y-m-d ',(strtotime('+1 day')))) + 3600*3;
        //time()+$expire
        $data = ['id' => $user_id,'ticket'  => $ticket,'t' =>$time];
        return self::aesEncrypt(json_encode($data));
    }

    /**
     * 对text进行AES加密，密钥使用$secret_key,向量使用$iv
     * @param $text
     * @param string $iv
     * @param string $secret_key
     * @return string
     */
    private static function aesEncrypt($text){
        $vec = base64_decode(self::IV);
        return trim(self::urlsafe_b64encode(openssl_encrypt($text, "aes-256-cbc", self::$SERCET_KEY, 0, $vec)));
    }

    /**
     * URL base64编码
     * @param $string
     * @return mixed|string
     */
    private static function urlsafe_b64encode($string){
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * 检测$token是否超时，如果没有超时，返回token对应的用户ID
     * @param $token
     * @param bool $check_timestamp
     * @return mixed
     * @throws Exception
     */
    public static function getUserIDFromToken($token, $check_timestamp = true){
        $data = json_decode(self::aesDecrypt($token), true);
        if ($data === null) {
            return  null ;
        }
        if(array_key_exists('t', $data)){
            $expire = $data['t'];
            if($check_timestamp) {
                // 检测是否过了有效期
                if (time() <= $expire) {
                    return $data;
                } else {
                    return false;
                }
            }else{
                return $data;
            }
        }else{
            // 如果token里没有time，说明不带时间戳，直接返回
            return $data;
        }
    }




    /**
     * 对text进行AES解密，密钥使用$secret_key,向量使用$iv
     * @param $text
     * @return string
     */
    private static function aesDecrypt($text){
        $vec = base64_decode(self::IV);
        return trim(openssl_decrypt(self::urlsafe_b64decode($text), "aes-256-cbc", self::$SERCET_KEY, 0, $vec));
    }




    /**
     * URL base64解码
     * @param $string
     * @return bool|string
     */
    private static function urlsafe_b64decode($string){
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }



}