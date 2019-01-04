<?php
namespace app\fx\controller;
use app\common\Caches;
use app\common\Jssdk; 
use app\common\StringRsa;
use app\index\controller\Basics;
use think\Controller; 
use think\Db;
use think\facade\Config;  
use think\Model;

class Json extends Model
{
	
	public function qrcode()
  { 	
	   	 
		/* $uid = session('islogin'); 
			$token= StringRsa::createUserToken($uid);   //加密token
			$this->assign('token',$token);  
			return $this->fetch();  
		 */
 
		 //vendor('phpqrcode.phpqrcode');
	 echo "aaa";
		 
	   
    }  



	
	}