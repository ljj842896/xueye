<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\Log;
use app\common\model\Myuser;
use app\common\model\MyuserZtPay;
use app\common\model\Recharge;
use app\common\StringRsa;
use app\index\controller\Basics;
use think\Controller;
use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

/**
 * Class Index
 * @package app\api\controller
 */
class Wx extends Basics
{
    /**
     * @return mixed
     */
    public function fnwxlogin()
    {
        $get = '';
        if(!empty(input('get.source')) && input('get.source') =='xnw'){
            $get = '&source=xnw';
        }

        if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'xy.wisecareer.org') !==false){
            $url = empty($_SERVER['HTTP_REFERER'])?'http://xy.wisecareer.org':$_SERVER['HTTP_REFERER'];
        }else{
            $url ='http://xy.wisecareer.org';
        }

//        cookie('openid',null);
//        return redirect("http://xytest.wisecareer.org");exit;
        $redirect_uri = urlencode ( 'https://wapi.wisecareer.org/Wx/getuserinfo?url='.$url.$get );
        $wxurl ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".XYAPPID."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header("Location:".$wxurl);exit;
    }

    public function getuserinfo(){

        $appid = XYAPPID;
        $secret = APPSECRET;
        $code = $_GET["code"];
        $source = empty($_GET['source'])?'':$_GET['source'];
//第一步:取全局access_token

        $access_token = Cache::get("xyaccess_token");

        //如果缓存的access_token失效
        if(!$access_token)
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $outoptArr = getJson($url);
            //如果失效调用获取接口凭证来获取access_token
            if(!isset($outoptArr['errcode']))
            {
                //memcache缓存access_token
                Cache::set("xyaccess_token",$outoptArr['access_token'],7000);
                $access_token = $outoptArr["access_token"];
            }
        }

//第二步:取得openid
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $oauth2 = getJson($oauth2Url);


        if(!empty($oauth2['errcode'] )){
            header("Location:https://wapi.wisecareer.org/Wx/fnwxlogin");
            exit;
        }
        $httpurl =  $_GET['url'];


        //第三步:根据全局access_token和openid查询用户信息

        $openid = $oauth2['openid'];
        //$get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
       // $userinfo = getJson($get_user_info_url);
        //打印用户信息
        //$titnames =  print_r($userinfo);


        $userinfo = Myuser::whereLike('openid',"%{$openid}%")->find();
        if($source=='xnw'){
            cookie('openid',$openid,20*60);
            return redirect($httpurl.'?source=xnw');exit;
        }
        if($userinfo ==null || cookie('token')){
            cookie('openid',$openid,20*60);
            return redirect($httpurl);exit;
        }
        $uid = $userinfo['id'];  // 用户ID
        $type = 3 ;// 学业e站
        $Log = new Log();
        $Log->userlog($uid,$type);
        // 生成token   下次请求带上
        $status =Caches::MyuserXyStatus($uid,'',1);
        $days= empty($status['xy_e_time'])?0:  intval(round(($status['xy_e_time'] - time())/(24*3600))  );
        $viptype = $userinfo['viptype'];
        $token= StringRsa::createUserToken($uid);
        Cache::set($token,$uid,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        $data['token'] = $token;
        cookie('openid',$openid,'6*60*60');
        cookie('days',$days,'6*60*60');
        cookie('viptype',$viptype,'6*60*60');
        cookie('token',$token,'6*60*60');
        //打印用户信息
        //$titnames =  print_r($userinfo);

        return redirect( $httpurl);exit;
        // $_SESSION['USER_MEMBER_WX_OPENID'] = $userinfo['openid']; //用户openID
        // $_SESSION['USER_MEMBER_WX_NAME'] = $userinfo['nickname'];  // 用户名


    }

    public function ztfnwxlogin()
    {


        $get = '';
        if(!empty(input('get.source')) && input('get.source') =='xnw'){
            $get = '&source=xnw';
        }

        if(!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'zt.wisecareer.org') !==false){
            $url = empty($_SERVER['HTTP_REFERER'])?'http://zt.wisecareer.org':$_SERVER['HTTP_REFERER'];
        }else{
            $url ='http://zt.wisecareer.org';
        }

