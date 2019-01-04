<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class MyuserZtPay extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

	/**
	 *用户是否购买套餐
	 */
	public static function userSetMealHas($uid){
		$time = time();
		$data =self::where('uid',$uid)->where('state',1)->where('endtime','>',$time)->order('id','desc')->find();
		if($data!=null){
			return $data;
		}
		return false;
	} 

    /**
     *用户是否购买套餐
     */
    public static function userEvalHas($uid){

        $data =self::name('myuser_zt_eval_pay')->where('uid',$uid)->where('state',1)->order('id','desc')->find();

        if($data!=null){
            return $data;
        }
        return false;
    }

    /**
     * 获取 开通的 子通道
     * @return mixed
     */
    public static function getztpayzi($uid,$tpid){
        $indata = [];
        $hasdata = self::name('myuser_zt_pay_zi')->where('tpid',$tpid)->where('uid',$uid)->field('type,tid')->select();

        if($hasdata==null){  return false; }
        foreach ($hasdata as $k =>$v){
            $indata[$v['type']][]=$v['tid'];
        }
        return $indata;
    }
}

