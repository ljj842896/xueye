<?php
namespace app\common;
use Db;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class MobileSms
{
    /*验证码类
    *短信错验证码  email验证码   验证码对
    */
    public  $use;            //1,注册 2,修改密码 3,一次性 4，教学端
    public  $uname = "bjhszt";
    public  $passd = "caihonglian123";
    public  $smsapi = "http://www.smsbao.com/";
    public  $time;
    public  $mobile;        //手机
    public  $content;
    private $success;
    private $uid;        //发送uid

    /**
     * 发送短信验证码
     * @return string
     */
    public function send_sms($mobile,$uid,$str)
    {
        $this->time = time();
        $this->mobile 	= $mobile;
        $this->uid 	= $uid;
        $this->content 	= $str;
        //$Timestemp = date("mdHis",time());
        //$Keys = md5($this->uname.$this->passd.$Timestemp);
        //$ChineseName = urlencode($this->content);
        //$gateway ="http://www.youxinyun.com:3070/Platform_Http_Service/servlet/SendSms?UserName=".$this->uname."&password=".$this->passd."&Key=".$Keys."&Mobiles=".$this->mobile ."&Content=".$ChineseName."&Timestemp=".$Timestemp."&Extcode=&senddate=&batchID=&CharSet=utf-8";
        $sendurl = $this->smsapi."sms?u=".$this->uname."&p=".md5($this->passd)."&m=".$this->mobile."&c=".urlencode($this->content);
        //$result =file_get_contents($sendurl) ;
        //echo $statusStr[$result];
        do{
            $result = file_get_contents($sendurl);
        }while($result !=0 );
        //$data = json_decode($result,true);
        if($result != 0){
            $this->success =0;
        }else{
            $this->success =1;
        }
        $this->writetable();

    }

    //写入数据库
    private function writetable()
    {
        $conent=$this->content;
        $last_sms_id = Db::name('user_message_sms')->insertGetId( [
            "mobile" => $this->mobile,
            "uid" => $this->uid,
            "content" => $conent,
            "datetime" => $this->time,
            "adminid" =>0,
            "success" =>$this->success,
        ]);
        return $last_sms_id;
    }

}