//        cookie('openid',null);
//        return redirect("http://xytest.wisecareer.org");exit;
        $redirect_uri = urlencode ( 'https://wapi.wisecareer.org/Wx/ztgetuserinfo?url='.$url.$get );
        $wxurl ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".ZTAPPID."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        header("Location:".$wxurl);exit;
    }

    public function ztgetuserinfo(){

        $appid = ZTAPPID;
        $secret = ZTAPPSECRET;
        $code = $_GET["code"];
        $source = empty($_GET['source'])?'':$_GET['source'];
//第一步:取全局access_token

        $access_token = Cache::get("ztaccess_token");

        //如果缓存的access_token失效
        if(!$access_token)
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $outoptArr = getJson($url);
            //如果失效调用获取接口凭证来获取access_token
            if(!isset($outoptArr['errcode']))
            {
                //memcache缓存access_token
                Cache::set("ztaccess_token",$outoptArr['access_token'],7000);
                $access_token = $outoptArr["access_token"];
            }
        }

//第二步:取得openid
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        $oauth2 = getJson($oauth2Url);


        if(!empty($oauth2['errcode'] )){
            header("Location:https://wapi.wisecareer.org/Wx/ztfnwxlogin");
            exit;
        }
        $httpurl =  $_GET['url'];


        //第三步:根据全局access_token和openid查询用户信息

        $openid = $oauth2['openid'];
        //$get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        // $userinfo = getJson($get_user_info_url);
        //打印用户信息
        //$titnames =  print_r($userinfo);


        $userinfo = Myuser::whereLike('ztopenid',"%{$openid}%")->find();
        if($source=='xnw'){
            cookie('ztopenid',$openid,20*60);
            return redirect($httpurl.'?source=xnw');exit;
        }
        if($userinfo ==null || cookie('token')){
            cookie('ztopenid',$openid,20*60);
            return redirect($httpurl);exit;
        }
        $uid = $userinfo['id'];  // 用户ID
        $type = 3 ;// 学业e站
        $Log = new Log();
        $Log->userlog($uid,$type);
        // 生成token   下次请求带上
        $status =Caches::MyuserXyStatus($uid,'',1);
        //$days= empty($status['zt_e_time'])?0:  intval(round(($status['zt_e_time'] - time())/(24*3600))  );
        $viptype = $userinfo['viptype'];
        $token= StringRsa::createUserToken($uid);
        Cache::set($token,$uid,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        $data['token'] = $token;
        cookie('ztopenid',$openid,'6*60*60');
        //cookie('days',$days,'6*60*60');
        cookie('viptype',$viptype,'6*60*60');
        cookie('token',$token,'6*60*60');
        //打印用户信息
        //$titnames =  print_r($userinfo);

        return redirect( $httpurl);exit;
        // $_SESSION['USER_MEMBER_WX_OPENID'] = $userinfo['openid']; //用户openID
        // $_SESSION['USER_MEMBER_WX_NAME'] = $userinfo['nickname'];  // 用户名


    }

    //微信支付 提交订单
    public function wxpay(){
        require_once APP_PATH."wxpay/lib/WxPay.Api.php";
        require_once APP_PATH."wxpay/example/WxPay.JsApiPay.php";
        require_once APP_PATH."wxpay/example/log.php";
        $tools = new \JsApiPay();
        //①、获取用户openid
        if(empty(session('money') ) || empty(cookie('token'))){
            if( (empty($_POST['money']) || empty($_POST['token']) )     ){
                return redirect("http://xy.wisecareer.org/user/wallet");exit;
                exit;
            }
        }
        if(isset($_POST['money']) && !empty($_POST['money']) && is_numeric($_POST['money']) ){
            $money = $_POST['money'];
            $token = $_POST['token'];
            $action = empty($_POST['action'])?'':$_POST['action'];
            $moneythom = empty($_POST['moneythom'])?'':$_POST['moneythom'];
            session('money',$money) ;
            if($action ==''){
                session('action', null);
                session('moneythom',null) ;
            }else{
                session('action',$action) ;
                session('moneythom',$moneythom) ;

            }
            cookie('token',$token) ;
        }else{
            $token = cookie('token');
        }
        if(empty(cookie('openid') ) ){
            cookie('openid',$tools->GetOpenid());
        }

        $tokens = StringRsa::getUserIDFromToken($token);
        if($tokens ==null || $tokens ==false){
            return redirect("http://xy.wisecareer.org/user/wallet");exit;
        }

        $uid = $tokens['id'];

        $openId = cookie('openid');
        $money = session('money');

        $action =  empty(session('action'))?'':'we' ;
        $moneythom =  empty(session('moneythom'))?'':session('moneythom');
    $moneys=  $money*100;
	// $moneys=  $money*1;
        $pay_money = $money;
        if($action ==''){
            $pay_money = Db::name('myuser_activity_rule')->where('money',$money)->value('coupon');
            $add_pay['coin']=$money;
        }

//初始化日志
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);
//①、获取用户openid

