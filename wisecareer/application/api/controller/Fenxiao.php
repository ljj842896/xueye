<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\Libs;
use app\common\model\Cares;
use app\common\model\Navigationa;
use app\common\model\Fxuser;
use app\common\model\FxuserPay;
use app\common\model\Myuser;
use app\index\controller\Action;
use think\db\exception\BindParamException;
use think\exception\DbException;
use think\exception\PDOException;
use think\facade\Cache;
use think\Controller;
use think\Db;
use think\facade\Config;
 require_once '/data/wisecareer/thinkphp/library/vendor/phpqrcode/phpqrcode.php';

/**
 * Class Index
 * @package app\api\controller
 */
class Fenxiao extends Action
{
	
			// 分销用户信息表 
	public function myuserinfo()
	{ 	
			 $uid = $this->uid;   
			 //  缓存中获取个人心思
        $myinfo = Caches::MyuserInfo($uid);
        //个人资料
        $myuserStatus = Caches::MyuserXyStatus($uid);
		  if(empty($myinfo['pic'])){
				if($myinfo['sex'] =='男'){
					$pic = "userpic/man_s.png";
				}else{
					$pic = "userpic/woman_s.png";
				}
        }else{
            $pic = $myinfo['pic'];
        }
		
			$userretailid = Fxuser::userretailid($uid); 
			
			$usercheck = Db::name('user_retail')->where('check',1)->count();  //现有分销商人数
		
		 $result['myuser'] = [
				'pic'=>$pic,  
				'sex'=>$myinfo['sex'],
				'user_name'=>$myinfo['user_name'],
				'school_name'=>$myinfo['school_name'],
				'status'=>$myuserStatus['status'], 
				'viptype'=>$myinfo['viptype'],
				'classname'=>$myinfo['classname'], 
				'userretailid'=>empty( $userretailid )? '0': 1, 
				'usertersum'=>$usercheck, 
        ];
		
		  $money = Fxuser::userretail($uid);
          $result['userretail'] = $money;
		
		return $this->echo_json(0,'',$result);
		
		
			  
	}  	  
 
	
	//提现明细
	 public function cash_list()
    {
		  	 $uid = $this->uid;
			 $limit = $this->search_page(); 
			 $wheres = "";
			 $datas = "";
			 $type="";
	
		  if($_GET['type']!=""){
					$wheres = "and type = '".$_GET['type']."'";
		  } 
		  
			$sqls = "SELECT * FROM kw_user_retail_detail where  uid='$uid' ".$wheres." order by id desc ". $limit; 
	 $sqlscount = "SELECT * FROM kw_user_retail_detail where  uid='$uid' ".$wheres." order by id desc ";  
			$result = Db::query($sqls); 
			$count = count(Db::query($sqlscount));   
 
	  foreach ($result as $key => $value) { 
			$datas[$key]['money'] = $value['money'];
            $datas[$key]['type'] = $value['type'];
			$datas[$key]['state'] = $value['state'];
			$datas[$key]['datetime'] = date('Y-m-d H:i:s' ,$value['datetime']); 
        }    
		  $res['num'] = $count;
          $res['list'] = $datas; 
	    return $this->echo_json(0,'',$res); 
 } 
 
 
 //佣金明细， 1 一级分销 ， 2， 二级分销
	 public function commission_list()
    {
		  	 $uid = $this->uid;
			 $limit = $this->search_page(); 
			 $wheres = "";
			 $datas = "";
			 $type="";
			 
			 if($_GET['type']!=""){
					$wheres = "and type = '".$_GET['type']."'"; //， 1 一级分销 ， 2， 二级分销
			 } 
 		 
  $sqls = "SELECT * FROM kw_user_retail_commission where userid='$uid' ".$wheres." order by id desc ". $limit;
   $sqlcount = "SELECT * FROM kw_user_retail_commission where userid='$uid' ".$wheres." order by id desc ";  
			$result = Db::query($sqls);   
			$count = count(Db::query($sqlcount));   
 
	  foreach ($result as $key => $value) { 
			$datas[$key]['money'] = $value['money'];
            $datas[$key]['type'] = $value['type'];
			$datas[$key]['state'] = $value['state'];
			$datas[$key]['userid'] = $value['userid'];
			$datas[$key]['datetime'] = date('Y-m-d H:i:s' ,$value['datetime']); 
        }    
		  $res['num'] = $count;
          $res['list'] = $datas; 
	    return $this->echo_json(0,'',$res); 
 }
 
 
 //一级分销商
 
