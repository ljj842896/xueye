<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\Charts;
use app\common\model\ActivityModel;
use app\common\model\Evaluate;
use app\common\model\Myuser;
use app\common\model\UserMessage;
use app\common\WechatCallback;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Activity extends Action
{
    //现场邀约
    public function invitation()
    {
        $uid = $this->uid;
        $myinfofiled = Caches::MyuserInfoFiled($uid);

        $data =ActivityModel::sel_activity($uid,$myinfofiled);
        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(0,'无');
        }
    }

    /**历史邀约
     * @return \think\response\Json
     */
    public function history(){
        $data = ActivityModel::history($this->uid);
        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(0,'无');
        }
    }

    //实习记录
    public function Internship_record(){
        $data = ActivityModel::Internship_record($this->uid);
        $myinfo = Caches::MyuserInfoFiled($this->uid);
        if($data){
            $resul['email'] =  $myinfo['email'];
            $resul['list'] = $data;
            return $this->echo_json(0,'',$resul);
        }else{
            return $this->echo_json(0,'无');
        }
    }

    public function leave(){
        if(empty($_POST['tid']) || !is_numeric($_POST['tid']) ){
            return $this->echo_json(30004);
        }
        $uid = $this->uid;
        $tid = intval($_POST['tid']);
        $data = ActivityModel::leave($uid,$tid);

        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(0,'无');
        }

    }


    //接受邀约

    public function acceptinvitation()
    {




        if(empty($_POST['tid']) || !is_numeric($_POST['tid']) ){
           return  $this->echo_json(30004);
        }
        if(empty($_POST['eid']) || !is_numeric($_POST['eid']) ){
            return $this->echo_json(30004);
        }
        $uid = $this->uid;


        $myinfo = Caches::MyuserInfoFiled($uid);
        if(!userinfo_is($myinfo)){
            return $this->echo_json(50012);
        }



        $array['tid'] = intval($_POST['tid']);
        $tid = $array['tid'];
        $money['uid']  = $uid;
        // 岗位 内容
        $srate_usernum= ActivityModel::get_exper_activ_all($tid);
        $money['money'] = $srate_usernum['srate'];
        $usernum = $srate_usernum['usernum'];
        $array['eid'] = intval($_POST['eid']);
        $array['userid'] = $uid;
        $array['uid'] = 0;
        $array['content1'] = '';
        $array['degree'] = '';
        $array['number'] = 0;

        $has_invi = ActivityModel::has_accept_invi($uid,$tid);
        if($has_invi != null){
            return $this->echo_json(130011);
        }


        $hasaccept =  ActivityModel::has_accept($uid,$tid);
        if(!$hasaccept){
            //接受邀约
            $user_money = Myuser::userBalance($uid);
            if( $user_money > $money['money'] ){
                $data =ActivityModel::accept_invitation($array,$usernum);
                if(!$data){
                    return $this->echo_json(130012);
                }
                if($money['money']){
                    $has = Myuser::dec_money($money);
                }

                $sendsms['code']= $srate_usernum['enter'];
                $sendsms['titlename']=$srate_usernum['titlename'] ;
                $sendsms['ksdate']= $srate_usernum['ksdate'] ;
                $sendsms['title']=$srate_usernum['title'] ;
                UserMessage::send_invi_sms($uid,$sendsms);

            }

        }else{
            $data =ActivityModel::cancel_invitation($array);

            if(!$data){$this->echo_json('你已经进入现场实习不能取消邀约',130002);}
            if($money['money']){
                $has = Myuser::inc_money($money);
                if($has){
                    //通知退费信息
                }
            }
        }

        if($data){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(130002);
        }
    }

    public function test(){
        $WechatCallback = new WechatCallback();
        $WechatCallback->run();
    }


    public function inputlistactivity(){
        $uid  = $this->uid;
        $time = $this->request->time();
        $has_invi_tid=ActivityModel::name('experience_enter')->alias('t1')
            ->leftJoin('experience_activity t2 ','t1.tid = t2.id' )
            ->where('t1.userid',$uid)->where('t1.invi_datetime','neq',0)->where([['t2.jsdate ','>',$time]])->value('t1.tid');
        $has=ActivityModel::name('experience_enter')->alias('t1')
            ->leftJoin('experience_activity t2 ','t1.tid = t2.id' )
            ->where('t1.userid',$uid)->where('t1.invi_datetime','neq',0)->where('t1.datetime','neq',0)->where('t1.endtime','neq',0)->count('*');
        $result['num'] = $has;
        if($has_invi_tid==null){

            $result['data']='';
            return $this->echo_json(130013,'',$result);
        }

        $data =ActivityModel::name(ActivityModel::$table1)->where('id',$has_invi_tid)->field('titlename,ksdate')->find();
        $data['str'] = $data['ksdate']<$time?'进行中':'未开始';
        $data['ksdate']=date("Y-m-d  h:iA",$data['ksdate']);// 02:23:58pm
        $result['data'] = $data;
        return $this->echo_json(0,'',$result);


    }

    //进入现场
    public function input_activity()
    {
        if(empty($_POST['code']) || !is_numeric($_POST['code']) || strlen($_POST['code']) > 6){
            return $this->echo_json(30004);
        }
        $code = trim($_POST['code']);
        $uid  = $this->uid;
        $time  = $this->request->time();
        //->where('datetime',0)
        $has_invi_tid=ActivityModel::name('experience_enter')->alias('t1')
            ->leftJoin('experience_activity t2 ','t1.tid = t2.id' )
            ->where('t1.userid',$uid)->where('t1.invi_datetime','neq',0)->where([['t2.jsdate ','>',$time]])->value('t1.tid');
        if(!$has_invi_tid){
            return $this->echo_json(130013);
        }

        $date = ActivityModel:: verify_enter_time($uid,$code,$has_invi_tid);
        if(!$date){
            return $this->echo_json(130014);
        }

        if($date['ksdate'] > time()){
            $datae = date('Y-m-d H:i:s',$date['ksdate']);
            return $this->echo_json(130015,'',$datae);
        }
        if($date['id']){
            $data = ActivityModel::insert_activity($uid,$date['id']);
            return $this->echo_json(130016);
//            if($data){
//
//            }else{
//                return $this->echo_json(130017);
//            }
        }else{
            return $this->echo_json(130018);
        }

    }

    public function internship()
    {
        $uid  = $this->uid;
        $data = ActivityModel:: getin_activity($uid);
        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(130002);
        }
    }

    /**现场空间
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function scene_space()
    {
        $uid = $this->uid;
        $data = ActivityModel::field_space($uid);

        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(130019);
        }

    }

    /**
     * @return \think\response\Json
     */
    public function degree_endtime()
    {
        if(empty(input('tid')) || !is_numeric(input('tid'))  ){
            return $this->echo_json(30004);
        }
        $tid = intval(input('tid'));
        $uid = $this->uid;

        ActivityModel::degree_endtime($uid,$tid);
        return $this->echo_json(0);
    }


    //实习进度
    public function degree()
    {
        $uid = $this->uid;

        if(empty(input('degree')) || !is_numeric(input('degree')) || input('degree') >5  ){
            return $this->echo_json(30004);
        }
        if(empty(input('tid')) || !is_numeric(input('tid'))  ){
            return $this->echo_json(30004);
        }
        $degree = intval(input('degree'));
        $tid = intval(input('tid'));

        $degree_user=ActivityModel::name('experience_enter')->where(['userid'=>$uid,'tid'=>$tid])->value('degree');
        // if($degree_user >1 && $degree <= $degree_user){
        // 	$this->echo_json('已经上岗不能修改',130003);
        // }

        if(!empty($_FILES['file']['size']) ){
            $is =ActivityModel::file_img($uid,$tid);
            if(!$is){
                return $this->echo_json(130020);
            }
        }

        if($degree==1 && !empty(input('uid')) && is_numeric(input('uid')) ){
            $uids =input('uid');
            $data = ActivityModel::update_degree($uid, $tid, $degree,$uids );
            return $this->echo_json(0);
        }

        if($degree==5){
            $jifen = ActivityModel::get_srate_usernum($tid)['coachjifen'];
            Evaluate::add_jifen($uid,$jifen);
        }

        $content  =[];
        if(!empty(input('post.content1') )  ){
            $content['content1'] = (trim(input('post.content1')));
        }
        if(!empty(input('post.content2') )  ){
            $content['content2'] = (trim(input('post.content2')));
        }
        if( !empty(input('post.content3')) ){
            $content['content3'] = (trim(input('post.content3')));
        }



        $data = ActivityModel::insert_degree($uid, $tid, $degree,$content );
        return $this->echo_json(0);

    }


    public function user_base_opinion(){
        $uid =$this->uid;
        if(empty(input('id')) || !is_numeric(input('id'))  ){
            return $this->echo_json(30004);
        }
        $array['opinion']=null;
        if(!empty($_POST['opinion'])){
            $array['opinion'] = input('post.opinion');
        }
        $where['id']  = intval(input('id'));
        $where['userid']  = $uid;
        $data = ActivityModel::user_base_opinion($array,$where);
        if($data){
            return $this->echo_json(0);
        }else{
            return  $this->echo_json(130002);
        }
    }

    //实习记录
    public function scene_history(){
        $uid= $this->uid;

        $data = ActivityModel::scene_history($uid);
        if($data !=null){
            $data = array_map('formatting',$data);
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(30005);
        }
    }

}
