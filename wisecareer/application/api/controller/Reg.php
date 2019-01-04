<?php
namespace app\api\controller;

use app\common\Code;
use app\common\model\Myuser;
use app\common\model\Shcool;
use app\common\StringRsa;
use app\index\controller\Basics;
use think\Controller;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\facade\Cache;
use think\facade\Cookie;
use think\Request; 

/**
 * Class Index
 * @package app\api\controller
 */
class Reg extends Basics
{
    /**
     * @return mixed
     */
    public function index()
    {
        // 产生 验证码
//        session_start();
//        $vcode = md5(uniqid());
//        Cache::set(session_id(),$vcode,3600);
//        $this->assign('vcode',$vcode);
        return $this->fetch();
    }

    /**
     * @param object $app
     */
    public function reguser(Request $request)
    {
        // 验证提交内容是否符合规则
        $veri = $this->Verify_POST($request);
        if( $veri !== true){
            return $veri;
        }

        $dianhua = $request->param ('phone');
        $yz = $request->param ('yz');
        // 验证手机验证码

        $insertdata['schoolname']=0;


        if( !empty($request->param('schoolname')) ){
           // dump($request->param());
            // 学校名字  学校验证码
            $schoolname = $request->param('schoolname');
            $schoolcode = $request->param('schoolcode');

            try {
                $schooldata = Shcool::scope( 'Titlename', $schoolname )->find();
            } catch (DbException $e) {
                return $this->echo_json(40007);
            }

            if($schooldata == null ) {
                return $this->echo_json( 40007 );
            }
            $schoolid = $schooldata->id;
            if(!$schoolid){
                return $this->echo_json(40007);
            }
            $whereset['classid']= intval($schoolid);
            $whereset['verify']= trim($schoolcode);

            $res = Shcool::shcoolVerify($whereset);

            if(!$res){
                return $this->echo_json(40008);
            }
            $insertdata['scid']  =  $schooldata['scid'];
            $insertdata['sdid']  =   $schooldata['sdid'];
            $insertdata['closslx']  =   $schooldata['lx'];
            $insertdata['viptype'] = 1;
            $insertdata['schoolname'] = $schoolid;

        }

        $code = new Code();
        if(!$code->VerificationCode($dianhua,$yz)){
            return $this->echo_json(40006);
        }


        $insertdata['dianhua'] = trim ($dianhua);
        $insertdata['ol_type'] = 2;
        $insertdata['grades'] = 1;
        $insertdata['memberId'] = 0;
        $insertdata['memberuid'] = 0;
        $insertdata['openid'] = cookie('openid');

        if(!empty(cookie('openid'))){
            $openid = cookie('openid');
            $type = empty(input('post.type'))?0:intval(input('post.type'));
            if($type ==2){
                $mydata = Myuser::whereLike('ztopenid',"%{$openid}%")->data(['ztopenid'=>''])->update();
            }elseif($type==3){
                $mydata = Myuser::whereLike('openid',"%{$openid}%")->data(['openid'=>''])->update();
            }

        }
        $insertdata['datetime'] =time();
        $insertdata['password_od'] = $request->param( 'mm' );
        $insertdata['password']   = md5( $request->param( 'mm' ) );

        if(Myuser::viliregUser($dianhua)){
            return $this->echo_json(20003);
        }
        $user = Myuser::create($insertdata);

        if(!$user){
             return $this->echo_json(40009);
        }
        //给一天的会员
        //$myuser ->test_delay($uid);
        //$myinfo = Myuser::whereid($user->id)->field('password,password_od',true)->find();

        if(null == Myuser::name('myuser_account')->where('uid',$user->id)->find()){
            Myuser::name('myuser_account')->insert(['uid'=>$user->id,'datetime'=>$this->request->time()]);
        }
        $token= StringRsa::createUserToken($user->id);
        Cache::set($token,$user->id,24*CACHE_DATA_TIME*CACHE_DATA_TIME);
        return $this->echo_json(0,'',$token);

        //现在，可以先来定制自己的“决策树”模型，然后了解推荐的目标专业，以及选科走班方案
    }



    //验证所传数据是否为空
    private function Verify_POST (Request $request)
    {
        // 验证手机号

        if (empty($request->param('phone'))) {

            return $this->echo_json(40001);
        }
        // 验证密码
        if (empty($request->param ('mm'))) {
            return $this->echo_json(40002);
        }
        if ( empty($request->param ('qrmm'))) {
            return $this->echo_json (40003);
        }
        if ( empty($request->param ('yz')) ) {
            return $this->echo_json (40004);
        }

        if ( empty($request->param ('type')) || !is_numeric($request->param('type')) ) {
            return $this->echo_json (40011);
        }

        if (  $request->param ('mm') != $request->param ('qrmm')) {
            return $this->echo_json(40005);
        }

        return true;
    }



    public function explain(){
        return $this->fetch();
    }

}