  public function subordinate()
    { 
			$uid = $this->uid;  
			$sqls = "SELECT `id` , `superiorUID`, `pic` ,`sex` ,`user_name` FROM kw_myuser where superiorUID='".$uid."' order by id desc ";
			$result = Db::query($sqls);   
			$count = count($result);   
 			$nbid = 1;
			 $userInfo = Caches::MyuserInfo($uid);
			 $datas = '';
			 	$t_msum = '';
  foreach ($result as $key => $value) { 
  
  	 	$user_pic = $value['pic'];
 
		$datas[$key]['nbid'] = $nbid++;   
		$datas[$key]['userid'] =$value['id'];
	
		 if($user_pic!=""){
	 $datas[$key]['pic'] = "//www.wisecareer.org/od/".$user_pic; 
	}else{
		$datas[$key]['pic'] = ""; 
	 }
		$datas[$key]['sex'] = $value['sex'];
		$datas[$key]['username'] = $value['user_name'];
		$datas[$key]['moneynum'] = Db::name('user_retail_commission')->where('uid',$value['id'])->where('userid',$uid)->sum('money');
		 $datas[$key]['num'] =Db::name('myuser')->where('superiorUID',$value['id'])->count();  
		 $t_msum += Db::name('user_retail_commission')->where('uid',$value['id'])->where('userid',$uid)->sum('money');
     }

	$res['num'] = $count;
	$res['username'] = $userInfo['user_name'];
	if($userInfo['pic']!=""){
		$res['pic'] = "//www.wisecareer.org/od/".$userInfo['pic']; 
	}else{
		$res['pic'] = ""; 
	 }

 //$res['usermoney'] = Db::name('user_retail_commission')->where('userid',$uid)->where('userid',$uid)->sum('money');
	$res['usermoney'] = $t_msum;
	$res['usernum'] = Db::name('myuser')->where('superiorUID',$uid)->count(); 
	$res['list'] = $datas; 
	    return $this->echo_json(0,'',$res); 
 }
 
 
  // 2， 二级分销商
	 public function subcommission()
    {
		 
			$uid = $this->uid; 
				$userid = intval($_GET['userid']); 
			 $sqls = "SELECT `id` , `superiorUID`, `pic` FROM kw_myuser where superiorUID='".$userid."' order by id desc ";
			$result = Db::query($sqls);   
			$count = count($result);   
		 
		 if($userid && $count){ 
			$nbid = 1;
			$userInfo = Caches::MyuserInfo($userid);
			$datas = '';
			$t_msum = '';
	  foreach ($result as $key => $value) { 
		  $user_pic = Db::name('myuser')->where('id',$value['id'])->value('pic');
 
		$datas[$key]['nbid'] = $nbid++;   
		$datas[$key]['userid'] =$value['id'];
			if($user_pic!=""){
				$datas[$key]['pic'] = "//www.wisecareer.org/od/".$user_pic; 
			}else{
				$datas[$key]['pic'] = ""; 
			}
	  
			$datas[$key]['username'] = Db::name('myuser')->where('id',$value['id'])->value('user_name');
			$datas[$key]['moneynum'] = Db::name('user_retail_commission')->where('userid',$uid)->where('uid',$value['id'])->sum('money');
			$datas[$key]['num'] =Db::name('myuser')->where('id',$value['id'])->count(); 
	 $t_msum += Db::name('user_retail_commission')->where('userid',$uid)->where('uid',$value['id'])->sum('money');
   	}   
	 
	$res['num'] = $count;
	$res['username'] = $userInfo['user_name'];
	if($userInfo['pic']!=""){
		$res['pic'] = "//www.wisecareer.org/od/".$userInfo['pic']; 
	}else{
		$res['pic'] = ""; 
	 } 
	$res['datetime'] = date("Y-m-d",$userInfo['datetime']);
	//$res['usermoney'] = Db::name('user_retail_commission')->where('userid',$userid)->sum('money');
	$res['usermoney'] = $t_msum;
	$res['usernum'] = Db::name('myuser')->where('superiorUID',$userid)->count(); 
	$res['list'] = $datas; 
	
	    return $this->echo_json(0,'',$res); 
			}else{
				 return $this->echo_json(32002,'',''); 
				}
 }  
	
