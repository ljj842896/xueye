<?php 
session_start();
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
require_once 'function.php';
$tools = new JsApiPay();

//①、获取用户openid


if(isset($_POST['money']) && !empty($_POST['money']) && is_numeric($_POST['money']) ){
	$money = $_POST['money'];
	

	$_SESSION['money'] = $money;
	$_SESSION['action'] = $_POST['action']   ;
	$_SESSION['uid'] = $_POST['uid']   ;
	$_SESSION['url'] = $_POST['url']   ;

}



if(empty($_SESSION['payopenId'] ) ){
	$tools = new JsApiPay();
	$_SESSION['payopenId'] = $tools->GetOpenid();
}	





if(empty($_SESSION['money']) || empty($_SESSION['action']) || empty($_SESSION['uid']) ){
	header('location:/user/wallet');
	exit;
}



	$openId = $_SESSION['payopenId'];	
	$gourl = $_SESSION['url'];	

	$money = $_SESSION['money'] ;
	$uid = $_SESSION['uid'] ;
	$moneys=  $money*100;




$action='';
if(!empty($_SESSION['action'])){
	$action = $_SESSION['action'];
}



$mem = new Memcache;
    if(!$mem->connect("127.0.0.1",11211)){
    	
       exit;
    }

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


//打印输出数组信息
// function printf_info($data)
// {
//     foreach($data as $key=>$value){
//         echo "<font color='#00ff55;'>$key</font> : $value <br/>";
//     }
// }

//①、获取用户openid

//


//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($money.'职拓币');
$input->SetAttach($money.'职拓币');
$code = WxPayConfig::MCHID.date("YmdHis");
$input->SetOut_trade_no($code);
$input->SetTotal_fee($moneys);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($money.'职拓币');
$input->SetNotify_url("http://xy.wisecareer.org/wxpay/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);

$order = WxPayApi::unifiedOrder($input);


$jsApiParameters = $tools->GetJsApiParameters($order);


/*
 * $uid = $_POST['uid'];
	 		$code = $_POST['code'];
	 		$action = empty($_POST['action'])?'':$_POST['action'];
	 		$money = $_POST['money'];
 			$rechargeModel = new rechargeModel();
 			$add_pay['code']=$code;
 			$add_pay['action']=$action;
			$add_pay['uid']=$uid;
			$add_pay['money']=$money;
			$add_pay['pay_type']=2;
			$add_pay['state']=0;
			$add_pay['datetime']=$this->time;

			$rechargeModel->add_pay($add_pay);*/

$url = "http://api.wisecareer.org/ajax/add_myuser_pay";
$data['uid'] = $uid;
$data['money'] = $money;
$data['code'] = $code;
$data['action'] = $action;
 

$token = md5('xueyeguihuaapi');

 https_request($url,$data);



//获取共享收货地址js函数参数
//$editAddress = $tools->GetEditAddressParameters();



//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>学业e站</title>
    <script src="http://xueye.wisecareer.org/js/jquery-1.8.3.min.js"></script>
    <script src="http://xueye.wisecareer.org/js/layer/mobile/layer.js "></script>
	<link rel="stylesheet" href="http://xueye.wisecareer.org/js/layer/mobile/need/layer.css "/>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				if(res.err_msg =="get_brand_wcpay_request:cancel"){
					layer.open({
				content: '支付失败！'
				,shadeClose: false
				,btn: '我知道了'
			});
				}else if(res.err_msg =="get_brand_wcpay_request:ok" ){
					
					
layer.open({
								content: '支付成功！'
								,shadeClose: false
								,btn: '我知道了'
								,yes: function(index){
				 					window.location.href="<?php echo $gourl  ?>/user/vip_text"
				 				}
							}); 					
					// $.ajax({
				 //        url: "http://api.wisecareer.org/ajax/update_myuser_pay",
				 //        type: "Post",
				 //        data: "token=<?php echo $token;?>&uid=<?php echo $uid;?>&code=<?php echo $code;?>&action=<?php echo $action?>",
				 //        dataType:'json',
				 //        async:false,
				 //        success: function (result){
				        	
				 //        }
				 //  	})

			  	

				
					
				}
			
				//WeixinJSBridge.log(res.err_msg);
			
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	// function editAddress()
	// {
	// 	WeixinJSBridge.invoke(
	// 		'editAddress',
	// 		<?php echo $editAddress; ?>,
	// 		function(res){
	// 			alert(res.err_msg)
	// 			var value1 = res.proviceFirstStageName;
	// 			var value2 = res.addressCitySecondStageName;
	// 			var value3 = res.addressCountiesThirdStageName;
	// 			var value4 = res.addressDetailInfo;
	// 			var tel = res.telNumber;
				
				
	// 		}
	// 	);
	// }
	
	// window.onload = function(){
	// 	if (typeof WeixinJSBridge == "undefined"){
	// 	    if( document.addEventListener ){
	// 	        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
	// 	    }else if (document.attachEvent){
	// 	        document.attachEvent('WeixinJSBridgeReady', editAddress); 
	// 	        document.attachEvent('onWeixinJSBridgeReady', editAddress);
	// 	    }
	// 	}else{
	// 		editAddress();
	// 	}
	// };
	
	</script>
	<style></style>
</head>
<body>
    <br/>
    <div id = "addr">

    </div>
    <br/><br/>
    <div  align="center"><img src="http://www.wisecareer.org/images/weixin.jpg" alt="" width="40%"/></div>
    <br/>
    <div align="center" style="letter-spacing:2px;color:#666;"><font color="#666;"><b>该笔订单应付金额为<span style="color:#f00;font-size:50px"><?php echo $money;?></span>元</b></font><br/><br/></div>
    <br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#00bb0c; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:18px;font-weight:bold;letter-spacing:2px" type="button" onClick="callpay()" >立即支付</button>
	</div>
</body>
</html>