<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\Myuser;
use app\common\model\Recharge;
use app\common\model\UserMessage;
use app\common\Paydef;
use app\index\controller\Action;
use think\Controller;
use think\Db;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Payment extends Action
{
    /**
     * 升级vip
     * @return mixed
     */
    public function defrayal()
    {
        if(empty($this->request->param('action'))){
            return $this->echo_json(30004);
        }
        if(empty($this->request->param('money')) || !is_numeric($this->request->param('money'))){
            return $this->echo_json(30004);
        }
        $action = trim($this->request->param('action'));
        $uid = $this->uid;
        $money = intval($this->request->param('money'));
        // 余额支付
        if($action =='yu'){
            $moneys = Myuser::userBalance($uid);
            if($money>$moneys){
                return $this->echo_json(23001);
            }
            if(!Paydef::get_moth($money)){
                return $this->echo_json(23001);
            }

            $res  =	Paydef::yu_pay($uid,$money,'xy_e_time');
            if($res){
                $add_consume['record'] = "职拓币支付VIP会员";
                $add_consume['code']=Paydef::ordernum();
                $add_consume['uid']=$uid;
                $add_consume['money']=$money;
                $add_consume['pay_type']=5;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume);

                $smg ='职拓币支付VIP会员，消费 '.$money.'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>9,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata);

                $myuserStatus=Caches::MyuserXyStatus($uid,'',1);
                return $this->echo_json(0);
            }else{
                return $this->echo_json(23004);
            }
        }elseif($action =='ka'){
            // 职拓卡 延期
            if(empty($this->request->param('kahao'))){
                return $this->echo_json(23005);
            }
            if(empty($this->request->param('mima'))  ){
                return $this->echo_json(23003);
            }
            $tykdata['password'] =trim($this->request->param('mima'));
            $tykdata['kahao'] = trim($this->request->param('kahao')) ;
            $moneys = Myuser::userBalance($uid);

            // 检验职拓卡 是否正确
            $data = Paydef::Verification($tykdata);
            if(empty($data)){

                return $this->echo_json(23006);
            }
            if($data['occ']==1){
                return $this->echo_json(23007);
            }
            if($money > ( $data['lxtime']/2 +$moneys )){
                return $this->echo_json(23008);
            }
            // 职拓卡 充值
            $res1 = Paydef::recharge_add($data,$uid);
            if($res1){
                $add_pay['code']=Paydef::ordernum();
                $add_pay['uid']= $uid;
                $add_pay['money']= $data['lxtime']/2;
                $add_pay['pay_type'] = 1 ;
                $add_pay['state']= 1 ;
                $add_pay['datetime']=time();
                Paydef::add_pay($add_pay);
            }
            // 延期
            $res2  =	Paydef::yu_pay($uid,$money,'xy_e_time');
            if($res2){
                $add_consume['record'] = '职拓卡支付VIP会员' ;
                $add_consume['code']=Paydef::ordernum();
                $add_consume['uid']=$uid;
                $add_consume['money']=$money;
                $add_consume['pay_type']=5;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume);

                $smg ='职拓卡支付VIP会员，消费 '.$money.'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>9,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata);

                $myuserStatus=Caches::MyuserXyStatus($uid,'',1);
                return $this->echo_json(0);
            }else{
                return $this->echo_json(23004);
            }

        }else{
            return $this->echo_json(23009);
        }

    }



    /**
     *充值职拓币
     */
    public function recharge(){
        //充值卡号
        // 职拓卡 延期
        if(empty($this->request->param('kahao'))){
            return $this->echo_json(23005);
        }
        if(empty($this->request->param('password'))  ){
            return $this->echo_json(23003);
        }
        $tykdata['password'] =trim($this->request->param('password'));
        $tykdata['kahao'] = trim($this->request->param('kahao')) ;

        $uid = $this->uid;
        // 检验职拓卡 是否正确
        $data = Paydef::Verification($tykdata);

        if(empty($data)){
            return $this->echo_json(23006);
        }
        if($data['occ']==1){
            return $this->echo_json(23007);
        }

        // 职拓卡 充值
        $res1 = Paydef::recharge_add($data,$uid);
        if($res1){
            $add_pay['code']=Paydef::ordernum();
            $add_pay['uid']= $uid;
            $add_pay['money']= $data['lxtime']/2;
            $add_pay['pay_type'] = 1 ;
            $add_pay['state']= 1 ;
            $add_pay['datetime']=time();
            Paydef::add_pay($add_pay);

            $money =$data['lxtime']/2;
            $smg ='职拓卡充值，消费 '.$money .'元';
            $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
            UserMessage::insert($insertdata);
            return $this->echo_json(0);
        }else{
            return $this->echo_json(23004);
        }
        // 职拓卡 充值

    }

    /**
     *充值金额
     */
    public function activ(){
        $uid = $this->uid;
        $userinfo = Caches::MyuserInfoFiled($uid);
        $usertype = $userinfo['viptype']?1:2;
        if( floor((time()-$userinfo['datetime']) /(3600*24) ) >=15 ){
            $typeclass = 2 ;
        }else{
            $typeclass = 1;
        }
        $cityid = $userinfo['scid']?$userinfo['scid']:0 ;
        $time = time();
        $classid  = $userinfo['classname']?$userinfo['classname']:0;


        $sql = "SELECT * from kw_myuser_activity where  (usertype in ({$usertype}) or usertype =0)  and  ( typeclass in ({$typeclass}) or typeclass=0) and (find_in_set($cityid,cityid) or cityid =0) and ( find_in_set($classid,classid) or classid =0) and ks_time < {$time} and js_time >{$time} ";
        $data = Db::query($sql);
        if($data != null ){
            $data = $data[0];
            $result['st'] = 1;
        }else{
            $data =  Db::name('myuser_activity')->where('id',1)->find();
            $result['st'] = 0;
        }
        $data2 = Db::name('myuser_activity_rule')->where('cid',$data['id'])->field('money,coupon')->order('rank','asc')->select();
        $result['list'] = $data2;
        $result['title'] = $data['titlename'];
        $result['content'] = $data['content'];
        return $this->echo_json(0,'',$result);
    }


}
