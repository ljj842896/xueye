<?php
namespace app\api\controller;

use app\common\Code;
use app\common\model\Myuser;
use app\common\model\MyuserCoupon;
use app\common\model\UserMessage;
use app\common\StringRsa;
use app\index\controller\Basics;
use think\Controller;
use think\Db;
use think\facade\Cache;

/**
 * Class Index
 * @package app\api\controller
 */
class Coupon extends Basics
{

    /**
     * 代金礼券
     */
    public function cash(){
		
        $id = input('id');
 	
	if(empty($id)){
			   return $this->echo_json(30002);
	}
	
        if(!$data = Cache::get('MyuserCoupon'.$id)){
            $time = $this->request->time();
            $data  = Db::name('myuser_coupon')->where('id',$id)->where('open',1)->where('ks_time','<=',$time)->where('js_time','>=',$time)->find();
            if($data == null){
                return $this->echo_json(240004);
            }
				 Cache::set('MyuserCoupon'.$id,$data,60);
        }
 
        $result['titlename'] = $data['titlename'];
        $result['intro'] = $data['intro'];
        $result['type'] = $data['type'];
        $result['rate'] = $data['rate'];
        $result['content'] = $data['content'];
        return $this->echo_json(0,'',$result);
	
    }


    /**
     * 获取用户礼券
     */
    public function gainCoupon(){
        $tel = input('post.tel');
        $id = input('post.id');

        if(!empty(input('post.token'))){
            $token = input('post.token');
            $userstr  =  StringRsa::getUserIDFromToken($token);
            if(empty($userstr)){
                return $this->echo_json(30002);
            }
            $uid = $userstr['id'];
            $userid  = Myuser::where('id',$uid)->value('id');
        }else{
            if(empty($tel)){
                return $this->echo_json(40001);
            }
            $userid  = Myuser::where('dianhua',$tel)->value('id');
        }

        if(empty($id)){
            return $this->echo_json(30002);
        }

        // 判断用户是否存在

        if($userid==null){
            return $this->echo_json(20004);
        }
        $time = $this->request->time();
        // 0判断需要领取的代金券是否存在 或者过期
        if(!$data = Cache::get('MyuserCoupon'.$id)){
            $data  = Db::name('myuser_coupon')->where('id',$id)->where('open',1)->where('ks_time','<=',$time)->where('js_time','>=',$time)->find();
            if($data == null){
                return $this->echo_json(30002);
            }
            Cache::set('MyuserCoupon'.$id,$data,60);
        }

        // 判断是否领取代金券
        $re = Db::name('myuser_coupon_rule')->where('uid',$userid)->where('couponid',$id)->find();
        if($re!=null){
            return $this->echo_json(240001);
        }
        $insertdata =['uid'=>$userid,'couponid'=>$id,'get_time'=>$time,'use_time'=>$time,'use'=>1];
		
        $re =Db::name('myuser_coupon_rule')->data($insertdata)->insert();
		
        if($re){
            MyuserCoupon::gainCoupon($userid,$id,$data);
            UserMessage::send_coupon_message($userid,$data['rate']);
            return $this->echo_json(240002);
        }else{
            return $this->echo_json(240003);
        }


    }


    /**
     * 注册领劵
     */
    public function reggain(){ 
        $tel = input('post.tel');
        $yz = input('post.code');
        $passwd = input('post.passwd');
        $id = input('post.id');
        // 验证提交内容是否符合规则

        if(empty($id)){return $this->echo_json(30002);}
        if(empty($tel)){return $this->echo_json(40001);}
        if(empty($yz)){return $this->echo_json(40004);}
        if(empty($passwd)){return $this->echo_json(40002);}
        if(!Phone($tel)){return $this->echo_json(20001);}
        $insertdata['schoolname']=0;
        $time = $this->request->time();
        if(!$data = Cache::get('MyuserCoupon'.$id)){
            $data  = Db::name('myuser_coupon')->where('id',$id)->where('open',1)->where('ks_time','<=',$time)->where('js_time','>=',$time)->find();
            if($data == null){
                return $this->echo_json(30002);
            }
            Cache::set('MyuserCoupon'.$id,$data,60);
        }

        if(Myuser::viliregUser($tel)){
            return $this->echo_json(20003);
        }

        $code = new Code();

        if(!$code->VerificationCode($tel,$yz)){
            return $this->echo_json(40006);
        }

        $insertdata['dianhua'] = trim ($tel);
        $insertdata['ol_type'] = 2;
        $insertdata['grades'] = 1;
        $insertdata['memberId'] = 0;
        $insertdata['memberuid'] = 0;

        $insertdata['datetime'] =$time;
        $insertdata['password_od'] = $passwd;
        $insertdata['password']   = md5( $passwd );

        $user = Myuser::create($insertdata);
        if(!$user){
            return $this->echo_json(40009);
        }

        $insertdata =['uid'=>$user->id,'couponid'=>$id,'get_time'=>$time,'use_time'=>$time,'use'=>1];
        Db::name('myuser_coupon_rule')->data($insertdata)->insert();
        Db::name('myuser_account')->data(['uid'=>$user->id,'money'=>0,'datetime'=>$time])->insert();
        MyuserCoupon::gainCoupon($user->id,$id,$data);
        $token= StringRsa::createUserToken($user->id);
        Cache::set($token,$user->id,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        return $this->echo_json(0,'',$token);

        //现在，可以先来定制自己的“决策树”模型，然后了解推荐的目标专业，以及选科走班方案

    }

}
