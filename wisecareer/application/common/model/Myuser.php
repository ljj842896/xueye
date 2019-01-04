<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Myuser extends Model
{

    private  static $table1 = 'myuser_account';
    /**
     *
     */
    protected static function init()
    {

    }
    /**
     * @param $user
     * @param $passwd
     * @return array  用户信息详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function validateUser($user, $passwd){

        $data=self ::where(['password'=>$passwd])->where(function($query) use($user){
            $query->whereOr(['username'=>$user,'dianhua'=>$user]);
        })->find();
        return $data;
    }



    /**
     * 验证 用户是否注册 过
     * @param $dianhua
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function viliregUser($dianhua){
       return self::where('dianhua',$dianhua)->find();
    }

    /**
     * 会员状态
     * @param $uid
     * @param string $viptype
 */
    public static function myuserXyStatus($uid,$viptype=''){


        if($viptype==''){
            $data1 = self::whereid($uid)->field('viptype,schoolname')->find();
            $viptype =$data1['viptype'];
            $schoolid = $data1['schoolname'];
        }
        // 学校ID不存在 并且 不为0
        if(!isset($schoolid)){
            $schoolid = self::whereid($uid)->value('schoolname');
        }
        if($viptype==1){
            $xy_e_time = self::name('shcool')->whereid($schoolid)->value('xy_e_time');
            if( $xy_e_time> time()){
                $result['status']  = 2;
                $result['xy_e_time']  = $xy_e_time ;
                return $result;
            }
        }

        $data = self::name(self::$table1)->where('uid',$uid)->find();
        if($data && $data['xy_e_time'] >time()){
            $result['status']  = 1;
            $result['xy_e_time']  = $data['xy_e_time'] ;
            return $result;
        } 
		
        $result['status']  = 0;
        $result['xy_e_time']  = empty( $data['xy_e_time'] )? '': $data['xy_e_time'];
        return $result;


    }
    /**
     * 职拓会员
     * @param $uid
     * @param string $viptype
     */
    public static function myuserZtStatus($uid,$viptype=''){
        if($viptype==''){
            $data1 = self::whereid($uid)->field('viptype,schoolname')->find();
            $viptype =$data1['viptype'];
            $schoolid = $data1['schoolname'];
        }
        // 学校ID不存在 并且 不为0
        if(!isset($schoolid)){
            $schoolid = self::whereid($uid)->value('schoolname');
        }
        if($viptype==1){
            $xy_e_time = self::name('shcool')->whereid($schoolid)->value('zt_e_time');
            if( $xy_e_time> time()){
                $result['status']  = 2;
                $result['zt_e_time']  = $xy_e_time ;
                return $result;
            }
        }

//        $data = self::name(self::$table1)->where('uid',$uid)->find();
//        if($data && $data['zt_e_time'] >time()){
//            $result['status']  = 1;
//            $result['zt_e_time']  = $data['zt_e_time'] ;
//            return $result;
//        }

        $myztpy = MyuserZtPay::userSetMealHas($uid);

        if($myztpy ==false){
            $result['status']  = 0;
            $result['zt_e_time']  = '';
            return $result;
        }
        $result['status']  = 1;
        $result['zt_e_time']  = $myztpy['endtime'] ;
        return $result;


    }

    /**
     * 帐号余额
     */
    public static function userBalance($uid){
        return self::name(self::$table1)->where('uid',$uid)->value('money');
    }

    /**扣费
     * @param $array
     * @return int|true
     * @throws \think\Exception
     */
    public static function dec_money($array)
    {
        return self::name(self::$table1)->where('uid',$array['uid'])->setDec('money', $array['money']);
    }

    /**退费
     * @param $array
     * @return int|true
     * @throws \think\Exception
     */
    public static function inc_money($array)
    {
        return self::name(self::$table1)->where('uid',$array['uid'])->setInc('money', $array['money']);
    }


    //学生平均分
    public static function avg_score($uid)
    {
        $sql = "SELECT avg(case scores2 when 0 then null else scores2 end) as scores2, avg(case scores5 when 0 then null else scores5 end) as scores5 , avg(case scores4 when 0 then null else scores4 end) as scores4 , avg(case scores8 when 0 then null else scores8 end) as scores8 , avg(case scores9 when 0 then null else scores9 end) as scores9 , avg(case scores10 when 0 then null else scores10 end) as scores10 , avg(case scores15 when 0 then null else scores15 end) as scores15, avg(case scores17 when 0 then null else scores17 end) as scores17, avg(case scores27 when 0 then null else scores27 end) as scores27 ,avg(case perc2 when 0 then null else perc2 end) as perc2,avg(case perc4 when 0 then null else perc4 end) as perc4,avg(case perc17 when 0 then null else perc17 end) as perc17,avg(case perc5 when 0 then null else perc5 end) as perc5,avg(case perc8 when 0 then null else perc8 end) as perc8,avg(case perc9 when 0 then null else perc9 end) as perc9,avg(case perc10 when 0 then null else perc10 end) as perc10,avg(case perc15 when 0 then null else perc15 end) as perc15,avg(case perc27 when 0 then null else perc27 end) as perc27 from kw_user_scores WHERE uid = {$uid} ";

        $data = self::query($sql)[0];

        $data['datetime']= time();
        $has =  self::name('user_scores_avg')->where('uid',$uid)->find();
        if(!empty($has)){
            return self::name('user_scores_avg')->where('uid',$uid)->data($data)->update();
        }else{
            $data['uid'] = $uid;
            return self::name('user_scores_avg')->data($data)->insert();
        }

    }

    //用户案例标签 调取类


}

