<?php
namespace app\index\controller;
use app\common\Code;
use app\common\model\Myuser;
use app\common\model\Shcool;
use app\index\controller\Basics;

use think\facade\Cache;
use think\Request;
use think\Validate;
// 不需要登录的  公共 方法
class Ajax extends Basics{



    /**
     *学校搜索
     */
    public function searchAll(){

        // 需要检索的 单词
        if (empty($_GET['term'])) exit ;
        $q = strtolower($_GET["term"]);
        if (get_magic_quotes_gpc()) $q = stripslashes($q);
        if(!empty($_GET['type']) && $_GET['type']=='z'){
            $shcoolFile=Cache::store('file')->get('shcoolFilez');
            if(!$shcoolFile){
                //所以学业名字d
                $data= Shcool::scope('z')->select()->toArray();
                $shcooltitle = array_column($data,'titlename');
                $shcoolFile = array_combine($shcooltitle,$data);
                // $content=var_export($shcoolFiles,true);
                Cache::store('file')->set('shcoolFilez',$shcoolFile);
            }
        }elseif(!empty($_GET['type']) && $_GET['type']=='d'){
            $shcoolFile=Cache::store('file')->get('shcoolFiled');
            if(!$shcoolFile){
                //所以学业名字
                $data= Shcool::scope('d')->select()->toArray();
                $shcooltitle = array_column($data,'titlename');
                $shcoolFile = array_combine($shcooltitle,$data);
                // $content=var_export($shcoolFiles,true);
                Cache::store('file')->set('shcoolFiled',$shcoolFile);
            }
        }else{
            $shcoolFile=Cache::store('file')->get('shcoolFileall');
            if(!$shcoolFile){
                //所以学业名字
                $data= Shcool::scope('all')->select()->toArray();
                $shcooltitle = array_column($data,'titlename');
                $shcoolFile = array_combine($shcooltitle,$data);
                // $content=var_export($shcoolFiles,true);
                Cache::store('file')->set('shcoolFileall',$shcoolFile);
            }
        }


        $result = [];
        foreach ($shcoolFile as $key=>$value) {
            if (strpos(strtolower($key), $q) !== false) {
                array_push($result, array("id"=>$value['id'], "value" =>$value['titlename'], strip_tags($key)));
            }
            if (count($result) > 11)
                break;
        }

        $data['data'] = $result;

        return json($data);

    }

    /**
     * 更换新的 vcode
     * @return \think\response\Json
     */
    public function vcode( ){
        //session_start();
        $vcode = md5(uniqid());
        //Cache::set(session_id(),$vcode,3600);
        return json($vcode);

    }

    /**
     *  验证码方法
     */
    public function code()
    {
        $cookie = $this->request->cookie('PHPSESSID');
        // 同一个 cookie 一天只能 接受 10条短信

        if( Cache::get($cookie) && Cache::get($cookie) > 10 ){
            return $this->echo_json(20007);
        }else{
            $num = intval(Cache::get($cookie)) + 1;
        }

        if( !empty($this->request->param('tel')) && Phone(trim($this->request->param('tel')))){
            $str = trim($this->request->param('tel'));
        }else{
            return $this->echo_json(20001);
        }
		
        //判断是注册还是 修改秘密
        $use = empty($this->request->param('sms'))?'0':intval($this->request->param('sms'));
        if(!$use || $use>3){
            return $this->echo_json(20002);
        }

        $code = new Code();
        $result = $code->Code($str,$use);

        if($result =='shouji'){
            return $this->echo_json(20003);
        }elseif($result =='none'){
            return $this->echo_json(20004);
        }elseif($result =='error'){	
            return $this->echo_json(20005);
        }else{
            Cache::set($cookie,$num,60); // 设置缓存时间
            return $this->echo_json(20000);
        }
    }


    /**
     * 说明 文档
     * @return \think\response\Json
     */
    public function person()
    {
        if(empty($_GET['id'])){
            $this->echo_json('错误',300);
        }
        $id  = intval($_GET['id']);
        $res = Db('app_article')->whereid($id)->field('content,titlename')->find();
        $headlines = $res['titlename'];
        $result['res']=$res['content'];
        $result['headlines']=$headlines;
        return $this->echo_json(0,'',$result);

    }


}