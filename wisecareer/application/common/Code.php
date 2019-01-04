<?php
namespace app\common;
use Db;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Code
{
    /*验证码类
    *短信错验证码  email验证码   验证码对
    */
    public $use;            //1,注册 2,修改密码 3,一次性 4，教学端
    public $uname = "bjhszt";
    public $passd = "caihonglian123";
    public $smsapi = "http://www.smsbao.com/";
    public $time;
    public $stoptime;
    public $mobile;        //手机
    public $userrune;        //验证码
    public $content;        //发送内容
    //生成验证码
    public function Code($str,$use)
    {
        // 手机号存在  已经注册过
        if($use == 1  ){
            $data =  Db::name('myuser')->whereDianhua($str)->find();
            if($data){
                return 'shouji';
            }
        }

        // 手机号不存在 不能找回密码
        if($use == 2){
            $data =  Db::name('myuser')->whereDianhua($str)->find();
            if(!$data){
                return 'none';
            }
        }

        $this->time = time();
        $this->stoptime = time() +600;
        $this->userrune = $this->getRand(4);
        $this->mobile 	= $str;
        $this->use 	= $use;

        if($use == 1){
            $this->content = "【彩虹联】你的注册验证码为".$this->userrune."";
        }elseif($this->use == 2){
            $this->content = "【彩虹联】你找回密码的验证码为".$this->userrune."";
        }elseif($this->use == 3){
            $this->content = "【彩虹联】你的验证码为".$this->userrune."";
        }

        if($this->writetable()){
            return $this->mobileCode();
        }else{
            return 'error';
        }

    }

    /**
     * 发送短信验证码
     * @return string
     */
    public function mobileCode()
    {

        //$Timestemp = date("mdHis",time());

        //$Keys = md5($this->uname.$this->passd.$Timestemp);

        //$ChineseName = urlencode($this->content);

        //$gateway ="http://www.youxinyun.com:3070/Platform_Http_Service/servlet/SendSms?UserName=".$this->uname."&password=".$this->passd."&Key=".$Keys."&Mobiles=".$this->mobile ."&Content=".$ChineseName."&Timestemp=".$Timestemp."&Extcode=&senddate=&batchID=&CharSet=utf-8";

        $sendurl = $this->smsapi."sms?u=".$this->uname."&p=".md5($this->passd)."&m=".$this->mobile."&c=".urlencode($this->content);
        //$result =file_get_contents($sendurl) ;
        //echo $statusStr[$result];

        $result = file_get_contents($sendurl);
        //$data = json_decode($result,true);

        if($result != 0){
            return 'error';
        }else{
            return $this->userrune; //验证码
        }

    }

    //生成数字
    private function getRand($leng)
    {
        //$a = '0123456789abcdefghijklmnopqrstuvwxyz'; //加小写字母都可以
        $a = '0123456789'; //加小写字母都可以
        $str =null;
        for($i=0;$i<$leng;$i++){
            $str .= $a[rand(0, strlen($a)-1)];
        }
        return $str;
    }
    //写入数据库
    private function writetable()
    {

        $conent=$this->content;
        $last_sms_id = Db::name('user_sms')->insertGetId( [
            "tel" => $this->mobile,
            "use" => $this->use,
            "uid" => 0,
            "sms" => $this->userrune,
            "content" => $conent,
            "datetime" => $this->time,
            "stoptime" => $this->stoptime,
        ]);

        return $last_sms_id;
    }
    //验证验证码对错
    public function VerificationCode($str,$code)
    {

        $id  =  Db::name('user_sms')->where ('tel',$str)->where ('sms',$code)->where ('orderby',0)->value ('id');
        if($id){
            return Db::name('user_sms')->where ('id',$id)->update(['orderby' => 1]);
        }
        return false;

    }

}






