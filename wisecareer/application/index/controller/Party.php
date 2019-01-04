<?php
namespace app\index\controller;

use app\common\Caches;
use app\common\StringRsa;
use think\Db;
use think\facade\Cache;

class Party extends Basics
{
    /**
     * Action constructor.
     */

    public function gettoken(){
        if( empty(input('post.partner'))  ){
            return $this->echo_json(140001,'用户名空');
            exit;
        }
        if( empty( input('post.keyID'))  ){
            return $this->echo_json(140002,'密码错误');
            exit;
        }

        if( empty(  input('post.uid') ) && !is_numeric( input('post.uid')  )  ){
            return $this->echo_json(140006,'uid错误');
            exit;;
        }
        if( empty(  input('post.mobile') )  || !is_numeric(input('post.mobile')  ) || !Phone( input('post.mobile') ) ){
            return $this->echo_json(140004,'手机号错误');
            exit;
        }
        $username = trim(input('post.partner')  );
        $password = md5(trim( input('post.keyID')  ));
        $mobile   =  trim(input('post.mobile')  );


        $array['dianhua'] = $mobile ;
        $array['closslx'] = 3;
        $array['ol_type'] = 2;
        $array['memberuid'] = intval( input('post.uid') );
        $array['password'] = md5(123456);
        $array['password_od'] = 123456;
        $array['datetime']  = time();

        $memberdata = Db::name('member')->where('username',$username)->where('password',$password)->find();
        if($memberdata == null){
            return $this->echo_json(140005,'用户名或密码错误');
            exit;
        }
        $memberId = $memberdata['id'];

        $userinfo  = Db::name('myuser')->where('dianhua',$mobile)->find();
        if($userinfo ==null){
            $array['memberId'] = $memberId;
            $uid =  Db::name('myuser')->insertGetId($array);
            $account['uid'] = $uid;
            Db::name('myuser_account')->insert($account);
        }else{
            $uid  = $userinfo['id'];
        }
        if($uid && empty($userinfo['memberuid'])){
            Db::name('myuser')->whereid($uid)->update(['memberuid'=>$array['memberuid'],'memberId'=>$memberId]);
        }
        $token= StringRsa::createUserToken($uid);
        Cache::set($token,$uid,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        return $this->echo_json(0,'',$token);

    }


    /**
     *  第三方修改手机号
     * @return \think\response\Json
     */
    public function modifyPhone(){
        if( empty(input('post.partner'))  ){
            return $this->echo_json(140001,'用户名空');
            exit;
        }
        if( empty( input('post.keyID'))  ){
            return $this->echo_json(140002,'密码错误');
            exit;
        }

        if( empty(  input('post.uid') ) && !is_numeric( input('post.uid')  )  ){
            return $this->echo_json(140006,'uid错误');
            exit;;
        }
        if( empty(  input('post.mobile') )  || !is_numeric(input('post.mobile')  ) || !Phone( input('post.mobile') ) ){
            return $this->echo_json(140004,'手机号错误');
            exit;
        }
        $username = trim(input('post.partner')  );
        $password = md5(trim( input('post.keyID')  ));
        $mobile   =  trim(input('post.mobile')  );
        $uid   =  intval(input('post.uid')  );

        $memberdata = Db::name('member')->where('username',$username)->where('password',$password)->find();
        if($memberdata == null){
            return $this->echo_json(140005,'用户名或密码错误');
            exit;
        }
        $data =Db::name('myuser')->where('memberuid',$uid)->where('memberId',$memberdata['id'])->update(['dianhua'=>$mobile]);
        if($data){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(130002);
        }

    }

    public function notify(){

        $httpurl = empty( $_POST['siteUrl'])? $_SERVER['HTTP_REFERER']:"http://".$_POST['siteUrl'];
        if(empty($_POST['token']) || empty($_POST['siteUrl'])  ){
            return redirect($httpurl);exit;
            exit;
        }
        $token = trim($_POST['token']);
        $tokens = StringRsa::getUserIDFromToken($token);
        if($tokens===null){
            return redirect($httpurl);exit;
        }elseif($tokens==false){
            return redirect($httpurl);exit;
        }else{
            if(!Cache::get($token)){
                return redirect($httpurl);exit;;
            }
        }
        cookie('token',$token,'6*60*60');
        cookie('EXTERNAL',1);
        return redirect("https://wapi.wisecareer.org/Wx/fnwxlogin?source=xnw");
        exit;
    }
}