//②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($money.'test');
        $input->SetAttach($money.'test');
        $code = \WxPayConfig::MCHID.date("YmdHis");
        $input->SetOut_trade_no($code);
        $input->SetTotal_fee($moneys);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($money.'test');
        $input->SetNotify_url("https://wapi.wisecareer.org/Wx/notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);

        $add_pay['code']=$code;
        $add_pay['action']=$action;
        $add_pay['purchase']=$moneythom;
        $add_pay['uid']=$uid;
        $add_pay['money']=$pay_money;
        $add_pay['pay_type']=2;
        $add_pay['state']=0;
        $add_pay['datetime']=time();
        Recharge::name('myuser_pay')->data($add_pay)->insert();

        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('money',$money);

        return $this->fetch();
    }

    public function notify(){

        require_once APP_PATH."wxpay/example/log.php";
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');

//初始化日志
        $log = \Log::Init($logHandler, 15);
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
        \Log::DEBUG("call back:" . json_encode($data));
//判断算出的签名和通知信息的签名是否一致
        if($sign == $data['sign']){
           // $code ='148624028220180712193726';
 
            $code = $data['out_trade_no'];
            $res = Recharge::wxpay_recharge($code);
            if($res){
                echo '<xml>
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
              </xml>';
           }
            exit();
        }

    }


    // 订单产生
    public function meal_wxpay(){

        require_once APP_PATH."wxpay/lib/WxPay.Api.php";
        require_once APP_PATH."wxpay/example/WxPay.JsApiPay.php";
        require_once APP_PATH."wxpay/example/log.php";
        $tools = new \JsApiPay();
        //①、获取用户openid

        session('action', trim(input('post.action')));
        session('token', trim(input('post.token')));
        session('tpid', trim(input('post.tpid')));
        $action = trim(input('post.action'))? trim(input('post.action')) :session('action');
        $token = trim(input('post.token'))? trim(input('post.token')) :session('token');
        $tpidstr = trim(input('post.tpid'))? trim(input('post.tpid')) :session('tpid');

        if(!$action || !$token || !$tpidstr){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }

        if(empty(cookie('openid') ) ){
            cookie('openid',$tools->GetOpenid());
        }

        $tokens = StringRsa::getUserIDFromToken($token);


        if($tokens ==null || $tokens ==false){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }
        $uid = $tokens['id'];
        $openId = cookie('openid');
        // 获取金额
        $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        $hasdata = MyuserZtPay::where('id',$tpid)->where('uid',$uid)->find();
        // 开通时间  是否在范围   tpid 是否存在对应的 数据
        if($hasdata==null){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }

        // 金额
        $money = $hasdata['money'];
         $moneys=  $money*100;
		//$moneys=  $money*1;

//初始化日志
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);
//①、获取用户openid

