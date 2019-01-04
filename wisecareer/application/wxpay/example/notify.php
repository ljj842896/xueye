

<?php
ini_set('date.timezone','Asia/Shanghai');



require_once('/data/api/vendor/catfan/medoo/medoo.php');

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{

		Log::DEBUG("begin notify1222222");

		// Log::DEBUG("call back:" . json_encode($data));
		// $notfiyOutput = array();
		
		// if(!array_key_exists("transaction_id", $data)){
		// 	$msg = "输入参数不正确";
		// 	return false;
		// }
		// //查询订单，判断订单真实性
		// if(!$this->Queryorder($data["transaction_id"])){
		// 	$msg = "订单查询失败";
		// 	return false;
		// }
		// return true;
	}
}

class apiModel  extends \medoo {

	
	public $time ;
	

	public function __construct(){
		$database = require_once('/data/api/core/config/database.php');

		parent::__construct($database);

	}
}

Log::DEBUG("begin notify");
// $notify = new PayNotifyCallBack();
// $notify->Handle(false);

$xmlData = file_get_contents('php://input');
libxml_disable_entity_loader(true);
$data = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

ksort($data);
$buff = '';
foreach ($data as $k => $v){
    if($k != 'sign'){
        $buff .= $k . '=' . $v . '&';
    }
}
$stringSignTemp = $buff . 'key=xueye9876543210wisecareerkuanwei';//key为证书密钥
$sign = strtoupper(md5($stringSignTemp));
//判断算出的签名和通知信息的签名是否一致
if($sign == $data['sign']){

	//Log::DEBUG("call back:" . json_encode($data));
	$code = $data['out_trade_no'];
	$apiModel =  new apiModel();
	$add = $apiModel->get('myuser_pay',['uid','money','action'],['code'=>$code]);

	Log::DEBUG("call back:" . json_encode($add));
	$url = "http://api.wisecareer.org/ajax/update_myuser_pay";
	$resdata['uid'] =$add['uid'];
	
	$resdata['code'] =$code;
	$resdata['action'] =$add['action'];
	if( https_request($url,$resdata) ) {
 		echo '<xml>
              <return_code><![CDATA[SUCCESS]]></return_code>
              <return_msg><![CDATA[OK]]></return_msg>
          </xml>';		
	} 

		 
		
    //处理完成之后，告诉微信成功结果
   
    exit();
}





function https_request($url,$data=null)
    {
      //1、初始化curl
      $ch = curl_init();
  
      //2、设置传输选项
      curl_setopt($ch, CURLOPT_URL, $url);//请求的url地址
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//将请求的结果以文件流的形式返回
        
      if(!empty($data))
      {
        curl_setopt($ch,CURLOPT_POST,1);//请求POST方式
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//POST提交的内容
      }
  
      //3、执行请求并处理结果
      $outopt = curl_exec($ch);
  	 
  
      //把json数据转化成数组
       if(preg_match('/^\xEF\xBB\xBF/',$outopt)) {
		    $outopt = substr($outopt,3);
		}
	

      $outoptArr = json_decode($outopt,TRUE);

  	
      //4、关闭curl
      curl_close($ch);
  
      //如果返回的结果$outopt是json数据，则需要判断一下
      if(is_array($outoptArr))
      {  
        return $outoptArr;
      }
      else
      {
        return $outopt;
      }      
    }
/*
		$code=  $data['out_trade_no'];
		$total_fee=  $data['total_fee'];

		$slesql = "SELECT uid,state FROM kw_myuser_pay WHERE `code` = {$code} ";
		$resultsle = $conn->query($slesql); 
		$add = $resultsle->fetch_assoc();

		if(!$add['state']){

			$sql = "UPDATE   kw_myuser_pay  SET state=1 WHERE code={$code} ";
			$results = $conn->query($sql);  
			$money  =  ($total_fee/100);

			$sql = "UPDATE   kw_myuser_account  SET money = money + {$money} WHERE uid = {$add['uid']} ";
			$results = $conn->query($sql2);  
		}
 */