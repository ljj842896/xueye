<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Evaluate extends Model
{
    private static $table5 ='app_user_evaluate';
    private static $table1 ='ty_user';
    private static $table2 ='user_ty';
    private static $table3 ='ty_user_jifen';
    private static $table4 ='ty_user_defen';

    //
    /**评价案例数
     * @param $uid
     * @return int|string
     */
    public static function user_evaluate_num($uid){
        return self::name(self::$table5)->where('userid',$uid)->count();
    }

    /**
     * @param $uid
     * @return int|string
     */
    public static function user_evaluate_xin_num($uid){
        return self::name(self::$table5)->where('checks',0)->where('userid',$uid)->count();
    }

    /**给学员增加积分
     * @param $uid
     * @param $jifen
     */
    public static function add_jifen($uid, $jifen)
    {
        $time = time();
        return self::name(self::$table3)->insert(['uid'=>$uid,'jifen'=>$jifen,'type'=>3,'datatime'=>$time]);
    }

    /**关注学员  推荐 答案
     * @param $uid
     * @return array
     */
    public  static  function user_atten_recom($uid)
    {
        $result = [];
        $result['atten'] =  self::name('app_user_recommend')->where('userid',$uid)->where('attention',1)->count();
        $result['recom_eval'] =self::name('app_user_recom_evaluate')->where('userid',$uid)->count();
        return $result;
    }

    /**
     * @param $id
     * @param $uid
     * @return static
     * @throws \think\exception\DbException
     */
    public static function recom($id, $uid)
    {
        $recom = AppUserEvaluate::where(['id'=>$id,'userid'=>$uid])->value('recom');
        if($recom !=null){
            return AppUserEvaluate::where(['id'=>$id,'userid'=>$uid])->update(['recom'=>0]);
        }else{
            return AppUserEvaluate::where(['id'=>$id,'userid'=>$uid])->update(['recom'=>1]);
        }
    }

    /**评价案例
     * @param $uid
     * @param $type
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_user_evaluate($uid, $type){
        $data = self::name(self::$table5)->alias('t1')
            ->leftJoin('center_user t2','t1.uid = t2.id')
            ->where(['t1.userid'=>$uid,'t1.type'=>$type])
            ->field('t1.taid,t1.type,t1.aid,t1.content,t1.pingfen,t1.recom,t2.oid,t1.checks,t2.uid,t2.id,t1.id as evid')
            ->order('t1.datetime','desc')
            ->select();

        if($data ==null){
            return false;
        }

        foreach ($data as $key => $value) {
            if(!$value['checks']){
                AppUserEvaluate::where('id',$value['evid'])->update(['checks'=>1]);
            }
            $atten = AppUserEvaluate::has_user_attenrecom($value['id'],$uid);

            if($atten !=null){
                $data[$key]['attention'] = $atten['attention'];
                $data[$key]['recommend'] = $atten['recommend'];
            }else{
                $data[$key]['attention'] = 0;
                $data[$key]['recommend'] =0;
            }
            if($value['oid'] ==1){
                $dataname = self::name('school_useradmin')->where('id',$value['uid'])->value('user_name');
                $data[$key]['user_name'] = $dataname;
                $data[$key]['grades'] = ' ';
            }elseif($value['oid'] ==2){
                $dataname = self::name('app_member')->where('id',$value['uid'])->field('grades,user_name,pic')->find();
                $data[$key]['user_name'] = $dataname['user_name'];
                $data[$key]['pic'] = $dataname['pic'];
                $data[$key]['grades'] = grade($dataname['grades']);
            }

            $temp = TyUser::where(['id'=>$value['taid'],'uid'=>$uid])->field('qita,daname')->find();
            $data[$key]['qita']=$temp['qita'];
            $data[$key]['daname']= iconv('gbk', 'utf-8',urldecode($temp['daname']))  ;

            if($value['type'] == 1){
                $data[$key]['titlename'] = self::name('anli')->where('id',$value['aid'])->value('titlename');
            }elseif($value['type']==2){
                $data[$key]['titlename'] = self::name('v_ticlass')->where('id',$value['aid'])->value('titlename');
            }
        }
        return $data;
    }

    /**
     * @param $uid
     * @param $tyuser_id
     * @return bool|null|static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function user_evaluate_detail($uid, $tyuser_id){
        $data = TyUser::where(['id'=>$tyuser_id,'uid'=>$uid])->field('aid,daname,qita,tyid')->find();

        if($data ==null){
            return false;
        }
        if($data['tyid'] == 1){
            $res['detail'] = self::name('anli')->where('id',$data['aid'])->field('titlename,setting,describe,pic,renwu,wt2,wt3')->find();
        }elseif($data['tyid']==3){
            $res['detail']['ticlass'] = self::name('v_ticlass')->where('id',$data['aid'])->field('titlename,pic')->find();
            $res['detail']['ti'] = self::name('v_ti')->where('tid',$data['aid'])->field('da,titlename')->order('px','asc')->select();
        }
        $data['daname'] = iconv('gbk', 'utf-8',urldecode($data['daname']))  ;
        $result = array_merge($res,$data->toArray());
        return $result;
    }

    /**体验 报告
     * @param $uid
     * @param $tid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function experience_report($uid, $tid)
    {
        $data = UserTy::where(['id'=>$tid,'uid'=>$uid])->field('stopdate,jifen,datetime,ztid,hangye,lx,zdefen,defen,tysj')->find();
        if($data == null){
            return false;
        }
        $result['stopdate'] = @number_format(($data['stopdate']-$data['datetime'])/60,1);

        $anlidata = Caches::HangYeClass();
        $lxdata = Caches::LxClass();
        $vtxdata = Caches::ZhuanTiClass();

        $anlitpids = array_column($anlidata,'tpid');
        $anlitpnames= array_column($anlidata,'tpname');
        $anlidatas = array_combine($anlitpids,$anlitpnames);

        $lxids = array_column($lxdata,'id');
        $lxtpnames = array_column($lxdata,'tpname');
        $lxdatas = array_combine($lxids,$lxtpnames);

        $vlxids = array_column($vtxdata,'id');
        $vlxtpnames = array_column($vtxdata,'tpname');
        $vtxdatas = array_combine($vlxids,$vlxtpnames);

        if($data['ztid']>0){
            $result['tpname'] = $vtxdatas[$data['ztid']];
        }elseif($data['hangye']>0){
            $result['tpname'] = $anlidatas[$data['hangye']];
        }else{
            $result['tpname'] = $lxdatas[$data['lx']];
        }

        $result['jifen']= self::zongjifen($uid,$tid);
        $result['correctpro']= @number_format(($data['defen']/$data['zdefen'])*100);
        $result['sucaipro']= self::user_sucai($uid,$tid);
        $result['guandian']= self::guandian($uid,$tid);
        return $result;
    }


    /**
     *
     * 获取体验的 主题
     * @param $data
     * @return mixed
     */
    public static function get_tpname($data){
        $anlidata = Caches::HangYeClass();
        $lxdata = Caches::LxClass();
        $vtxdata = Caches::ZhuanTiClass();

        $anlitpids = array_column($anlidata,'tpid');
        $anlitpnames= array_column($anlidata,'tpname');
        $anlidatas = array_combine($anlitpids,$anlitpnames);

        $lxids = array_column($lxdata,'id');
        $lxtpnames = array_column($lxdata,'tpname');
        $lxdatas = array_combine($lxids,$lxtpnames);

        $vlxids = array_column($vtxdata,'id');
        $vlxtpnames = array_column($vtxdata,'tpname');
        $vtxdatas = array_combine($vlxids,$vlxtpnames);

        if($data['ztid']>0){
            $result['tpname'] = $vtxdatas[$data['ztid']];
        }elseif($data['hangye']>0){
            $result['tpname'] = $anlidatas[$data['hangye']];
        }else{
            $result['tpname'] = $lxdatas[$data['lx']];
        }
        return $result;
    }
    /**
     * 总积分
     * @param $uid
     * @param $tid
     * @return float|int|mixed|string
     */
    public static function zongjifen($uid, $tid)
    {
        return  self::zjifen($uid,$tid) + self::guandian($uid,$tid) * 2  - self::sucaifen($uid,$tid,1) + self::user_jifen($uid,$tid);
    }
    /**素材
     * @param $uid
     * @param $tid
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function user_sucai($uid, $tid){
        $data = TyUser::where(['uid'=>$uid,'userty'=>$tid,'sucaiid'=>1])->column('sucaiod');
        if($data ==null){
            return 0;
        }
        $num = count($data);
        $sunum = 0;
        foreach($data as $value){
            $sunum += $value?1:0;
        }
        return @number_format($sunum/$num*100);
    }

    /**素材分  ,个数
     * @param $uid
     * @param $tid
     * @param $od
     * @return float|int|string
     */
    public static function sucaifen($uid, $tid, $od){
        if($od){
            return TyUser::where(['uid'=>$uid,'userty'=>$tid,'sucaiid'=>1,'sucaiod'=>1])->sum('sucaijifen');
        }else{
            return TyUser::where(['uid'=>$uid,'userty'=>$tid,'sucaiid'=>1,'sucaiod'=>1])->count();
        }
    }

    /** 积分
     * @param $uid
     * @param $tid
     * @return float|int
     */
    public static function user_jifen($uid, $tid){
        return self::name('ty_user_jifen')->where(['uid'=>$uid,'userty'=>$tid])->sum('jifen');
    }

    /**
     * 得分
     * @param $uid
     * @param $tid
     * @return float|int
     */
    public static function user_defen($uid, $tid){
        return self::name('ty_user_defen')->where(['uid'=>$uid,'userty'=>$tid])->sum('defen');
    }

    //观点数
    public static function guandian($uid,$tid){
        return TyUser::where('uid',$uid)->where('userty',$tid)->where('qita','neq','')->count('defen');
    }

    /**
     * 积分
     * @param $uid
     * @param $tid
     * @return int|mixed
     */
    public static function zjifen($uid, $tid){
        $data =UserTy::where(['id'=>$tid,'uid'=>$uid])->value('jifen');
        if($data != null){
            return $data;
        }else{
            return 0;
        }
    }
    // i观点  主观题得分
    public static function eval_defens($userid){
        return self::name('app_user_evaluate')->where('userid',$userid)->sum('pingfen');
    }

    // 被关注
    public static function rep_atten($userid)
    {
        $num1 = self::name('app_user_recommend')->where('userid',$userid)->where('attention',1)->count();
        $num2 = self::name('student_attention')->where('userid',$userid)->count();
        return $num1+$num2;
    }

    // 被推荐
    public static function rep_recom($userid)
    {
        $num1 = self::name('app_user_recommend')->where(['userid'=>$userid,'recommend'=>1])->count();
        $num2 = self::name('student_recommen')->where('userid',$userid)->count();
        return $num1+$num2;
    }

    //  总积分分
    public static function rep_integral($userid)
    {
        $sum1 =self::name(self::$table2)->where('uid',$userid)->sum('jifen');
        $sum2 =self::name(self::$table3)->where('uid',$userid)->sum('jifen');
        // $sql = "SELECT id,jifen FROM `kw_user_ty` WHERE uid = 1942";
        // $data = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        // foreach ($data as $key => $value) {
        // 	$sql1 ="SELECT sum(truncate(jifen,3) ) as su  FROM `kw_ty_user` WHERE uid = 1942 and userty ={$value['id']}";
        // 	$data1 = $this->query($sql1)->fetch(\PDO::FETCH_ASSOC);
        // 	$data[$key]['su']  = $data1['su'];
        // }

        return round($sum1 + $sum2,2) ;
    }
    //今日积分
    public static function dotay_intergral($userid){
        $start = start_time();
        $end = end_time();
        $sum1 =self::name(self::$table2)->where('uid',$userid)->where([['datetime' ,'<',$end],['datetime','>',$start]])->sum('jifen');
        $sum2 =self::name(self::$table3)->where('uid',$userid)->where([['datatime' ,'<',$end],['datatime','>',$start]])->sum('jifen');
        return round($sum1 + $sum2,2) ;
    }

    //今日每个人的积分
    public static function dotay_intergral_group(){
        $start = start_time();
        $end = end_time();
        $sum1 =self::name(self::$table2)->where([['datetime' ,'<',$end],['datetime','>',$start]])->sum('jifen');
        $sum2 =self::name(self::$table3)->where([['datatime' ,'<',$end],['datatime','>',$start]])->sum('jifen');
        return round($sum1 + $sum2,2) ;
    }


    //胜任力
    public static function competency($userid)
    {
        $sum1 =self::name(self::$table2)->where('uid',$userid)->sum('zdefen');
        $sum2 =self::name(self::$table2)->where('uid',$userid)->sum('defen');
        $sum3 =self::name(self::$table4)->where('uid',$userid)->sum('defen');

        $sum = @round(((($sum3 + $sum2 )/$sum1)*100),2);

        return $sum;
    }


}

