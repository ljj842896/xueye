<?php

namespace app\common\model;

use app\common\Paydef;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class MyuserCoupon extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }


    /**
     * 增加 代金券 金额
     * @param $uid
     * @param $id
     * @param $data
     * @return mixed
     */
    public static function gainCoupon($uid, $id, $data=''){
        $time = time();
        if(empty($data)){
            $data = Db::name('myuser_coupon')->where('id',$id)->where('open',1)->where('ks_time','<=',$time)->where('js_time','>=',$time)->find();
        }
        $money = $data['rate'];
        $has = self::name('myuser_account')->where('uid',$uid)->find();
        if($has ==null){
            $user_account = ['uid'=>$uid,'money'=>$money,'datetime'=>$time];
            $re  = self::name('myuser_account')->data($user_account)->insert();
        }else{
            $re= self::name('myuser_account')->where('uid',$uid)->setInc('money', $money);
        }
		
        if($re){
            $add_pay['code']=Paydef::ordernum();
            $add_pay['uid']= $uid;
            $add_pay['money']=$money;
            $add_pay['pay_type'] = 3 ;  // 代金券
            $add_pay['state']= 1 ;
            $add_pay['datetime']=time();
            Paydef::add_pay($add_pay);
            return true;
        }else{
            return false;
        }
    }
}

