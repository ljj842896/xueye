<?php
namespace app\common;
use Db;
use think\facade\Cache;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class HttpApi
{
    public 	$fileds; //数据库字段
    public 	$time; //当前时间
    public	    $partner = 'wisecareer';
    public	    $url = 'http://xnw.com/iwx/wisecareer/sync';
    //用户信息 表
    public function __construct(){
        $this->time = time();
    }
    public function xnw_http_modify_user($uid,$array){
        if(!$uid){
            return false;
        }
        /*
            name:			真实姓名
            gender:			性别
            identity:		身份类别
            school:			学校名称
            area:			所在地区
            school_type:	学校类型
            class:			教学班级（年级/班级）
         */

        $time = time();
        $postdata['uid'] = $uid;
        $postdata['ctime'] = $this->time;
        $postdata['secret'] = md5($uid.$this->time.'wisecareer');
        $data = array_merge($postdata,$array);
        $result = $this->https_request($this->url,$data);
        if(!$result['errcode']){
            return true;
        }

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
        if(preg_match('/^\xEF\xBB\xBF/',$outopt)) {
            $outopt = substr($outopt,3);
        }

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

