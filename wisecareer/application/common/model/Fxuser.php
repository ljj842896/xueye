<?php

namespace app\common\model;

use think\Model;
use think\Db;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Fxuser extends Model
{
   private  static $table1 = 'user_retail'; 
    /**
     *
     */
    protected static function init()
    {

    } 
	 /**
     * 是否分销商
     */
	 public static function userretailid($uid){ 
			 return self::name(self::$table1)->where('uid',$uid)->value('uid'); 
	 }
	 
	 /**
     * 分销商用户信息
     */
    public static function userretail($uid){ 
			$data = self::name(self::$table1)->where('uid',$uid)->find(); 
			$two_msum = '';
			$two_count = '';
 if($data){
			$result['commission'] =$data['commission'];  //总佣金
			$result['money'] = $data['money'];   //用户余额
			$result['qrcode'] = $data['pic'];   //职拓二维码地址
			$result['xy_qrcode'] = $data['xypic'];   //学业二维码地址
			$result['datetime'] =  $data['datetime']?date('Y-m-d ',$data['datetime']):''; 			
			$result['one_moneysum'] = Db::name('user_retail_commission')->where('userid',$uid)->sum('money');
			$result['one_count'] =Db::name('myuser')->where('superiorUID',$uid)->count();
			$sqls = "SELECT * FROM kw_user_retail where superiorUID='".$uid."' order by id desc ";
			$resultdata = Db::query($sqls);   
		foreach ($resultdata as $key => $value) {  
			$two_msum += Db::name('user_retail_commission')->where('userid',$value['uid'])->sum('money'); 
		$two_count +=Db::name('myuser')->where('superiorUID',$value['uid'])->count();  
		}  
		
		$result['two_moneysum'] =$two_msum;
		$result['two_count'] = $two_count;

 }else{
		$result ="";   
}
			 
			 
		  return $result; 
    }
	
	
/*****
微信分销提现
*****/
 /**
 * 	作用：格式化参数，签名过程需要使用
 */
    public static function formatBizQueryParaMap($paraMap, $urlencode)
{
	var_dump($paraMap);//die;
	$buff = "";
	ksort($paraMap);
	foreach ($paraMap as $k => $v)
	{
		if($urlencode)
		{
			$v = urlencode($v);
		}
		//$buff .= strtolower($k) . "=" . $v . "&";
		$buff .= $k . "=" . $v . "&";
	}
	$reqPar;
	if (strlen($buff) > 0)
	{
		$reqPar = substr($buff, 0, strlen($buff)-1);
	}
	var_dump($reqPar);//die;
	return $reqPar;
}

/**
 * 	作用：生成签名
 */
    public static function getSign($Obj)
{
	var_dump($Obj);//die;
	foreach ($Obj as $k => $v)
	{
		$Parameters[$k] = $v;
	}
	//签名步骤一：按字典序排序参数
	ksort($Parameters);
	$String = self::formatBizQueryParaMap($Parameters, false);
	//echo '【string1】'.$String.'</br>';
	//签名步骤二：在string后加入KEY
	$String = $String."&key=xueye9876543210wisecareerkuanwei";
	//echo "【string2】".$String."</br>";
	//签名步骤三：MD5加密
	$String = md5($String);
	//echo "【string3】 ".$String."</br>";
	//签名步骤四：所有字符转为大写
	$result_ = strtoupper($String);
	//echo "【result】 ".$result_."</br>";
	return $result_;
}

	 

}