//②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($money.'test');
        $input->SetAttach($money.'test');
        $code = \WxPayConfig::MCHID.date("YmdHis");
        $input->SetOut_trade_no($code);
        $input->SetTotal_fee($moneys);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($money.'test');
        $input->SetNotify_url("https://wapi.wisecareer.org/Wx/meal_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);


        // 充值
        $add_pay['code']=$code;
        $add_pay['action']=$action;
        $add_pay['uid']=$uid;
        $add_pay['money']=$money;
        $add_pay['pay_type']=2;
        $add_pay['state']=0;
        $add_pay['datetime']=time();
		
        Recharge::name('myuser_pay')->data($add_pay)->insert();

        $hasdata = MyuserZtPay::where('id',$tpid)->where('uid',$uid)->update(['code'=>$code]);
        $hasdata = MyuserZtPay::name('myuser_zt_eval_pay')->where('classid',$tpid)->where('uid',$uid)->update(['code'=>$code]);
        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('money',$money);


        return $this->fetch();
    }

    public function meal_notify(){

        require_once APP_PATH."wxpay/example/log.php";
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');

//初始化日志
        $log = \Log::Init($logHandler, 15);
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
        \Log::DEBUG("call back:" . json_encode($data));
//判断算出的签名和通知信息的签名是否一致
        if($sign == $data['sign']){
            // $code ='148624028220180712193726';

            $code = $data['out_trade_no'];
            $res = Recharge::wxpay_recharge_meal($code);
            if($res){
                echo '<xml>
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
              </xml>';
            }
            exit();
        }

    }


    public function meal_wxpay_eval(){

        require_once APP_PATH."wxpay/lib/WxPay.Api.php";
        require_once APP_PATH."wxpay/example/WxPay.JsApiPay.php";
        require_once APP_PATH."wxpay/example/log.php";
        $tools = new \JsApiPay();
        //①、获取用户openid

        session('action', trim(input('post.action')));
        session('token', trim(input('post.token')));
        session('tpid', trim(input('post.tpid')));
        $action = trim(input('post.action'))? trim(input('post.action')) :session('action');
        $token = trim(input('post.token'))? trim(input('post.token')) :session('token');
        $tpidstr = trim(input('post.tpid'))? trim(input('post.tpid')) :session('tpid');

        if(!$action || !$token || !$tpidstr){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }

        if(empty(cookie('openid') ) ){
            cookie('openid',$tools->GetOpenid());
        }

        $tokens = StringRsa::getUserIDFromToken($token);

        if($tokens ==null || $tokens ==false){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }
        $uid = $tokens['id'];
        $openId = cookie('openid');
        // 获取金额
        $tpid = intval(input('tpid'));
        $eval_data = MyuserZtPay::name('myuser_zt_eval')->where('id',$tpid)->find();
        // 开通时间  是否在范围   tpid 是否存在对应的 数据
        if($eval_data==null){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }

        // 金额
        $money = $eval_data['money'];
        $moneys=  $money*100;
		 //$moneys=  $money*1;


//初始化日志
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);
//①、获取用户openid

