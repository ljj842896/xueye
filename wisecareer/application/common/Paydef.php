<?php
namespace app\common;

use think\Db;
use think\facade\Cache;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Paydef
{

    /**余额支付
     * @param $uid
     * @param $money
     * @return mixed
     */
    public static function yu_pay($uid, $money,$e_time)
    {
        $array['uid'] =$uid;
        $array['money'] =$money;
        $array['moth'] = self::get_moth($money);
        return self::delay_add($array,$e_time);
    }

    /**
     * 计算月份
     * @param $money
     * @return int
     */
    public static  function get_moth($money)
    {
        $moth = 0;
        if($money  ==270){
            $moth =12;
        }elseif($money  ==450){
            $moth = 24;
        }elseif($money ==540){
            $moth =36;
        }
        return  $moth;
    }

    /**
     * 生成订单号
     * { function_description }
     */

    public static function ordernum(){
        return "1485887672".date('YmdHis');
    }

    /**帐号延期
     * @param $array
     * @param $e_time
     * @return mixed
     */
    public static  function delay_add($array, $e_time){
        $time  = time();
        $data = Db::name('myuser_account')->where('uid',$array['uid'])->field("money,{$e_time}")->find();
        if( $time > $data[$e_time] ){
            $times = self::delay_time($array['moth']);
        }else{
            $times = self::delay_time($array['moth'],$data[$e_time] );
        }
        $money = $data['money']-$array['money'];
        $update_data = ['money'=>$money,$e_time=>$times];
        return Db::name('myuser_account')->where('uid',$array['uid'])->data($update_data)->update();
    }
    /**到期时间
     * @param $month
     * @param string $time
     * @return false|int
     */
    public static function delay_time($month, $time=''){
        if($time){
            return strtotime(date('Y-m-d',strtotime('+'.$month.'month',$time)));//指定时间戳+1年 2018-01-09 21:10:16
        }else{
            return strtotime(date('Y-m-d',strtotime('+'.$month.'month')));//当前时间戳+1月 2017-02-09 21:04:11
        }
    }

    /**
     * 消费记录
     * @param $array
     * @return int|string
     */
    public  static  function consume_add($array){
        return Db::name('myuser_consume')->data($array)->insert();
    }


    /**
     * j检验职拓卡 是否正确
     * @param $array
     * @return array|null|\PDOStatement|string|\think\Model
     */
    public static function Verification($array){

       return  Db::name('user_tyks')->where($array)->find();

    }

    /**
     * @param $data
     * @param $uid
     * @return mixed
     */
    public  static  function recharge_add($data, $uid){
        $time = time();
        $money = $data['lxtime']/2;
        $has =  Db::name('myuser_account')->where('uid',$uid)->find();
        if(!empty($has)){
            $moneys = $money + $has['money'];
            $res3 =Db::name('myuser_account')->where('uid',$uid)->update(['money'=>$moneys]);
        }else{
            $insert_data=['money'=>$money,'uid'=>$uid,'datetime'=>$time];
            $res3 = Db::name('myuser_account')->data($insert_data)->insert();
        }
        $array['cztime']=$time;
        $array['uid']=$uid;
        $array['occ']=1;
        $res2 = Db::name('user_tyks')->where('id',$data['id'])->data($array)->update();
        return $res2;
    }

    /**
     * 充值记录
     */
    public static  function add_pay($array){
        Db::name('myuser_pay')->data($array)->insert();
    }


}

