<?php

namespace app\common;
use app\common\model\TyUser;
use app\common\model\UserTy;
use app\common\model\V_txclass;
use think\Db;


/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Generateproblem
{
    //应该掉题数 1 案例题 3：思维题 4：知识题

    private  $tyal1 = '0'; // 开放式题数，本 （职能、行业、专题） 题
    private  $tyal1name = '0'; //开放式题数 通用题型
    private  $tyal2 = '0'; //命运题数
    private  $tyal3 = '0'; // 选择题中 ， 本（职能、行业、专题） 题
    private  $tyal3name = '0';   // 选择题中通用题型

    private  $type;   //调题类型  职能、行业、专题  
    private  $tyid;   //调题id 
    private  $uid;   //调题id 
    private  $planid;   //计划ID

    private  $id;   //生成套题ID
    private  $number;   //体验次数
    private  $lan;   //英文 还是中文案例
    private  $user_lx;   //英文 还是中文案例

    public   function __construct($uid,$user_lx,$number ,$type,$tyid,$planid,$lan){
        $this->type = $type;
        $this->tyid = $tyid;
        $this->uid = $uid;
        $this->number = $number;
        $this->time = time();
        $this->planid = $planid;
        $this->lan = $lan;
        if($user_lx ==0){
            $this->user_lx = '0,1';
        }else{
            $this->user_lx = '1';
        }
    }

    //一次性调题
    public   function transfer($tyal1,$tyal2,$tyal3,$tyal1name,$tyal3name){
        $this->tyal1 = $tyal1;
        $this->tyal1name = $tyal1name;
        $this->tyal2 = $tyal2;
        $this->tyal3 = $tyal3;
        $this->tyal3name = $tyal3name;
        //判断 生成 题类型
        if($this->type == 'hangye'){
            if(!$this->trans_hangye()){
                return false;
            }
        }elseif($this->type == 'lx'){
            if(!$this->trans_lx()){
                return false;
            }
        }elseif($this->type == 'ztid'){
            if(!$this->trans_zhuanti()){
                return false;
            }
        }

        return $this->id;
       // return $this->destiny();
    }



    //行业调题
    public   function trans_hangye(){
        $this->once_userty();
        $where1 = " and  FIND_IN_SET({$this->tyid},cid)  ";
        $where2 = " and  FIND_IN_SET({$this->tyid},hid)  ";
        //公共调题类
        $this-> public_once($where1,$this->tyal1);
        $this->Choice($where2,$this->tyal3);
        return true;
    }
    //职能调题
    public   function trans_lx(){
        $this->once_userty();
        $where1 = " and  lx = '".$this->tyid."' and  lx !='8' ";
        $where2 = " and  aid = '".$this->tyid."' and  lx !='8'";
        $this->public_once($where1,$this->tyal1);
        $this->Choice($where2,$this->tyal3);
        return true;
    }
    //专题调题
    public  function trans_zhuanti(){
        //if($this->existence($this->table)){

        $this->once_userty();
        $zhuantiname = V_txclass::where('id',$this->tyid)->value('tpname');// $this->get('v_txclass','tpname',['id'=>$this->tyid]);

        if($this->tyid == 2 or $this->tyid == 3 or $this->tyid == 8 or $this->tyid == 9 or $this->tyid == 10){
            $zhuantisqlANli = " and  txid = '".$this->tyid."' ";
        }else{
            $zhuantisqlANli = " and ztid  like  '%".$zhuantiname."%' ";
        }
        $this->public_once($zhuantisqlANli,$this->tyal1);
        $this->Choice($zhuantisqlANli,$this->tyal3);
        return true;
        // }else{
        // 	return false;
        // }
    }
    //命运题调题
    private  function destiny(){
        // 产生一个从1到$m的数组
        $arr = range(1,3);
        // 打乱数组 
        shuffle ($arr);
        // 取前十个 
        if(!$this->tyal2){
            return $this->id;
        }
        for($i=0;$i<$this->tyal2;$i++){
            $array = [];
            // 返回这组数字
            $sql ="select `id`,`cid` from kw_v_my where cid = ".$arr[$i]." and open = '1'  order by rand() limit 1";
            $result123  = Db::query($sql);
            foreach ($result123 as $key => $obj) {
                $array['uid']= $this->uid;
                $array['tyid']= 2;
                $array['aid']= $obj['id'];
                $array['userty']= $this->id;
                $array['source']= 1;
                $array['valid']= 0;
                //$this->insert($this->table2,$array);
                TyUser::insert($array);
            }
        }
        return $this->id;
    }
    /**
     *生成 套题
     */
    private  function once_userty(){
        $insert_id = UserTy::insertGetId(['uid'=>$this->uid,'language'=>$this->lan,'valid'=>0,'source'=>1,'planid'=>$this->planid,'datetime'=>$this->time,"{$this->type}"=>$this->tyid]);
        $this->id = $insert_id;  //生成套题Id
    }
    //开放式案例题
    private  function public_once($where,$num){
        if(!$num){
            return false;
        }
        $ztsql = "select `id` , `jifen` , `txid` , `ztid`, `cid` ,`lx` ,`language`  from kw_anli where Tystudy in({$this->user_lx})  and language = {$this->lan} and open = '1' ".$where."  order by rand() limit ".$num."";
        $result  = Db::query($ztsql);
        foreach ($result as $key => $obj) {
            $obid = $obj['id'];
            $jifen = $obj['jifen'];
            $txid = $obj['txid'];
            $cid = $obj['cid'];
            $tjlx = $obj['lx'];
            $lan = $obj['language'];
            //$sucaiid =	$this->has('anli_fodder',['lid'=>$obid]);
            $sucaiid = Db::name('anli_fodder')->where('lid',$obid)->find()==null?0:1;
            $type = $this->get_type_ty_user();
            //插入道题
            TyUser::insert(['uid'=>$this->uid,'language'=>$lan,"{$type}"=>$this->tyid,'valid'=>0,'tyid'=>1,'source'=>1,'aid'=>$obid,'userty'=>$this->id,'tjlx'=>$tjlx,'tifen'=>$jifen,'txid'=>$txid,'hid'=>$cid,'sucaiid'=>$sucaiid]);

        }

        return true;

    }

    private  function get_type_ty_user(){
        if($this->type == 'hangye'){
            $type = 'hangye';
        }elseif($this->type == 'lx'){
            $type = 'lxid';
        }elseif($this->type == 'ztid'){
            $type = 'zhuanti';
        }
        return $type;
    }

    //选择题 
    private  function Choice($where,$num){
        if(!$num){
            return false;
        }
        //选择题
        $ztsqlobj = "select `id`, `jifen` , `txid` , `ztid`, `hid` ,`sucai` , `aid`,`language` from kw_v_ticlass where Tystudy in ({$this->user_lx}) and language={$this->lan} and  lx !='8' ".$where." and (cid = '3' or cid = '2')  and open = '1'  order by rand() limit ".$num."";

        $result  = Db::query($ztsqlobj);
        foreach ($result as $key => $obj) {
            $obid = $obj['id'];
            $jifen = $obj['jifen'];
            $txid = $obj['txid'];
            $ztid = $obj['ztid'];
            $hid = $obj['hid'];
            $tjlx = $obj['aid'];
            $sucai = $obj['sucai'];
            $lan = $obj['language'];
            if($sucai!=""){
                $sucaiid = "1";
            }else{
                $sucaiid = "0";
            }
            $type = $this->get_type_ty_user();
            $array['uid']=$this->uid;
            $array["{$type}"]=$this->tyid;
            $array["tyid"]=3;
            $array["aid"]=$obid;
            $array["userty"]=$this->id;
            $array["tjlx"]=$tjlx;
            $array["tifen"]=$jifen;
            $array["txid"]=$txid;
            $array["hid"]=$hid;
            $array["sucaiid"]=$sucaiid;
            $array['source']= 1;
            $array['valid']= 0;
            $array['language']= $lan;
            TyUser::insert($array);
        }
    }
}

