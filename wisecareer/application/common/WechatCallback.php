<?php
namespace app\common;

use think\facade\Cache;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class WechatCallback{
    private $access_token;
    public function __construct($type){
        if($type =='zt'){
            $this->access_token = $this->getztAccessToken();
        }elseif($type =='xy'){
            $this->access_token = $this->getAccessToken();
        }

    }
    public function run(){
        $this->sand_template();
    }
    //获取用户信息  // subscribe 是否订阅
    public function get_userinfo(){
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=oWnY70lN_yKpmoElkPLbe48xM9OA&lang=zh_CN";
        $getresult = $this->https_request($url);
    }

    //发送 报名 通知
    public function send_invi_sms($openid,$webstr,$titlename,$ksdata){
        if(!$openid) return false;
        $result['touser'] =$openid;
        $result['template_id'] ="NdXyaj_ywbKkjbALiGjtYtE-fHfyFrM_LXzCfIVwQyQ";
        //$result['url'] ="http://www.baidu.com";
        $array['first']['value'] = $webstr;
        $array['keyword1']['value'] = $titlename;
        $array['keyword2']['value']= $ksdata;
        //$array['remark']['value']= "\r\n点击查看详情。";
        $result['data'] =$array;
        // $opeind = "oWnY70lN_yKpmoElkPLbe48xM9OA"; //
        // $time ="2018年9月22日";
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token;
        $data= json_encode($result);
        $getresult = $this->https_request($url,$data);

    }

    // 发送套餐报告
    public function send_meal_sms($openid,$webstr,$titlename,$money,$ksdata){
        //恭喜您，充值成功，以下为相关简讯
        //服务类型：余额充值
        //充值金额：100.00
        //充值时间：2018年09月01日 13:33
        //如有疑问，请及时在线联系我们的客服！
        if(!$openid) return false;
        $result['touser'] =$openid;
        $result['template_id'] ="VAe2mYmiAWdH15T1cmXFnm3ueywxm4bgwRaKkYPfD5g";
        //$result['url'] ="http://www.baidu.com";
        $array['first']['value'] = $webstr;
        $array['keyword1']['value'] = $titlename;
        $array['keyword2']['value'] = $money;
        $array['keyword3']['value']= $ksdata;
        //$array['remark']['value']= "\r\n点击查看详情。";
        $result['data'] =$array;
        // $opeind = "oWnY70lN_yKpmoElkPLbe48xM9OA"; //
        // $time ="2018年9月22日";
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token;
        $data= json_encode($result);
        $getresult = $this->https_request($url,$data);

    }
    /**
     * 获取模版列表
     *
     */

    public function gettemplate_list(){
        $url ="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".$this->access_token;

        $getresult = $this->https_request($url);
        echo "<pre>";
        var_dump($getresult);
    }
    /**
     * 获得模板ID
     */
    public function gettemplate_id(){
        $url ="https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$this->access_token;
        $data = '  {
             "template_id_short":"TM00221"
       }';
        $getresult = $this->https_request($url,$data);
        echo "<pre>";
        var_dump($getresult);
    }

    /**
     * 设置模版
     */
    public  function settemplate(){


        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=";

        $data = '{
          "industry_id1":"16",
          "industry_id2":"17"
       	}';
        $setresult = $this->https_request($url,$data);

        echo "<pre>";
        var_dump($setresult);
    }
    /**
     * 获取模版
     */
    public  function gettemplate(){

        $geturl = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=".$this->access_token;

        $getresult = $this->https_request($geturl);
        echo "<pre>";
        var_dump($getresult);


    }

    public function getAccessToken()
    {
        //获取memcache缓存的access_token
        $access_token = Cache::get("xyaccess_token");

        //如果缓存的access_token失效
        if(!$access_token)
        {
            //如果失效调用获取接口凭证来获取access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".XYAPPID."&secret=".APPSECRET;
            $outoptArr = $this->https_request($url);
            if(!isset($outoptArr['errcode']))
            {
                //memcache缓存access_token
                Cache::set("xyaccess_token",$outoptArr['access_token'],7000);
                return $outoptArr['access_token'];
            }
        }
        return $access_token;
    }
    public function getztAccessToken()
    {
        //获取memcache缓存的access_token
        $access_token = Cache::get("ztaccess_token");

        //如果缓存的access_token失效
        if(!$access_token)
        {
            //如果失效调用获取接口凭证来获取access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".ZTAPPID."&secret=".ZTAPPSECRET;
            $outoptArr = $this->https_request($url);
            if(!isset($outoptArr['errcode']))
            {
                //memcache缓存access_token
                Cache::set("ztaccess_token",$outoptArr['access_token'],7000);
                return $outoptArr['access_token'];
            }
        }
        return $access_token;
    }


    private function https_request($url,$data=null)
    {
        //1、初始化curl
        $ch = curl_init();

        //2、设置传输选项
        curl_setopt($ch, CURLOPT_URL, $url);//请求的url地址
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//将请求的结果以文件流的形式返回

        if(!empty($data))
        {
            curl_setopt($ch,CURLOPT_POST,1);//请求POST方式
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//POST提交的内容
        }

        //3、执行请求并处理结果
        $outopt = curl_exec($ch);

        //把json数据转化成数组
        $outoptArr = json_decode($outopt,TRUE);

        //4、关闭curl
        curl_close($ch);

        //如果返回的结果$outopt是json数据，则需要判断一下
        if(is_array($outoptArr))
        {
            return $outoptArr;
        }
        else
        {
            return $outopt;
        }
    }
}