//②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($money.'test');
        $input->SetAttach($money.'test');
        $code = \WxPayConfig::MCHID.date("YmdHis");
        $input->SetOut_trade_no($code);
        $input->SetTotal_fee($moneys);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($money.'test');
        $input->SetNotify_url("https://wapi.wisecareer.org/Wx/meal_notify_eval");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);


        // 充值
        $add_pay['code']=$code;
        $add_pay['action']=$action;
        $add_pay['uid']=$uid;
        $add_pay['money']=$money;
        $add_pay['pay_type']=2;
        $add_pay['state']=0;
        $add_pay['datetime']=time();
        Recharge::name('myuser_pay')->data($add_pay)->insert();

        $insert_eval_pay['uid']=$uid;
        $insert_eval_pay['eval_num']=$eval_data['strip'];
        $insert_eval_pay['code']=$code;
        $insert_eval_pay['money']=$eval_data['money'];
        $insert_eval_pay['datetime']=$this->request->time();
        $hasdata = MyuserZtPay::name('myuser_zt_eval_pay')->insert($insert_eval_pay);
        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('money',$money);


        return $this->fetch();
    }

    public function meal_notify_eval(){

        require_once APP_PATH."wxpay/example/log.php";
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');
//初始化日志
        $log = \Log::Init($logHandler, 15);
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
        \Log::DEBUG("call back:" . json_encode($data));
//判断算出的签名和通知信息的签名是否一致
        if($sign == $data['sign']){
            // $code ='148624028220180712193726';

            $code = $data['out_trade_no'];
            $res = Recharge::wxpay_recharge_meal_eval($code);
            if($res){
                echo '<xml>
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
              </xml>';
            }
            exit();
        }

    }

    public function meal_wxpay_en(){

        require_once APP_PATH."wxpay/lib/WxPay.Api.php";
        require_once APP_PATH."wxpay/example/WxPay.JsApiPay.php";
        require_once APP_PATH."wxpay/example/log.php";
        $tools = new \JsApiPay();
        //①、获取用户openid

        session('action', trim(input('post.action')));
        session('token', trim(input('post.token')));
        session('tpid', trim(input('post.tpid')));
        $action = trim(input('post.action'))? trim(input('post.action')) :session('action');
        $token = trim(input('post.token'))? trim(input('post.token')) :session('token');

        if(!$action || !$token ){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }

        if(empty(cookie('openid') ) ){
            cookie('openid',$tools->GetOpenid());
        }

        $tokens = StringRsa::getUserIDFromToken($token);

        if($tokens ==null || $tokens ==false){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }
        $uid = $tokens['id'];
        $openId = cookie('openid');
        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        if(!$userinsertpay){
            return redirect("http://zt.wisecareer.org/user/wallet");exit;
        }



        // 金额
        $money = $userinsertpay['moneys'];
       $moneys=  $money*100;
		//$moneys=  $money*1;


//初始化日志
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);
//①、获取用户openid

//②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($money.'test');
        $input->SetAttach($money.'test');
        $code = \WxPayConfig::MCHID.date("YmdHis");
        $input->SetOut_trade_no($code);
        $input->SetTotal_fee($moneys);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($money.'test');
        $input->SetNotify_url("https://wapi.wisecareer.org/Wx/meal_notify_en");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);


        // 充值
        $add_pay['code']=$code;
        $add_pay['action']=$action;
        $add_pay['uid']=$uid;
        $add_pay['money']=$money;
        $add_pay['pay_type']=2;
        $add_pay['state']=0;
        $add_pay['datetime']=time();
        Recharge::name('myuser_pay')->data($add_pay)->insert();


        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('money',$money);


        return $this->fetch();
    }

    public function meal_notify_en(){

        require_once APP_PATH."wxpay/example/log.php";
        $logHandler= new \CLogFileHandler(APP_PATH."wxpay/logs/".date('Y-m-d').'.log');
//初始化日志
        $log = \Log::Init($logHandler, 15);
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
        \Log::DEBUG("call back:" . json_encode($data));
//判断算出的签名和通知信息的签名是否一致
        if($sign == $data['sign']){
            // $code ='148624028220180712193726';

            $code = $data['out_trade_no'];
            $res = Recharge::wxpay_recharge_meal_en($code);
            if($res){
                echo '<xml>
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
              </xml>';
            }
            exit();
        }

    }
}
