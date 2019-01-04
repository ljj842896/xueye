<?php
namespace app\api\controller;
use app\common\Caches;
use app\common\Code;
use app\common\Log;
use app\common\model\Myuser;
use app\common\model\MyuserZtPay;
use app\common\model\Shcool;
use app\common\StringRsa;
use app\index\controller\Basics;
use think\Db;
use think\facade\Cache;
use think\Request;
use think\Validate;


class Login extends Basics
{

    /**
     * 处理 登录 信息页面
     * @return \think\response\Redirect
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function dologin(){
        // 判断用户名 密码是否为空
        if(empty($_POST['user'] ) ){ return $this->echo_json(10001);}
        if(empty($_POST['passwd'] ) ){ return $this->echo_json(10002);}
        if(empty($_POST['type'] ) ){ return $this->echo_json(10010);}
        // 用户名 密码
        $user = $_POST['user'];
        $passwd = md5($_POST['passwd']);
        //验证用密码密码
        $myuserdata =Myuser::validateUser($user,$passwd);
        if(!$myuserdata){ return $this->echo_json(10003);  }
        // 判断用户 是否满足登录 要求
        //if(in_array( $myuserdata['closslx'],array(15,32,44) )){ return $this->echo_json(10004);   }
        //把信息写入session

        $myuserdata['school_name'] = Shcool::whereid($myuserdata['schoolname'])->value('titlename');
        $myuserdata =$myuserdata->toArray();

        $uid = $myuserdata['id'];  // 用户ID
        if(!empty(input('openid'))){
            $openid = input('openid');
            $mydata = Myuser::whereLike('openid',"%{$openid}%")->data(['openid'=>''])->update();
            Myuser::whereid($uid)->data(['openid'=>$openid])->update();
        }
        if(!empty(input('ztopenid'))){
            $ztopenid = input('ztopenid');
            $mydata = Myuser::whereLike('ztopenid',"%{$ztopenid}%")->data(['ztopenid'=>''])->update();
            Myuser::whereid($uid)->data(['ztopenid'=>$ztopenid])->update();
        }
        $type = empty(input('post.type'))?0:input('post.type') ;// 学业e站 3  职拓2
        $Log = new Log();
        $Log->userlog($uid,$type);
        // 生成token   下次请求带上

        if($type==2){
            if($myuserdata['viptype']){
                $status=Caches::MyuserZtStatus($uid,'',1);
                $endtime = $status['zt_e_time'];
            }else{
                $hasmeal = MyuserZtPay::userSetMealHas($uid);
                $endtime = $hasmeal['endtime'];
                $status['status'] = $hasmeal?1:0; //
            }


            $data['days'] = empty($endtime)?0:  intval(round(($endtime - time())/(24*3600))  );
        }elseif($type ==3){
            $status =Caches::MyuserXyStatus($uid,'',1);
            $data['days'] = empty($status['xy_e_time'])?0:  intval(round(($status['xy_e_time'] - time())/(24*3600))  );
        }

        if(null == Myuser::name('myuser_account')->where('uid',$myuserdata['id'])->find()){
            Myuser::name('myuser_account')->insert(['uid'=>$myuserdata['id'],'datetime'=>$this->request->time()]);
        }


        $data['viptype'] = $myuserdata['viptype'];
        $data  = array_merge($data,$status);
        $token= StringRsa::createUserToken($uid);
        Cache::set($token,$uid,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        $data['token'] =$token;

        return $this->echo_json(0,'',$data);

    }

    /**
     * 处理 登录 信息页面
     * @return \think\response\Redirect
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function codedologin(){
        // 判断用户名 密码是否为空
        if(empty($_POST['phone'] ) ){ return $this->echo_json(10011);}
        if(empty($_POST['yz'] ) ){ return $this->echo_json(10012);}
        if(empty($_POST['type'] ) ){ return $this->echo_json(10010);}
        // 用户名 密码
        $dianhua = trim($_POST['phone']);
        $yz = trim($_POST['yz']);
        $code = new Code();
        if(!$code->VerificationCode($dianhua,$yz)){
            return $this->echo_json(40006);
        }
        $myuserdata = Myuser::where('dianhua',$dianhua)->find();
        if($myuserdata==null){
            $insertdata['schoolname']=0;
            $insertdata['dianhua'] = trim ($dianhua);
            $insertdata['ol_type'] = 2;
            $insertdata['grades'] = 1;
            $insertdata['memberId'] = 0;
            $insertdata['memberuid'] = 0;
            $insertdata['openid'] = '';
            $insertdata['viptype'] = 0;
            $insertdata['datetime'] =time();
            $insertdata['password_od'] = 123456;
            $insertdata['password']   = md5( 123456 );

            $myuserdata = Myuser::create($insertdata);
            //return $this->echo_json(20004);
        }
        $myuserdata = $myuserdata->toArray();


        $uid = $myuserdata['id'];  // 用户ID
        if(!empty(cookie('openid'))){
            $openid = cookie('openid');
            $mydata = Myuser::whereLike('openid',"%{$openid}%")->data(['openid'=>''])->update();
            Myuser::whereid($uid)->data(['openid'=>$openid])->update();
        }

        $type = empty(input('post.type'))?0:input('post.type') ;// 学业e站 3  职拓2
        $Log = new Log();
        $Log->userlog($uid,$type);
        // 生成token   下次请求带上

        if($type==2){
            $status =Caches::MyuserZtStatus($uid,'',1);
            $data['days'] = empty($status['zt_e_time'])?0:  intval(round(($status['zt_e_time'] - time())/(24*3600))  );
        }elseif($type ==3){
            $status =Caches::MyuserXyStatus($uid,'',1);
            $data['days'] = empty($status['xy_e_time'])?0:  intval(round(($status['xy_e_time'] - time())/(24*3600))  );
        }

        $data['viptype'] = $myuserdata['viptype'];
        $data  = array_merge($data,$status);
        $token= StringRsa::createUserToken($uid);
        Cache::set($token,$uid,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        $data['token'] =$token;
        return $this->echo_json(0,'',$data);

    }

    /**
     * 退出登录
     * @return App
     */
    public function logout()
    {
        if(empty($_POST['token'] ) ){ return $this->echo_json(30003);}
        $token = trim( $_POST['token']);
        cookie('openid',null);
        cookie('token',null);
        Cache::rm($token);
        return $this->echo_json(0);


    }

    /**
     * @param App $app
     */
    public function password()
    {
        session_start();
        $vcode = md5(uniqid());
        Cache::set(session_id(),$vcode,3600);
        $this->assign('vcode',$vcode);
        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return View
     */
    public function newpass (Request $request)
    {
        if(empty($request->param('phone'))){
            return $this->echo_json(40001);
        }
        if(empty($request->param('password'))){
            return $this->echo_json(40002);
        }
        if(empty($request->param('yz'))){
            return $this->echo_json(40004);
        }

        $phone = $request->param('phone');
        $yz = $request->param('yz');

        $password = $request->param('password');
//        if($passwds != $password){
//            return $this->echo_json(40005);
//        }
        $password_od_lao = Myuser::where('dianhua',$phone)->value('password_od');
        if($password_od_lao == $password ){
            return $this->echo_json(50016);
        }
        $result = (new Code)->VerificationCode($phone,$yz);
        if(!$result){
            return $this->echo_json(40006);
        }


        $update_data['password'] = md5($password);
        $update_data['password_od'] = $password;

        $res = Myuser::where('dianhua',$phone)->update($update_data);
        if($res){
            return $this->echo_json(10006);
        }else{
            return $this->echo_json(50009);

        }


    }




}