		 //下线用户注册页面
	public function user_distributor()
  		{ 	
			 //$result=scerweima1("http://fx.wisecareer.org/index/distributor?userid=9070",123456,9070); 
		
			 
    	}  
		
		// 分销用户提现功能
		
	public function user_fx_cash()
	{   
	/*
		* 配置内容可以修改   XXXXXXXXXXXXXXXXXX替换成自己信息
		* 微信公众号配置
		* 用于微信企业付款
		
		$cpy_appid = 'wx602a56ee4a4ac69b';                  //公众号appid
		$cpy_mchid = '1485887672';                          //商户id
		$cpy_key = 'xueye9876543210wisecareerkuanwei';      //商户key
		$cpy_secret = '105e74a9d1436e5fc5f05bf07c11d0ac';   //公众号secret
		$cpy_nonce_str = time().rand(100000, 999999);   //随机字符串
		$cpy_order_str = time().rand(100000, 999999); // 唯一订单号
		$cpy_openid = 'oWnY70hcjs5G2Fk6rErBvFIUl1u8';       //公众号appid 所获取的用户openid
		*/
		   echo FxuserPay::sendMoney(1,'oWnY70hcjs5G2Fk6rErBvFIUl1u8','红包');

	 //成功
	
			exit;
	
		$uid = $this->uid; 
		$txd = input('post.txd');
		$usermoney = Db::name('user_retail')->where('uid',$uid)->value('money'); 
		
		if($txd > $usermoney){ 
			  return $this->echo_json(32001);	 
		 }else if(!empty($txd) && $txd>0){
			$insert = ['uid'=>$uid,'money'=>$txd,'type'=>2,'state'=>0,'datetime'=>$this->request->time()];
			$res = Db::name('user_retail_detail')->insert($insert);
			Db::name('user_retail')->where('uid',$uid)->setDec('money', $txd); //setInc 
			
			
			
			return $this->echo_json(0,'',$res); 
			
			
   		}else{
			return $this->echo_json(30004);	 
			} 
	}  	 
 
   //成为分销商用户 
 public function user_fx_become()
	{ 
			$uid = $this->uid; 
		 if(!input('isagree') || !is_numeric(input('isagree'))){
				    return $this->echo_json(30004);	
		}
		  $isagree = intval(input('isagree'))?1:0; 
		  $data = Db::name('user_retail')->where('uid',$uid)->value('uid'); 
		 if($data){   
		 
			 return $this->echo_json(32005);	
			 
         }else{
				$insert_data['uid']=$uid;
				$insert_data['datetime']=$this->request->time(); 
				$res = Db::name('user_retail')->insert($insert_data);  
				
  if($res){
	  
		$dataUid = Db::name('user_retail')->where('uid',$uid)->value('uid'); 
		$datadatetime = Db::name('user_retail')->where('uid',$uid)->value('datetime'); 
		$qrcode_pic_zt = scerweima1("http://fx.wisecareer.org/index/distributor?type=2&userid=".$dataUid,$datadatetime,$dataUid,2); 
		$qrcode_pic_xy = scerweima1("http://fx.wisecareer.org/index/distributor?type=1&userid=".$dataUid,$datadatetime,$dataUid,1);
		
		$update = ['uid'=>$uid,'pic'=>$qrcode_pic_zt,'xypic'=>$qrcode_pic_xy]; 
		Db::name('user_retail')->where('uid',$uid)->update($update);
		$resture['sumcount'] = Db::name('user_retail')->count(); 
		return $this->echo_json(0,'',$resture);
		   
			}else{
				return $this->echo_json(32005);	
		}   
		 } 
	} 	 
  
	
}
