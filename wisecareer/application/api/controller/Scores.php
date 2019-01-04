<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\Shcool;
use app\index\controller\Action;
use think\Controller;
use think\Db;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Scores extends Action
{
    /**
     * @return mixed
	 志愿填报
     */

    //省控线
    public function get_province(){
        if(empty($_GET['stu_city']) || !is_numeric($_GET['stu_city'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['lx']) || !is_numeric($_GET['lx'])  ){
            return $this->echo_json(30004);
        }

        if(!empty($_GET['year']) && is_numeric($_GET['year'])  ){
            $where['year'] = intval($_GET['year']);
        }

        $where['scid'] = intval($_GET['stu_city']);
        $where['subject'] = intval($_GET['lx']);

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }

        $scid = intval($_GET['stu_city']);
        $areadata = Caches::AreaAll();
        $sctpname   = empty($areadata[$scid])?'':$areadata[$scid];
        $data= \app\common\model\Scores::sel_enrollProvince($where);

        $result['list'] = $data;
        $result['sctpname'] = $sctpname;
        return $this->echo_json(0,'',$result);


    }


    //大学分数线
    public function get_enrollgrade()
    {
        # code...
        //stu_city=26&wl=1&lx=15&titlename=safd


        if(empty($_GET['lx']) || !is_numeric($_GET['lx'])  ){
            return $this->echo_json(30004);
        }


        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];
        if(empty($_GET['stu_city']) || !is_numeric($_GET['stu_city'])  ){
            $_GET['stu_city'] = $myinfo['scid'];
        }
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }

        if(empty($_GET['schoolid'])  || !is_numeric($_GET['schoolid'])  ){
            return $this->echo_json(30004);
        }
        if(!empty($_GET['wl'])){
            $where['subject'] = intval($_GET['wl']);
        }
        $where['schoolid'] = intval($_GET['schoolid']);
        $where['scid'] = intval($_GET['stu_city']);
        $schoolid = intval($_GET['schoolid']);

        $areadata = Caches::AreaAll();

        $tpname =Shcool::whereid($schoolid)->field('pic,scid,sdid,titlename,tagid')->find();


        $tpname['scname'] =  empty($areadata[$tpname['scid']])?'':$areadata[$tpname['scid']];
        $tpname['sdname'] = empty($areadata[$tpname['sdid']])?'':$areadata[$tpname['sdid']];

        $tagid= explode(',',$tpname['tagid']);
        $tpname['tagid'] =  Db::name('class_class')->wherein('id',$tagid)->column('tpname');
        $city  =empty( $areadata[intval($_GET['stu_city'])])?'': $areadata[intval($_GET['stu_city'])];
        $data= \app\common\model\Scores::sel_enrollgrade($where);


        $result['list'] = $data;
        $result['sctpname'] = $tpname;
        $result['city'] = $city;
        return $this->echo_json(0,'',$result);

    }


    /**  之分选大学
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function knowledge_points()
    {
        if(empty($_GET['stu_city']) || !is_numeric($_GET['stu_city'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['lx']) || !is_numeric($_GET['lx'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['score'])  || !is_numeric($_GET['score'])  ){
            return $this->echo_json(30004);
        }

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }

        //stu_city=360&lx=2&sch_city=3&score=650

        $where['scid'] = intval($_GET['stu_city']);//k考生所在地
        $where['subject'] = intval($_GET['lx']); //w文理

        if(!empty($_GET['sch_city'])){
            $where['sty_du'] = intval($_GET['sch_city']);		//院校所再低
        }

        $score = trim($_GET['score']);		//院校所再低
        $data= \app\common\model\Scores::knowledge_points($where,$score);
        if(intval($_GET['lx']) ==1){
            $result['subject'] = '文';
        }elseif(intval($_GET['lx']) ==2){
            $result['subject'] = '理';
        }

        $areadata = Caches::AreaAll();
        $city  = empty($areadata[intval($_GET['stu_city'])])?'':$areadata[intval($_GET['stu_city'])];
        $result['city']  = $city;
        $result['sch']  =  intval($_GET['sch_city']);
        $result['schname']  =empty( $areadata[intval($_GET['sch_city'])])?'': $areadata[intval($_GET['sch_city'])];

        $result['list'] = $data;
        return $this->echo_json(0,'',$result);

    }

    //

    /**
     *大学列表
     */
    public function knowledge_points_list()
    {
        if(empty($_GET['action'])   ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['stu_city']) || !is_numeric($_GET['stu_city'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['lx']) || !is_numeric($_GET['lx'])  ){
            return $this->echo_json(30004);
        }

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }


        if(empty($_GET['score'])  || !is_numeric($_GET['score'])  ){
            return $this->echo_json(30004);
        }

