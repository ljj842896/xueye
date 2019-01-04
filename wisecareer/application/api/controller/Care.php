<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\Cares;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\Db;
use think\facade\Config;
class Care extends Action
{
    /**
     * @return string
     */
    private function find_where(){
        if(empty($_GET['action'])){
           return $this->echo_json(30004);
        }
        $action  = trim($_GET['action']);
        if($action == "advantage"){
            return "subject_advantage_users";
        }elseif($action == "selection"){
            return "subject_combination_users";
        }elseif($action == "major"){
            return "action_major_users";
        }elseif($action == "character"){
            return "character_type_users";
        }else{
            return  $this->echo_json(30004);
        }
    }
    // 占比例
    public function proportion(){
        $uid = $this->uid;

        $model = $this->find_where();

        if($model =="subject_advantage_users"){
            $tpname= Db::name('edu_wen_sr')->alias('s')->leftJoin("edu_lilun l",'s.list21 = l.tpid')->where('s.uid',$uid)->value('l.tpname');
            $msg = '请先在决策树中选择自己的“优势学科”，再来查询哟！';
        }elseif($model =="subject_combination_users"){
            $tpname= Db::name('myuser')->alias('m')->leftJoin("edu_gk_class e",'m.subject = e.tpid')->where('id',$uid)->value('e.tpname');
            $tpname = str_replace('#','-',$tpname);
            $msg = '请先在决策树中选好选科组合，再来查询哟！';
        }elseif($model =="character_type_users"){
            $rest  = Db::name('st_ceping')->where('uid',$uid)->where('intE','>',0)->where('intI','>',0)->order('datetime','desc')->find();
            $daima = $rest['val1'].$rest['val2'].$rest['val3'].$rest['val4'];
            if($daima){
                $tpname = Db::name('xing')->where('daima',$daima)->value('titlename').'('.$daima.')';
            }else{
                $tpname = '';
            }
            $msg = '请先完成性格测评，再来查询哟！';
        }else{
            $msg = '请先收藏一些目标专业，再来查询哟';
            $tpname  = count( Caches::EduUserScId($uid));
        }

        return  $this->echo_json(0	,$msg,$tpname);
    }

    /**
     *优势学科分布
     */
    public  function discipline_distribution()
    {
        $uid = $this->uid;
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $model = $this->find_where();
        $users = Cares::$model($uid,$scid) ;
        if(!$users){
            return $this->echo_json(0);
        }

        $data = Cares::discipline_distribution($users);
        return $this->echo_json(0,'',$data);
    }
    // 学科订阅分布
    public function subject_subscription(){
        $uid = $this->uid;
        $model = $this->find_where();
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $users = Cares::$model($uid,$scid) ;
        if(!$users){
            return $this->echo_json(0);
        }
        $data =Cares::subject_subscription($users);

        return $this->echo_json(0,'',$data);
    }
    //行业偏向分布
    public function industry_bias(){
        $uid = $this->uid;
        $model = $this->find_where();
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $users = Cares::$model($uid,$scid) ;
        if(!$users){
            return $this->echo_json(0);
        }
        $data = Cares::industry_bias($users);
        return $this->echo_json(0,'',$data);
    }

    //人气专业
    public function get_professional(){
        $uid = $this->uid;

        $model = $this->find_where();
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $users = Cares::$model($uid,$scid) ;

        if(!$users){
            return $this->echo_json(0);
        }
        $data = Cares::get_professional($users);

        return $this->echo_json(0,'',$data);
    }

    //四大专业类型中的分布
    public function edu_edu_tpid(){
        $uid = $this->uid;

        $model = $this->find_where();
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $users = Cares::$model($uid,$scid) ;

        if(!$users){
            return $this->echo_json(0);
        }
        $data = Cares::Edu_edu_Tpid($users);

        return $this->echo_json(0,'',$data);
    }

    //偏科
    public function partial_section(){
        $uid = $this->uid;

        $model = $this->find_where();
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $users = Cares::$model($uid,$scid) ;
        if(!$users){
            return $this->echo_json(0);
        }
        $data = Cares::partial_section($users);

        return $this->echo_json(0,'',$data);
    }

    //选科组合
    public function edu_gk_class(){
        $uid = $this->uid;

        $model = $this->find_where();
        $scid = Caches::MyuserInfoFiled($uid)['scid'];
        $users = Cares::$model($uid,$scid) ;

        if(!$users){
            return $this->echo_json(0);
        }
        $data = Cares::edu_gk_class($users);

        return $this->echo_json(0,'',$data);
    }




}