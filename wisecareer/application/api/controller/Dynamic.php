<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\Anli_class;
use app\common\model\HangyeDynamic;
use app\common\model\Label;
use app\common\model\Lx_class;
use app\common\model\MyuserZtPay;
use app\common\model\Shcool;
use app\common\model\SubjectAttention;
use app\index\controller\Action;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Dynamic extends Action
{
    /**我关心的数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function subjectlist(){
        $uid = $this->uid;
        $data = SubjectAttention::get_lilun($uid);
        return $this->echo_json(0,'',$data);

    }

    /**
     * 订阅学科
     */
    public function subjectadd(){
        $liluns = [2,4,5,8,9,10,15,17,27,28];
        if(empty($this->request->param('classid'))){
            return $this->echo_json(11001);
        }

        $classid = intval($this->request->param('classid'));
        if(!in_array($classid,$liluns)){
            return $this->echo_json(11002);
        }

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $scid = $myinfo['scid'];
//        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
//        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }
        if(!$scid){
           return $this->echo_json(50012);
        }

        $has = SubjectAttention::add_subject($uid,$classid);
        if($has){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(11001);

        }


    }
    /**
     * 用户学科动态
     */
    public function usersubjectlist(){

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $scid = $myinfo['scid'];
//        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
//        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }

//        if(!$scid){
//            return $this->echo_json(50012);
//        }

        $data = SubjectAttention::Recommend_subject($uid,$scid);
        $data1 = SubjectAttention::getsubject($uid);
        foreach ($data1 as $key => $value) {
            $data1[$key]['overdue'] = 0;
            if(time() > $value['stop_time']){
                $data1[$key]['overdue'] = 1;
            }
            $data1[$key]['stop_time']=date('Y-m-d',$value['stop_time']);
        }
        $result['rec']=$data;
        $result['sub']=$data1;
        return $this->echo_json(0,'',$result);
    }

    /**
     * 用户学科动态
     */
    public  function newssubjectlist(){

        if(empty($_GET['classid'])){
            return $this->echo_json(11001);
        }
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $scid = $myinfo['scid'];
//        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
//        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }

//        if(!$scid){
//            return $this->echo_json(50012);
//        }

        $classid = intval($_GET['classid']);

        $limit = $this->search_page();

        $data = SubjectAttention::sel_subject_new($classid,$scid,$limit);


        return $this->echo_json(0,'',$data);

    }
    /**
     * 用户学科动态详情
     */
    public function subjectdetails(){
        if(empty($_GET['id']) || !intval($_GET['id']) ){
           return $this->echo_json(11001);
        }
        $id = intval($_GET['id']);
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];
       // $scid = $myinfo['scid'];
//        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
//        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }

//        if(!$scid){
//            return $this->echo_json(50012);
//        }
        $data = SubjectAttention::get_subject_news($id,$uid);
//        if(!empty($data['is']) && $data['is']==1){
//            return $this->echo_json(0,'',$data);
//        }

        return $this->echo_json(0,'',$data);

    }

    /**
     * 点赞
     */
    public function subject_like(){
        if(empty($this->request->param('tpid')) || !intval($this->request->param('tpid'))){
            return $this->echo_json(30004);
        }
        $tpid = intval($_POST['tpid']);
        $uid = $this->uid;
        $data = SubjectAttention::subject_like_add($tpid,$uid);
        if($data){
            return $this->echo_json(0,'',1);
        }else{
            return $this->echo_json(0,'',0);
        }
    }


    /**
     * 动态浏览量
     */
    public function subject_hits(){
        if(empty($this->request->param('id')) || !intval($this->request->param('id'))){
            return $this->echo_json(30004);
        }
        $id = intval($this->request->param('id'));

        SubjectAttention::subject_hitsnum($id);
        return $this->echo_json(0);
    }

    /**
     * 点赞
     */
    public function hangye_like(){
        if(empty($this->request->param('tpid')) || !intval($this->request->param('tpid'))){
            return $this->echo_json(30004);
        }
        $tpid = intval($_POST['tpid']);
        $uid = $this->uid;
        $data = HangyeDynamic::hangye_like_add($tpid,$uid);
        if($data){
            return $this->echo_json(0,'',1);
        }else{
            return $this->echo_json(0,'',0);
        }
    }

    /**
     * 动态浏览量
     */
    public function hangye_hits(){
        if(empty($this->request->param('id')) || !intval($this->request->param('id'))){
            return $this->echo_json(30004);
        }
        $id = intval($this->request->param('id'));

        HangyeDynamic::hangye_hitsnum($id);
        return $this->echo_json(0);
    }

    /**
     * 行业动态
     * @return \think\response\Json
     */
    public function hangyelist(){
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $scid = $myinfo['scid'];
        $schoolnamnid = $myinfo['schoolname'];
        $data = HangyeDynamic::Recommend_new($uid,$scid);
        // 行业类型

        if($viptype ==1){
            $schooldata =Shcool::name('school_versions')->where('sid',$schoolnamnid)->field('txid as type,aid as tid')->select();
            if($schooldata ==null){
                return $this->echo_json(0);
            }
            $indata=[];
            foreach ($schooldata as $key =>$value){
                $indata[$value['type']][] = $value['tid'];
            }
        }else{
            $ztpaydata = MyuserZtPay::userSetMealHas($uid);
            if($ztpaydata==false){
                return $this->echo_json(260009);
            }
            $tpid = $ztpaydata['id'];
            $indata =  MyuserZtPay::getztpayzi($uid,$tpid);
            if($indata==false){
                return $this->echo_json(260009);
            }

        }
        $data2 = Caches::LxClass();
        $data1=[];
        $data2=[];
        if(!empty($indata['hangye'])){
            $data1 = Caches::HangYeClass();
            foreach ($data1 as $key => $value){
                if(!in_array($value['tpid'],$indata['hangye'])){
                    unset($data1[$key]);
                    continue;
                }
                $data1[$key]['pic'] = 'http://www.wisecareer.org/terminals/'.$value['pic'];
            }
        }

        if(!empty($indata['lx'])){
            $data2 = Caches::LxClass();
            foreach ($data2 as $key => $value){
                if(!in_array($value['id'],$indata['lx'])){
                    unset($data2[$key]);
                    continue;
                }
                $data2[$key]['pic'] = 'http://www.wisecareer.org/terminals/'.$value['pic'];
            }
        }
        $datas = array_merge($data1,$data2);
        $result['rec']=$data;
        $result['sub']=$datas;
        return $this->echo_json(0,'',$result);
    }

    /**
     * 行业动态列表
     */
    public  function hangyenewlist(){

        if(empty(input('get.hangyeid')) && empty(input('get.lxid'))){
            return $this->echo_json(11001);
        }

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $scid = $myinfo['scid'];

        $hangye = intval(input('get.hangyeid'));
        $lx  = intval(input('get.lxid'));
        $limit = $this->search_page();

        $data = HangyeDynamic::sel_hangye_new($hangye,$lx,$scid,$limit);
        $result['list'] = $data;
        if($hangye){
            $title =  Anli_class::where('tpid',$hangye)->field('tpname,profiles')->find();
        }else{
            $title =  Lx_class::where('id',$lx)->field('tpname,profiles')->find();
        }
        $result['title'] = $title;

        return $this->echo_json(0,'',$result);

    }

    /**
     * 行业动态 详情
     * @return \think\response\Json
     */
    public function hangyedetails(){
        if(empty($_GET['id']) || !intval($_GET['id']) ){
            return $this->echo_json(11001);
        }
        $id = intval($_GET['id']);
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];

        $data = HangyeDynamic::get_hangye_news($id,$uid);

        return $this->echo_json(0,'',$data);

    }
}

