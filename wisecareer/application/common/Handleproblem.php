<?php
namespace app\common;
use app\common\model\TyUser;
use app\common\model\UserTy;
use think\Db;
use think\facade\Cache;

/**
 * Class Myuser   处理题
 * @package app\common\model
 */
class Handleproblem
{
    private $uid; //用户
    private $tid; //套题
    private $id; //题Id
    private $table3 ="ty_user_jifen"; //表2
    private $time; //当前时间


    public function __construct($uid,$tid,$id){
        $this->uid = $uid;
        $this->tid = $tid;
        $this->id = $id;
        $this->time = time();


    }

    //答案处理方法
    public function Handle($shop_content='',$daname='',$qita =''){
        if(!$this->id){
            return false;
        }
        /********提交答案结束******/
        $ty_user_info = TyUser::where('id',$this->id)->field('aid,tyid')->find();
        $tyid = $ty_user_info['tyid'];
        $aid = $ty_user_info['aid'];
        $tysave = [];
        //开放式案例题
        if($tyid==1){
            //案例题
            $anliinfo = Db::name('anli')->where('id',$aid)->field('jifen,describesz')->find();
            $defen = $anliinfo['jifen'] /2; //; // 本题得分 为本题分一半
            $tifen =  $anliinfo['jifen'];  //checklei($aid,$tablepre.anli,id,jifen); //本题分
            $sucaiod = TyUser::whereId($this->id)->value('sucaijifen');
            $siz =  $anliinfo['describesz'];//checklei($aid,$tablepre.anli,id,describesz);  //限制字符
            $shopnamec = urlencode($shop_content);
            $descnut = strlen($shop_content);
            //$upsiz = ceil($descnut/2); //计算字符
            /*判断输入内容*/
            if($siz>$descnut){
                return 'siz';
                exit;
            }
            $dfl = @number_format($defen/($tifen)*100); //得分率
            $has_huida = TyUser::where('id',$this->id)->where('aid',$aid)->where('huida',1)->find();
            if($has_huida == null) {
                TyUser::where(['id'=>$this->id,'aid'=>$aid,'uid'=>$this->uid,'userty'=>$this->tid])->data(['dttime'=>$this->time,'huida'=>1,'notin'=>2,'daname'=>$shopnamec,'jifen'=>$defen,'defen'=>$defen,'valid'=>1])->update();
                UserTy::where('id',$this->tid)
                    ->where('uid',$this->uid)
                    ->inc('jifen',$defen)
                    ->inc('defen',$defen)
                    ->inc('zjifen',$tifen)
                    ->inc('zdefen',$tifen)
                    ->data(['stopdate'=>$this->time])
                    ->update();

            }
            $tyanjf = $defen-$sucaiod;//积分
            //得分率函数
            $tysave = $this->dfl($tyanjf,$tysave);
            $tysave['altijifen'] = $tyanjf;//积分
            $tysave['altydefen'] = $defen; //得分
            $tysave['altydefen_rate'] = '50%'; //得分率
            $tysave['sucaiod'] =0; 	 // 素材扣分
            $tysave['viewstr'] =''; 	 // 素材扣分
            // 处理完毕
            if($sucaiod>0){
                $tysave['sucaiod'] = $sucaiod;
            }

            return $tysave;

        }//开放式案例题结束

        //计算选择题
        if($tyid==3 or $tyid==4){
            /******对选题答案*******/

            if(!$daname){
                return false;
            }

            if($daname!=""){
                if(is_array($daname)){
                    $idsdaname = implode('',$daname);
                }else{
                    $idsdaname = $daname;
                }
            }


            //思维题3// 4知识题

            $codeQita =strlen( trim($qita))?1:0;//判断I 观点是否提交
            $v_ticlass = Db::name('v_ticlass')->where('id',$aid)->field('da,lie,jifen,lx')->find();
            $da = $v_ticlass['da']; //调取答案
            $lie = $v_ticlass['lie']; //调取测谎项
            $jifen = $v_ticlass['jifen'];//调取积分 题分
            $lx = $v_ticlass['lx'];  //调取题类型

            $numti = Db::name('v_ti')->where('tid',$aid)->count();
            $sucaiod = TyUser::where('id',$this->id)->value('sucaijifen');
            if($da==""){
                if($codeQita){
                    $tyanjf = $jifen-$sucaiod+2; //获得当前积分
                    $viewstr = '提出 i-观点（1）条。';
                    $qita = $qita;
                }else{
                    $tyanjf = $jifen-$sucaiod; //获得当前积分
                    $viewstr = '';
                    $qita = '';
                }

                $has_huida = TyUser::where('id',$this->id)->where('aid',$aid)->where('huida',1)->find();
                if(!$has_huida) {
                    TyUser::where(['id'=>$this->id,'aid'=>$aid,'uid'=>$this->uid,'userty'=>$this->tid])->data(['dttime'=>$this->time,'qita'=>$qita,'huida'=>1,'notin'=>1,'daname'=>$idsdaname,'jifen'=>$tyanjf,'defen'=>$jifen,'valid'=>1])->update();
                    UserTy::where('id',$this->tid)
                        ->where('uid',$this->uid)
                        ->inc('jifen',$tyanjf)
                        ->inc('defen',$jifen)
                        ->inc('zjifen',$jifen)
                        ->inc('zdefen',$jifen)
                        ->data(['stopdate'=>$this->time,])
                        ->update();
                }

                $dfl = @number_format(($jifen/$jifen)*100); //得分率
                //得分率函数
                $tysave = $this->dfl($dfl,$tysave);
                $tysave['altijifen'] = $tyanjf;	//积分
                $tysave['altydefen'] = $jifen; //得分
                $tysave['altydefen_rate'] = $dfl.'%'; //得分
                $tysave['viewstr'] = $viewstr;
                $tysave['sucaiod'] =0; 	 // 素材扣分
                if($sucaiod>0){
                    $tysave['sucaiod'] = $sucaiod;
                }
                return $tysave;
                exit;
                //本题共获得   积分。得分为   。提出i-观点1条。参考素材查询扣0.5分
            }else{
                //答案不为空
                if($lx==1){
                    //1单选题
                    if($da==$idsdaname){
                        $notin = "1";  //对
                        $defen = $jifen;
                        $jifen  =  $jifen;
                    }else{
                        $notin = "2";	//错
                        $defen = "0";
                        $jifen  = "0";
                    }

                }else{
                    //调取多选

                    $t = $numti; //选项
                    $tf = $jifen; //提分
                    $a = $idsdaname;  //用户提交的答案
                    $b = $da;  //试卷标准正确答案

                    //答案错误2个 应为 提交 里面 ce 答案没有  错误2个 二标准答案中 ce 没有选择 也错误2 个 一共错误 2 个
                    //这个题一共得分是 (6-2)*(12/6) = 2分
                    $a1 = str_split($a); //print_r($a1);
                    $b1 = str_split($b); //print_r($b1);
                    $c = array_diff($a1, $b1);  //计算差集 zou
                    $d =  array_diff($b1, $a1);  //计算差集 zou
                    $cc = count($c);
                    $dd = count($d);
                    $bane = $cc+$dd;
                    $dtdd = $tf/$t; //一个选项的分数
                    $dt=number_format($dtdd, 3);
                    //这个题的 错误选择应该是5 个

                    $defen = sprintf("%.2f", ($t-$bane)*$dt);
                    if($lie!=""){
                        if(preg_match("/".$lie."/",$idsdaname)) //测谎选择
                        {
                            $defen = 0;  // 默认给 0
                        }
                    }
                    if($bane==0){
                        $notin = "1";  //对
                    }else{
                        $notin = "2";//错
                    }
                }
                //积分

                if($codeQita){
                    $qita =$qita;
                    $tsjifen = $defen-$sucaiod+2; //获得当前积分
                    $viewstr = '提出 i-观点（1）条。';
                }else{
                    //获得当前积分
                    $tsjifen = $defen-$sucaiod; //获得当前积分
                    $viewstr = '';
                    $qita = '';
                }

                $has_huida = TyUser::where('id',$this->id)->where('aid',$aid)->where('huida',1)->find();
                if(!$has_huida) {
                    TyUser::where(['id'=>$this->id,'aid'=>$aid,'uid'=>$this->uid,'userty'=>$this->tid])->data(['dttime'=>$this->time,'qita'=>$qita,'huida'=>1,'notin'=>$notin,'daname'=>$idsdaname,'jifen'=>$tsjifen,'defen'=>$defen,'valid'=>1])->update();
                    UserTy::where('uid',$this->uid)->where('id',$this->tid)->inc('jifen',$tsjifen)->inc('defen',$defen)->inc('zjifen',$jifen)->inc('zdefen',$jifen)->data('stopdate',$this->time)->update();

                }

                $dfl = @number_format(($defen/$jifen)*100); //得分率
                //得分率函数
                $tysave = $this->dfl($dfl,$tysave);
                $tysave['altijifen'] = $tsjifen;
                $tysave['altydefen'] = $defen;
                $tysave['altydefen_rate'] = $dfl.'%';
                $tysave['viewstr'] = $viewstr;
                $tysave['sucaiod'] = 0;

                if($sucaiod>0){
                    $tysave['sucaiod'] = $sucaiod;
                }
                return $tysave;
                exit;
            }
        }

        //命运提开始

        if($tyid==2){
            /*	 积分： cid= 1
            did 4  前一提    积分   （加分  减分  乘以）
            did 5  后一提    积分   （加分  减分  乘以 ）
            id 6  最后一道    积分   （加分  减分  乘以）
            id 7  第一道    积分   （加分  减分  乘以）
            id 8  得分最高的    积分   （加分  减分  乘以）
            id 9  得分最低的    积分   （加分  减分  乘以）
            题目 ： cid = 2
            id  10  得分最高的重做
            id  11  得分最低的重做
            id  12   增加一道题
            时间： cid  = 3
            id  13  时间   （ 缩短   延长   中止）
            */



            $v_my = TyUser::name('v_my')->where('id',$aid)->field('cid,did,jf1,jf2,jf3')->find();
            $cid = $v_my['cid'];
            $did =$v_my['did'];

            $jf1 = $v_my['jf1']; /*加分*/
            $jf2 =$v_my['jf2'];  /*减分*/
            $jf3 = $v_my['jf3']; /*乘以*/

            $has_huida = TyUser::where(['id'=>$this->id,'aid'=>$aid,'huida'=>1])->find();
            if($has_huida !=null){
                return false;
            }

            //命运提开始
            if($cid == 1){

                /*$id 处理题id*/

                if($did==4){
                    /*前一提*/
                    $ckid = $this->midchke($this->uid,$this->tid,$did);
                    if($jf1!=""){
                        $jifen = $jf1;
                    }elseif($jf2!=""){
                        $jifen = '-'.$jf2;
                    }elseif($jf3!=""){
                        $jifencke =TyUser::where('id',$ckid)->value('jifen');
                        if($jf3 == 0){
                            $dfjck = '-'.$jifencke;
                        }elseif($jf3 < 1 && $jf3 >0 ) {
                            $dfjck =  '-'.$jifencke *(1- $jf3);
                        }else{
                            $dfjck = $jifencke *($jf3-1);
                        }

                        $jifen= $dfjck;
                    }


                }elseif($did==5){  /*后一提*/

                }elseif($did==6){  /*最后一道*/

                }elseif($did==7){  /*第一道*/
                    $ckid = $this->midchke($this->uid,$this->tid,$did);
                    if($jf1!=""){
                        $jifen  = $jf1;
                    }elseif($jf2!=""){
                        $jifen  = '-'.$jf2;
                    }elseif($jf3!=""){
                        $jifencke =TyUser::where('id',$ckid)->value('jifen');
                        if($jf3 == 0){
                            $dfjck = '-'.$jifencke;
                        }elseif($jf3 < 1 && $jf3 >0 ) {
                            $dfjck =  '-'.$jifencke *(1- $jf3);
                        }else{
                            $dfjck = $jifencke *($jf3-1);
                        }
                        $jifen = $dfjck;
                    }
                }elseif($did==8){  /*积分最高的*/
                    $ckid = $this->midchke($this->uid,$this->tid,$did);
                    $jifencke =TyUser::where('id',$ckid)->value('jifen');
                    if($jf1!=""){
                        $jifen = $jf1;

                    }elseif($jf2!=""){
                        if($jf2 > $jifencke){
                            $jifencke =  '-'.$jifencke;
                        }else{
                            $jifencke =  '-'.$jf2;
                        }
                        $jifen= $jifencke;

                    }elseif($jf3!=""){
                        $jifencke =TyUser::where('id',$ckid)->value('jifen');
                        if($jf3 == 0){
                            $dfjck = '-'.$jifencke;
                        }elseif($jf3 < 1 && $jf3 >0 ) {
                            $dfjck =  '-'.$jifencke *(1- $jf3);
                        }else{
                            $dfjck = $jifencke *($jf3-1);
                        }
                        $jifen = $dfjck;

                    }
                }elseif($did==9){    /*积分最低的*/
                    $ckid = $this->midchke($this->uid,$this->tid,$did);
                    $jifencke =TyUser::where('id',$ckid)->value('jifen');
                    if($jf1!=""){
                        $jifen = $jf1;
                    }elseif($jf2!=""){
                        if($jf2 > $jifencke){
                            $jifencke =  '-'.$jifencke;
                        }else{
                            $jifencke =  '-'.$jf2;
                        }
                        $jifen = $jifencke;

                    }elseif($jf3!=""){
                        $jifencke =TyUser::where('id',$ckid)->value('jifen');
                        if($jf3 == 0){
                            $dfjck = '-'.$jifencke;
                        }elseif($jf3 < 1 && $jf3 >0 ) {
                            $dfjck =  '-'.$jifencke *(1- $jf3);
                        }else{
                            $dfjck = $jifencke *($jf3-1);
                        }
                        $jifen = $dfjck ;

                    }


                }

                $tabledata['uid'] = $this->uid;
                $tabledata['userty'] = $this->tid;
                $tabledata['eid'] = $this->id;
                $tabledata['type'] = 2;
                $tabledata['jifen'] = $jifen;
                $tabledata['datatime'] =$this->time;
                $tabledata['appuid'] =0;
                Db::name($this->table3)->insert($tabledata);
            }elseif($cid == 2 ){
                if($did==10 || $did ==11){  /*积分最高的重做*/
                    $ckid = $this->midchke($this->uid,$this->tid,$did);
                    $tiji = TyUser::where('id',$ckid)->field('jifen,tifen')->find();
                    $jifencke =  $tiji['jifen'];
                    $tifencke =   $tiji['tifen'];
                    $sql1 = "UPDATE kw_ty_user SET `dttime`='0','valid'=>0,`huida` = '0',`notin`='0',`jifen`='0',`defen`='0',`daname`='', qita  = '' where id='$this->id' and aid ='$aid'  ";
                    $sql2 = "UPDATE kw_user_ty SET `jifen`=`jifen`-".$jifencke." , `defen`=`defen`-".$jifencke." , `zjifen`=`zjifen`-".$tifencke." , `zdefen`=`zdefen`-".$tifencke." where uid='$this->uid' and id = '$this->tid'";
                    Db::query($sql1);
                    Db::query($sql2);
                }elseif($did == 12){
                    /**增加一道题开始*/
                    $notdate = $this->time - (90*24*60*60); // 当前超时三月时间
                    $obj = TyUser::where('uid',$this->uid)->where('tyid',1)->where('dttime','>',$notdate)->order('id','desc')->value('aid');
                    $tyanli = '';
                    if($obj !=null){
                        $tyanlisave= implode(',',$obj);
                        $tyanli = "and id not in($tyanlisave)";
                    }
                    $tabledata =UserTy::where('id',$this->tid)->field('lx,ztid,hangye')->find();
                    $lx = $tabledata['lx'];
                    $ztid =$tabledata['ztid'];
                    $hangye = $tabledata['hangye'];

                    $objs =Db::query("SELECT `id` , `jifen` , `txid` , `ztid`, `cid` ,`lx`  FROM kw_anli where  open = '1'  ".$tyanli."  order by rand() limit 1");
                    $obj = $objs[0];
                    $obid = $obj['id'];
                    $jifen = $obj['jifen'];
                    $txid = $obj['txid'];
                    $ztid = $obj['ztid'];
                    $cid = $obj['cid'];
                    $tjlx = $obj['lx'];
                    $sucaiid = Db::name('anli_fodder')->where('lid',$obid)->find() ==null ? 0:1;
                    $inarray['uid'] = $this->uid;
                    $inarray['tyid'] = 1;
                    $inarray['aid'] = $obid;
                    $inarray['userty'] = $this->tid;
                    $inarray['tjlx'] = $tjlx;
                    $inarray['lxid'] = $lx;
                    $inarray['zhuanti'] = $ztid;
                    $inarray['hangye'] = $hangye;
                    $inarray['tifen'] = $jifen;
                    $inarray['txid'] = $txid;
                    $inarray['hid'] = $cid;
                    $inarray['sucaiid'] =$sucaiid?$sucaiid:0;
                    TyUser::insert($inarray);

                }
                /**增加一道题结束*/
            }elseif($cid == 3){
                $v_my = Db::name('v_my')->where('id',$aid)->field('jf5,jf6,jf7')->find();
                if($v_my['jf7']){
                    TyUser::where('id',$this->id)->where('aid',$aid)->data(['dttime'=>$this->time,'valid'=>1,'huida'=>1])->update();
                    UserTy::where('id',$this->tid)->update(['stopdate'=>$this->time]);
                    return  'yansi';
                }
                /*算取时间（ 缩短 延长 中止）*/
                // $jf5 = checklei($mid,$tablepre.v_my,id,jf5); /*缩短*/
                // $jf6 = checklei($mid,$tablepre.v_my,id,jf6); /*延长*/
                // $jf7 = checklei($mid,$tablepre.v_my,id,jf7); /*中止*/

                // $times5 = 60*$jf5 ;
                // $times6 = 60*$jf6 ;
                // $times7 = 60*$jf7 ;
                // if($did == 13){
                // 		if($jf5!=""){
                // 	 	$this->conn->query("UPDATE ".$this->table." SET `jsdate`=`jsdate`-".$times5." where uid='$this->uid' and id = '$this->tid'");
                // 	}elseif($jf6!=""){
                // 	 	$this->conn->query("UPDATE ".$this->table." SET `jsdate`=`jsdate`+".$times6." where uid='$this->uid' and id = '$this->tid'");
                // 	}elseif($jf7!=""){
                // 		$outp = "&tm=oup&t=".$jf7;
                // 				}
                // }
            }

            //命运提结束

            TyUser::where('id',$this->id)->where('aid',$aid)->update(['dttime'=>$this->time,'valid'=>1,'huida'=>1]);
            UserTy::where('id',$this->tid)->update(['stopdate'=>$this->time]);
            return true;
        }

    }

