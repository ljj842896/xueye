<?php
namespace app\api\controller;

use app\common\Charts;
use app\common\model\AddtoRead;
use app\common\model\Anli_search;
use app\common\model\AppChat;
use app\common\model\AppJf;
use app\common\model\AppUserEvaluate;
use app\common\model\Coach;
use app\common\model\HangyeDynamic;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\Db;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Star extends Action
{

//明星教练
    public function star(){
        $uid = $this->uid;
        $data = Coach::get_star($uid);
        return $this->echo_json(0,'',$data);
    }

    //空间
    public function space(){

        if(empty($_GET['uid'])  || !is_numeric($_GET['uid'])){
            return $this->echo_json(30004);
        }

        $uid  = intval($_GET['uid']);
        $userid = $this->uid;
        // 教练信息
        $myuser = Coach::userInfo($uid);
        $data['has']= 0;
        $nickname= $myuser['coach_nickname']?$myuser['coach_nickname']:'匿名';

        $data['titlename'] = $nickname."的空间";

        $spaceimg = Db::name("app_user_pic")->where(['uid'=>$uid])->order('datetime','desc')->limit(5)->column('pic');
        $zhiye = Anli_search::get_user_search($uid);

        $logo = $this->get_log($uid);
        //教练积分
        $data['jifen'] = AppJf::get_jf($uid);
        $grades_new = AppJf::grades($uid);  //获取用户最新级别

        //级别
        $data['grade']  = grade($grades_new);

        //用户头像

        $data['img'] = userImg($myuser);
        $data['coach_nickname'] =$myuser['coach_nickname'];
        $data['sex'] =$myuser['sex'];
        $chat = AppChat::get_chat($uid);
        $data['chat']= $chat ? $chat:'';

        //点赞

        $datas  = AppUserEvaluate::user_recom($uid);
        $atten = AppUserEvaluate::has_user_attenrecom($uid,$userid);
        if($atten){
            $data['attention'] = $atten['attention'];
            $data['recommend'] = $atten['recommend'];
        }else{
            $data['attention'] = 0;
            $data['recommend'] =0;
        }


        $data['zhiye'] = $zhiye?$zhiye:'暂无';
        $data['spaceimg'] = $spaceimg?$spaceimg:'';
        $data['logo'] = $logo;
        $result = array_merge($data,$datas);
        return $this->echo_json(0,'',$result);

    }


    /**
     * 用户经历日志
     * @param $uid
     * @return mixed
     */
    private function get_log($uid){
        $logo = Db::name('app_user_jingli')->where(['uid'=>$uid])->where('logo','neq','')->value('logo');
        return $logo;
    }



    public function read()
    {
        if(empty($_GET['uid'])){
           return  $this->echo_json(30004);
        }
        $uid  = intval($_GET['uid']);

        $result = AddtoRead::sel_read($uid);
        foreach ($result as $key => $value) {
            $result[$key]['datetime']  =  date('Y-m-d',$value['datetime']);
        }

        $result = empty($result)?'':$result;
        return  $this->echo_json(0,'',$result);

    }

    public function experience()
    {
        if(empty($_GET['uid'])){
            return  $this->echo_json(30004);
        }

        $uid  = intval($_GET['uid']);
        $where['uid'] = $uid;
        $result = Coach::getjingli($where);

        foreach ($result as $key => $value) {
            $result[$key]['datetime']  =  date('Y-m-d',$value['datetime']);
        }
        $result = empty($result)?'':$result;
        return $this->echo_json(0,'',$result);

    }

    public function dynamic()
    {
        if(empty($_GET['uid'])){
            return $this->echo_json(30004);
        }
        $uid  = intval($_GET['uid']);

        $data  = HangyeDynamic::sel_dynamic($uid);
        foreach ($data as $key => $value) {
            $data[$key]['datetime']  =  date('Y-m-d',$value['datetime']);
        }
        $result = empty($result)?'':$result;
        return $this->echo_json(0,'',$data);

    }

    public function dynamic_details()
    {
        if(empty($_GET['id'])){
            return $this->echo_json(30004);
        }
        $where['id'] = intval($_GET['id']);
        $result  = HangyeDynamic::get_dynamic($where);
        $result['datetime']=   date('Y-m-d',$result['datetime']);
        return $this->echo_json(0,'',$result);
    }

    public function attenrecom()
    {
        if(empty($_POST['uid']) || !is_numeric($_POST['uid'])){
            return $this->echo_json(30004);
        }
        if(empty($_POST['type']) || !is_numeric($_POST['type'])  || intval($_POST['type']) >2 || intval($_POST['type']) ==0 ){
            return $this->echo_json(30004);
        }

        $type =  intval($_POST['type']);
        $array['uid']  = intval($_POST['uid']);
        $array['userid'] = $this->uid;

        if($type ==1){
            $array['attention'] = 1;
        }elseif($type ==2){
            $array['recommend'] = 1;
        }

        $data= AppUserEvaluate::add_attenrecom($array);

        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return  $this->echo_json(130002);
        }

    }


}
