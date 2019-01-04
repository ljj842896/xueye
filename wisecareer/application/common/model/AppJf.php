<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class AppJf extends Model
{
    private  static $table1 ='app_user_jifen_class';

    /**
     * @param $id
     * @param $uid
     * @param string $userid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function add_jf($id, $uid, $userid = ''){
        $res = self::name(self::$table1)->where('id',$id)->field('cid,fenshu,content')->find();
        //$inser_id =	$this->insert($this->table);
        self::insert(['classid'=>$res['cid'],'classdid'=>$id,'uid'=>$uid,'userid'=>$userid,'jifen'=>$res['fenshu'],'datetime'=>time()]);
        return $res['content'];
    }
    /**教师总积分
     * @param $uid
     * @param string $classdid
     * @return float|int
     */
    public static function get_jf($uid, $classdid =''){
        $where['uid'] = $uid;
        if($classdid){
            $where['classdid'] =$classdid;
        }
        return self::where($where)->sum('jifen');
    }
    /**积分明细
     * @param $uid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function jf_detaile($uid){
        $data =  self::alias('aj')->leftJoin('app_user_jifen_class jc','jc.classid = aj.id')
            ->where('aj.uid',$uid)
            ->order('datetime','desc')
            ->select();
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function get_jf_class($id){
        return self::name(self::$table1)->where('id',$id)->value('fenshu');
    }

    //教累计教龄
    public static function Sum_ages($uid){
        $times = time();
        $datetime = self::name('center_user')->where('id',$uid)->value('datetime');
        $ages = floor(($times - $datetime)/60/60/24/30);
        return $ages;
    }

    /**
     *学员关注数
     * @param $uid
     * @return float|int
     */
    public static function Sum_atten($uid){
        $attention = self::name('app_user_recommend')->where(["uid" => $uid ,'attention'=> 1])->value('attention');
        return $attention;
    }

    /**学员新增案例评价
     * @param $uid
     * @return float|int
     * @throws \think\exception\DbException
     */
    public static function Sum_evaluate($uid){
        $times = time();  //当前时间
        $date = self::name('app_grades_record')->where('uid',$uid)->order('id','desc')->value('datetime');
        $attention = AppUserEvaluate::where('uid',$uid)->where('datetime','>=',$date)->where('datetime','<',$times)->count();
        //案例评价数量
        return $attention;
    }

    /**新增提交新案例
     * @param $uid
     * @return int|string
     */
    public static function Sum_new($uid){
        $times = time();  //当前时间
        $data = self::name('app_grades_record')->where('uid',$uid)->order('id','desc')->value('datetime');
        if($data == null){
            $data = 0;
        }
        $attanli = self::name('anli_copy')->where([['uid','=',$uid],['state','neq',0],['datetime','>=',$data],['datetime','<',$times]])->count();
        $attti = self::name('v_ticlass_copy')->where('uid',$uid)->where('state','neq',2)->where('datatime','>=',$data)->where('datatime','<',$times)->count();
        //案例评价数量
        return $attanli+$attti;
    }

    //新增学员点赞
    public static function Sum_praise($uid){
        $times = time();  //当前时间
        $date = self::name('app_grades_record')->where('uid',$uid)->order('id','desc')->value('datetime');
        $attention = self::name('app_user_like')->where('uid',$uid)->where('recommend_time','>=',$date)->where('recommend_time','<',$times)->count();
        //学员数量
        return $attention;
    }

    //用户晋级
    public  static  function grades($uid=0){
        /*
            晋级指标	1级	晋1.5级	晋2级	晋2.5级	晋3级	晋3.5级	晋4级	晋4.5级	晋5级
            累计积分	----	300	600	1000	1500	2000	2500	3000	4000     jifen
            累计教龄	----	0	6个月	1年12 	1.5年18	 2年24	2.5年30  	3年 36 	 3.5年 42            ages
            学员关注数	----	10人	30人	60人	100人	150人	200人	250人	300人    atten
            新增案例评价	----	+ 30	+ 30	+ 30	+ 30	+ 30	+ 30	+ 30	+ 30   evaluate
            提交新案例	----	0	0	+ 3	+ 5	+ 5	+ 8	+ 8	+ 8                new
            新增学员点赞	----	0	+ 5	+ 5	+ 5	+ 5	+ 5	+ 5	+ 5              praise
        */

        $jifen =   self::get_jf($uid);
        $ages =  self::Sum_ages($uid);
        $atten =  self::Sum_atten($uid);
        $evaluate =  self::Sum_evaluate($uid);
        $new =  self::Sum_new($uid);
        $praise = self::Sum_praise($uid);

        if( $jifen>= 3500  and  $ages >= 42  and  $evaluate >=30 and $new >= 8 and $praise>=5){
            $num = 5;
        }elseif( $jifen>= 3000  and  $ages >= 36   and $evaluate >=30 and $new >= 8 and $praise>=5){
            $num = 4.5;
        }elseif( $jifen>= 2500  and  $ages >= 30  and   $evaluate >=30 and $new >= 8 and $praise>=5){
            $num = 4;
        }elseif( $jifen>= 2000  and $ages >= 24 and   $evaluate >=30 and $new >= 5 and $praise>=5){
            $num = 3.5;
        }elseif( $jifen>= 1500  and  $ages >= 18  and   $evaluate >=30 and $new >= 5 and $praise>=5){
            $num = 3;
        }elseif( $jifen>= 1000  and  $ages >= 12  and  $evaluate >=30 and $new >= 3 and $praise>=5 ){
            $num = 2.5;
        }elseif(  $jifen>= 600  and  $ages >= 6  and   $evaluate >=30 and $praise>=5 ){
            $num = 2;
        }elseif( $jifen>= 300  and  $ages >= 0  and   $evaluate >=30 ){
            $num = 1.5;
        }else{
            $num = 1;
        }
        return $num;
    }

    public static function add_grades_record($array){
        $array['datetime'] = time();
        return self::name('app_grades_record')->insert($array);
    }


}

