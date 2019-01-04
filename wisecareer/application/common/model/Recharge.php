<?php

namespace app\common\model;

use app\common\Paydef;
use think\facade\Cache;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Recharge extends Model
{
//    private static $table  = 'user_tyks';
//    private static $table1 = 'myuser_pay';
//    private static $table2 = 'myuser_consume';
//    private static $table3 = 'myuser_account';
//    private static $time; //当前时间


    /**
     * 获取充值记录
     */
    public static function list_recharge($uid,$limit){
         return  self::name('myuser_pay')->where('uid',$uid)->field('datetime,state,pay_type,money')->order('datetime','desc')->limit($limit)->select();

    }
    /**
     * 获取消费记录
     */
    public static function list_consume($uid,$limit){

       return self::name('myuser_consume')->where('uid',$uid)->field('datetime,record,pay_type,money')->order('datetime','desc')->limit($limit)->select();

    }

    /**
     * 微信充值
     * @param $code
     */
    public static function wxpay_recharge($code){
        $datas  = self::name('myuser_pay')->where('code',$code)->find();
        $money = intval($datas['money']);
        $uid = $datas['uid']; 
		 
        $purchase =0;
        if($datas['action'] =='we'){
            $purchase = intval($datas['purchase']);
        }
		
        if($datas['state']==0){
            $has = self::name('myuser_account')->where('uid',$uid)->find();
            if($has ==null){
                $insertdata = ["money"=>($money),'uid'=>$uid,'datetime'=>time()];
                $res3= self::name('myuser_account')->data($insertdata)->insert();
            }else{
                $updata=['money'=>($money + intval($has['money']))];
                $res3= self::name('myuser_account')->where('uid',$uid)->data($updata)->update();
            }
				 
		$superiorUID = self::name('myuser')->where('id',$uid)->value('superiorUID');// 上级ID 
		$suuserid = self::name('myuser')->where('id',$superiorUID)->value('superiorUID');// 上上级ID 
		$moneysupuid =  $money * 0.25;// 上级ID 返点钱
		$moneysuserid =  $money * 0.15;// 上上级ID 返点钱
		  
		  if($superiorUID){ // 处理上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysupuid;
				$insert_data['type']=1;
				$insert_data['userid']=$superiorUID;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);   
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('commission', $moneysupuid); 
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('money', $moneysupuid); 
				
		  }
			  
		  if($suuserid){ // 处理上上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysuserid;
				$insert_data['type']=2;
				$insert_data['userid']=$suuserid;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);  
				
			 self::name('user_retail')->where('uid',$suuserid)->setInc('commission', $moneysuserid); 
		 	 self::name('user_retail')->where('uid',$suuserid)->setInc('money', $moneysuserid); 
		  }
			
			
            $auth = self::name('myuser_pay')->where('code',$code)->update(['state'=>1]);
			
			
			
        }
		
        if($datas['action'] =='we'){
            $res  =	Paydef::yu_pay($uid,$datas['purchase'],'xy_e_time');
            if($res){
                $add_consume['record'] = '微信支付VIP会员' ;
                $add_consume['code']=Paydef::ordernum();
                $add_consume['uid']=$uid;
                $add_consume['money']=$datas['purchase'];
                $add_consume['pay_type']=6;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume); 
                $smg ='微信支付VIP会员，消费 '.$datas['purchase'] .'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata);
            }
        }

        return $auth;

    }

    /**
     * 微信充值  消费
     * @param $code
     */
    public static function wxpay_recharge_meal($code){
        $datas  = self::name('myuser_pay')->where('code',$code)->find();
        $money = intval($datas['money']);
        $uid = $datas['uid'];
        if($datas['state']==0 &&  $datas['action'] =='wx'){
			
			$superiorUID = self::name('myuser')->where('id',$uid)->value('superiorUID');// 上级ID 
		$suuserid = self::name('myuser')->where('id',$superiorUID)->value('superiorUID');// 上上级ID 
		$moneysupuid =  $money * 0.25;// 上级ID 返点钱
		$moneysuserid =  $money * 0.15;// 上上级ID 返点钱
		  
		  if($superiorUID){ // 处理上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysupuid;
				$insert_data['type']=1;
				$insert_data['userid']=$superiorUID;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);   
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('commission', $moneysupuid); 
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('money', $moneysupuid); 
				
		  }
			  
		  if($suuserid){ // 处理上上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysuserid;
				$insert_data['type']=2;
				$insert_data['userid']=$suuserid;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);  
				
			 self::name('user_retail')->where('uid',$suuserid)->setInc('commission', $moneysuserid); 
		 	 self::name('user_retail')->where('uid',$suuserid)->setInc('money', $moneysuserid); 
		  }
		  
			
            $auth = self::name('myuser_pay')->where('code',$code)->update(['state'=>1]);
            $res = MyuserZtPay::where('code',$code)->where('uid',$uid)->update(['state'=>1]);

            $res1= self::name('myuser_zt_eval_pay')->where('code',$code)->where('uid',$uid)->find();
            if($res1!=null){
                self::name('myuser_zt_eval_pay')->where('code',$code)->where('uid',$uid)->update(['state'=>1]);
                $eval_num = $res1['eval_num'];
                Myuser::name('myuser_account')->where('uid',$uid)->setInc('eval_num', $eval_num);
            }
            if($res){
                $add_consume['record'] = '微信支付套餐' ;
                $add_consume['code']= $code ;
                $add_consume['uid']=$uid;
                $add_consume['money']=$money;
                $add_consume['pay_type']=7;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume);

                $smg ='微信支付套餐，消费 '.$money .'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata); 

                UserMessage::send_meal_mess($uid);

            }
        }

        return $auth;

    }

    public static function wxpay_recharge_meal_eval($code){
        $datas  = self::name('myuser_pay')->where('code',$code)->find();
        $money = intval($datas['money']);
        $uid = $datas['uid'];
        if($datas['state']==0 &&  $datas['action'] =='wx'){
			
			
			$superiorUID = self::name('myuser')->where('id',$uid)->value('superiorUID');// 上级ID 
		$suuserid = self::name('myuser')->where('id',$superiorUID)->value('superiorUID');// 上上级ID 
		$moneysupuid =  $money * 0.25;// 上级ID 返点钱
		$moneysuserid =  $money * 0.15;// 上上级ID 返点钱
		  
		  if($superiorUID){ // 处理上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysupuid;
				$insert_data['type']=1;
				$insert_data['userid']=$superiorUID;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);   
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('commission', $moneysupuid); 
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('money', $moneysupuid); 
				
		  }
			  
		  if($suuserid){ // 处理上上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysuserid;
				$insert_data['type']=2;
				$insert_data['userid']=$suuserid;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);  
				
			 self::name('user_retail')->where('uid',$suuserid)->setInc('commission', $moneysuserid); 
		 	 self::name('user_retail')->where('uid',$suuserid)->setInc('money', $moneysuserid); 
		  }
			
            $auth = self::name('myuser_pay')->where('code',$code)->update(['state'=>1]);
            $res1= self::name('myuser_zt_eval_pay')->where('code',$code)->where('uid',$uid)->find();
            if($res1){
                self::name('myuser_zt_eval_pay')->where('code',$code)->update(['state'=>1]);
                $eval_num = $res1['eval_num'];
                Myuser::name('myuser_account')->where('uid',$uid)->setInc('eval_num', $eval_num);
            }
            if($res1){
                $add_consume['record'] = '微信支付教练评价' ;
                $add_consume['code']= $code ;
                $add_consume['uid']=$uid;
                $add_consume['money']=$money;
                $add_consume['pay_type']=7;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume);
                $smg ='微信支付教练评价，消费 '.$money .'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata);
                UserMessage::send_eval_mess($uid);
            }
        }

        return $auth;

    }
    public static function wxpay_recharge_meal_en($code){
        $time = time();
        $datas  = self::name('myuser_pay')->where('code',$code)->find();
        $money = intval($datas['money']);
        $uid = $datas['uid'];
        $lastid = false;
        if($datas['state']==0 &&  $datas['action'] =='wx'){
			
			$superiorUID = self::name('myuser')->where('id',$uid)->value('superiorUID');// 上级ID 
		$suuserid = self::name('myuser')->where('id',$superiorUID)->value('superiorUID');// 上上级ID 
		$moneysupuid =  $money * 0.25;// 上级ID 返点钱
		$moneysuserid =  $money * 0.15;// 上上级ID 返点钱
		  
		  if($superiorUID){ // 处理上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysupuid;
				$insert_data['type']=1;
				$insert_data['userid']=$superiorUID;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);   
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('commission', $moneysupuid); 
		  self::name('user_retail')->where('uid',$superiorUID)->setInc('money', $moneysupuid); 
				
		  }
			  
		  if($suuserid){ // 处理上上级返点
				$insert_data['uid']=$uid;
				$insert_data['money']=$moneysuserid;
				$insert_data['type']=2;
				$insert_data['userid']=$suuserid;
				$insert_data['state']=1;
				$insert_data['datetime']=time(); 
				$auth = self::name('user_retail_commission')->insert($insert_data);  
				
			 self::name('user_retail')->where('uid',$suuserid)->setInc('commission', $moneysuserid); 
		 	 self::name('user_retail')->where('uid',$suuserid)->setInc('money', $moneysuserid); 
		  }
		  
            $auth = self::name('myuser_pay')->where('code',$code)->update(['state'=>1]);

            $myztpay= MyuserZtPay::userSetMealHas($uid);
            $tpid = $myztpay['id'];
            $userinsertpay=  Cache::get('userinsertpay'.$uid);
            $insertdata_str  = Cache::get('userlabel'.$uid);
            $insertdata_label = json_decode($insertdata_str,true);

            $code = Paydef::ordernum();
            $insertdata = ['code'=>$code,'state'=>1,'uid'=>$uid,'aisleid'=>$userinsertpay['aisleid'],'classid'=>$userinsertpay['classid'],'en_case'=>$userinsertpay['en_case'],'en_money'=>$userinsertpay['en_money'],'aislenum'=>$userinsertpay['aislenum'],'months'=>$userinsertpay['months'],'money'=>$userinsertpay['moneys'],'endtime'=>$userinsertpay['endtime'],'datetime'=>$time];

            $res = MyuserZtPay::where('id',$tpid)->data(['state'=>2])->update();
            $lastid = MyuserZtPay::insertGetId($insertdata);
            if($lastid){
                $insertdata_label = array_map(function ($arr)use($lastid){$temp['tpid']=$lastid; return  $arr = array_merge($temp,$arr);},$insertdata_label);
                $res1= MyuserZtPay::name('myuser_zt_pay_zi')->insertAll($insertdata_label);
                $add_consume['record'] = '微信支付套餐' ;
                $add_consume['code']= $code ;
                $add_consume['uid']=$uid;
                $add_consume['money']=$money;
                $add_consume['pay_type']=7;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume);
                $smg ='微信支付套餐，消费 '.$money .'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata);
                UserMessage::send_meal_mess($uid);
            }
        }

        return $lastid;

    }


}