//stu_city=360&lx=2&sch_city=3&score=650
        $where['scid'] = intval($_GET['stu_city']);//k考生所在地
        $where['subject'] = intval($_GET['lx']); //w文理
        if(!empty($_GET['sch_city'])){
            $where['sty_du'] = intval($_GET['sch_city']);		//院校所再低
        }

        $score = trim($_GET['score']);
        $action = trim($_GET['action']);

        $data= \app\common\model\Scores::knowledge_points_list($where,$score,$action,$uid);
        return $this->echo_json(0,'',$data);


    }


    /**知位选大学
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function knowledge_rank(){

        if(empty($_GET['stu_city']) || !is_numeric($_GET['stu_city'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['lx']) || !is_numeric($_GET['lx'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['rank'])  || !is_numeric($_GET['rank'])  ){
             return $this->echo_json(30004);
        }

        $where['scid'] = intval($_GET['stu_city']);//k考生所在地
        $where['subject'] = intval($_GET['lx']); //w文理
        if(!empty($_GET['sch_city'])){
            $where['sty_du'] = intval($_GET['sch_city']);		//院校所再低
        }
        $rank= intval($_GET['rank']);
        $data= \app\common\model\Scores::knowledge_rank($where,$rank);

        if(intval($_GET['lx']) ==1){
            $result['subject'] = '文';
        }elseif(intval($_GET['lx']) ==2){
            $result['subject'] = '理';
        }

        $areadata = Caches::AreaAll();
        $city  = empty($areadata[intval($_GET['stu_city'])])?'':$areadata[intval($_GET['stu_city'])];
        $result['city']  = $city;
        $result['sch']  =  intval($_GET['sch_city']);
        $result['schname']  =  empty($areadata[intval($_GET['sch_city'])])?'':$areadata[intval($_GET['sch_city'])];

        //if($data){

        $result['list'] = $data;
        return $this->echo_json(0,'',$result);
        // }else{
        // 	$this->echo_json('无数据',200012);
        // }
    }



    public function knowledge_rank_list()
    {
        if(empty($_GET['action'])   ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['stu_city']) || !is_numeric($_GET['stu_city'])  ){
            return $this->echo_json(30004);
        }
        if(empty($_GET['lx']) || !is_numeric($_GET['lx'])  ){
            return $this->echo_json(30004);
        }
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }


        if(empty($_GET['rank'])  || !is_numeric($_GET['rank'])  ){
            return $this->echo_json(30004);
        }

//stu_city=360&lx=2&sch_city=3&score=650

        $where['scid'] = intval($_GET['stu_city']);//k考生所在地
        $where['subject'] = intval($_GET['lx']); //w文理
        if(!empty($_GET['sch_city'])){
            $where['sty_du'] = intval($_GET['sch_city']);		//院校所再低
        }

        $rank = intval($_GET['rank']);
        $action = $_GET['action'];

        $data= \app\common\model\Scores::knowledge_rank_list($where,$rank,$action,$uid);

        return$this->echo_json(0,'',$data);


    }

    /**
     *各学科年级排位
     */
    public function score_branch()
    {
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $classname = $myinfo['classname'];

        $data = \app\common\model\Scores::score_branch($uid,$schoolid,$classname);

        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return  $this->echo_json(30005);
        }

    }

}

