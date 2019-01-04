<?php

namespace app\common\model;

use think\Model;
use think\Db;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class FxuserPay extends Model
{
 
		private  static $APPID = "wx602a56ee4a4ac69b";  // 商户账号appid 
		private  static $MCHID = "1485887672";   // 商户号 
		private  static $SECRECT_KEY  ="105e74a9d1436e5fc5f05bf07c11d0ac" ;//支付密钥签名
		private  static $IP = "123.56.194.72";
		
    /**
     *
     */
    protected static function init()
    {

    } 
	
	//企业付款到微信零钱，PHP接口调用方法
	 
 
	 public static function createNoncestr($length =32) 
{
 
	$chars = "xueye9876543210wisecareerkuanwei";  
 
	$str ="";
 
	for ( $i = 0; $i < $length; $i++ )  {  
 
		$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
 
	}  
	return $str;
}
 
	
  public static function unicode() {
	$str = uniqid(mt_rand(),1);
	$str=sha1($str);
	return md5($str);
}
	 public static function arraytoxml($data){
	$str='<xml>';
	foreach($data as $k=>$v) {
		$str.='<'.$k.'>'.$v.'</'.$k.'>';
	}
	$str.='</xml>';
	return $str;
}
	 public static function xmltoarray($xml) { 
	//禁止引用外部xml实体 
	libxml_disable_entity_loader(true); 
	$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
	$val = json_decode(json_encode($xmlstring),true); 
	return $val;
} 
 
 public static function curl($param="",$url) {
 
	$postUrl = $url;
	$curlPost = $param;
	$ch = curl_init();                                      //初始化curl
	curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
	curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
	curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);           // 增加 HTTP Header（头）里的字段 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch,CURLOPT_SSLCERT,'/data/wisecareer/application/wxpay/cert/apiclient_cert.pem'); //这个是证书的位置绝对路径
	curl_setopt($ch,CURLOPT_SSLKEY,'/data/wisecareer/application/wxpay/cert/apiclient_key.pem'); //这个也是证书的位置绝对路径
	$data = curl_exec($ch);                                 //运行curl
	curl_close($ch);
	return $data;
}

/*
$amount 发送的金额（分）目前发送金额不能少于1元
$re_openid, 发送人的 openid
$desc  //  企业付款描述信息 (必填)
$check_name    收款用户姓名 (选填)
*/
 public static function sendMoney($amount,$re_openid,$desc='测试',$check_name=''){
 
		$total_amount = (100) * $amount;
 		
	$data=array(
		'mch_appid'=>"wx602a56ee4a4ac69b",//商户账号appid
		'mchid'=> "1485887672",//商户号
		'nonce_str'=>self::createNoncestr(),//随机字符串
		'partner_trade_no'=> date('YmdHis').rand(1000, 9999),//商户订单号
		'openid'=> $re_openid,//用户openid
		'check_name'=>'NO_CHECK',//校验用户姓名选项,
		're_user_name'=> "彭宽伟",//收款用户姓名
		'amount'=>$total_amount,//金额
		'desc'=> $desc,//企业付款描述信息
		'spbill_create_ip'=> "123.56.194.72",//Ip地址
	);

    $secrect_key="xueye9876543210wisecareerkuanwei";///这个就是个API密码。MD5 32位。
    $data=array_filter($data);
    ksort($data);
    $str='';
    foreach($data as $k=>$v) {
       $str.=$k.'='.$v.'&';
    }
    $str.='key='.$secrect_key;
    $data['sign']=md5($str);
    $xml=self::arraytoxml($data); 

    $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers'; //调用接口
    $res=self::curl($xml,$url);
    $return=self::xmltoarray($res);

    print_r($return);

	//返回来的结果
	// [return_code] => SUCCESS [return_msg] => Array ( ) [mch_appid] => wxd44b890e61f72c63 [mchid] => 1493475512 [nonce_str] => 616615516 [result_code] => SUCCESS [partner_trade_no] => 20186505080216815 
	// [payment_no] => 1000018361251805057502564679 [payment_time] => 2018-05-15 15:29:50
	
	$responseObj = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);

		echo $res= $responseObj->return_code;  //SUCCESS  如果返回来SUCCESS,则发生成功，处理自己的逻辑

	 return $res;
	 
   } 
	 

}

