<?php
namespace app\common;
use app\common\model\TyUser;
use app\common\model\UserTy;
use think\Db;
use think\facade\Cache;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Transferproblem
{
    private $uid; //用户ID
    private $tid; //套题ID
    private $time;//当前时间
    public function __construct($uid,$tid){
        $this->uid = $uid;
        $this->tid = $tid;
        $this->time = time();

    }
    //调提 方法
    public function Transfer_problem(){
        //判断体验时间
        $ass = UserTy::where('uid',$this->uid)->where('id',$this->tid)->order('id','desc')->find();
        if($ass ==null){
            return false;
        }
        //正常调提
        $tid = $ass['id'];
        $lx = $ass['lx'];
        $jb = $ass['jb'];
        /*******
        30分钟2道命运提穿插
        第二道 与 弟 10道题
        60分钟2道命运提穿插
        第2道 与 弟 10道题 30道题 40 道题
         ******/
        //调出当前 已做几道题

        $tysaveid = TyUser::where('userty',$tid)->where('huida',1)->count();
        //$tytime = checklei($tid,$this->table,id,times);
        //题总数
        $user_sum_comment = TyUser::where('userty',$tid)->count();
        $tage  = @round((($tysaveid+1)/$user_sum_comment)*100,1);//体验百分比
        //命运提出现
//        if($tysaveid == 2  or  $tysaveid == 7 ){
//            $wherelx = "and tyid = '2'";
//        }else{
//            $wherelx = "and tyid != '2'";
//        }
        //命运提出现结束
        $sql ="SELECT `tyid`,`id`,`aid` FROM kw_ty_user where uid='".$this->uid."' and userty = '".$tid."'  and huida = '0'  order by  rand() limit 1";
        //$add = $this->query($sql)->fetch(\PDO::FETCH_ASSOC);
        $add = TyUser::query($sql);
        if($add ==null){
            UserTy::where('uid',$this->uid)->where('id',$tid)->update(['valid'=>1]);
            return false;//题已经做完
            exit;
        }

        $add =  $add[0];
        $tyinfo = array(
            'tage'=>$tage,
            'tyid'=>$add['tyid'],
            'uid'=>$this->uid,
            'id'=>$add['id'],
            'tid'=>$tid,
        );
        /*案例题  */
        if($add['tyid'] == 1 ){
            $checkleirenwu = Db::name('anli')->where('id',$add['aid'])->field('renwu,wt2,wt3')->find();
            //参考素材
            //$anli_sucai  = Db::name('anli_fodder')->where('lid',$add['aid'])->value('content');
            $axx  = Db::name('anli')->where('id',$add['aid'])->field('titlename,setting,describe,renwu,pic,describesz')->find();
            $tyinfo['axx'] = $axx;
            //$tyinfo['anli_sucai'] =$anli_sucai;
            $tyinfo['wt'] = $checkleirenwu;
        }elseif($add['tyid'] == 2){
            $axv = Db::name('v_my')->where('id',$add['aid'])->field('titlename,pic')->find();
            $tyinfo['axv'] =$axv;
        }elseif($add['tyid'] == 3){
            $axs = Db::name('v_ticlass')->where('id',$add['aid'])->whereIn('cid',[2,3])->field('id,lx,titlename,sucai,pic,pics')->find();
            $tyinfo['axs'] =$axs;
            $adts = Db::name('v_ti')->where('tid',$axs['id'])->order('px','asc')->field('da,titlename')->select();
            $tyinfo['adts'] =$adts;
        }
        return $tyinfo;
        // $checklei= checklei($uid,".$tablepre."myuser_one,id,user_name);
        // $Smarty->assign('checklei',$checklei);
        // $checkuid= checkuid($myuser['id']);

        // $Smarty->assign('checkuid',$checkuid);
        //体验类型名字
    }
    //调取素材方法

    //调取积分
    public function integral(){
        $tyjifen = UserTy::where('id',$this->tid)->where('uid',$this->uid)->order('id','desc')->value('jifen');
    }

}