    public function dfl($dfl,$tysave){
        if($dfl<31){
            $tsname = "别灰心!";
            $tsxiao = "http://zxedu.wisecareer.org/img/sub_3.png";
        }elseif($dfl>31 && $dfl<70){
            $tsname = "恭喜你!";
            $tsxiao = "http://zxedu.wisecareer.org/img/sub_1.png";
        }elseif($dfl>69){
            $tsname = "你真棒!";
            $tsxiao = "http://zxedu.wisecareer.org/img/sub_1.png";
        }
        $tysave['tsname'] =$tsname;
        $tysave['tsxiao'] =$tsxiao;
        return $tysave;
    }

    //查询体验积分  并判断体验是否结束
    public function jstime(){

        $timesjs =   UserTy::where('uid',$this->uid)->where('id',$this->tid)->order('id','desc')->value('jsdate');

        if($this->time > $timesjs){
            return false;
        }
        return true;

    }

    //查询个人观点数是多少
    public function tyjifen(){
        return UserTy::where('uid',$this->uid)->where('id',$this->tid)->order('id','desc')->value('jifen');
    }

    //查询个人观点数是多少
    public function guandian(){
        $guandiannum = TyUser::where('uid',$this->uid)->where('userty',$this->tid)->where('qita','!=','')->count();
        return $guandiannum;
    }

    public function midchke($uid,$userty,$did){
        if($did == 4 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',1)->order('dttime','desc')->value('id');
        }elseif($did == 5 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',0)->order('aid','asc')->value('id');
        }elseif($did == 6 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',0)->order('aid','desc')->value('id');
        }elseif($did == 7 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',1)->order('dttime','asc')->value('id');
        }elseif($did == 8 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',1)->order('jifen','desc')->value('id');
        }elseif($did == 9 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',1)->order('jifen','asc')->value('id');
        }elseif($did == 10 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',1)->order('jifen','desc')->value('id');
        }elseif($did == 11 ){
            $obj= TyUser::where('uid',$uid)->where('userty',$userty)->where('huida',1)->order('jifen','asc')->value('id');
        }
        if($obj !=null){
            return $obj;
        }
        return false;
    }
}

