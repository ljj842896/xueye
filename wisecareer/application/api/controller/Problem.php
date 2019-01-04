<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\Charts;
use app\common\Generateproblem;
use app\common\Handleproblem;
use app\common\model\Anli_class;
use app\common\model\AppUserEvaluate;
use app\common\model\Coach;
use app\common\model\Evaluate;
use app\common\model\HangyeDynamic;
use app\common\model\Label;
use app\common\model\Lx_class;
use app\common\model\Myuser;
use app\common\model\MyuserZtPay;
use app\common\model\TyUser;
use app\common\model\UserMessage;
use app\common\model\UserTy;
use app\common\Transferproblem;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Problem extends Action
{

    //生成题 方法
    public function generate(){

        $uid = $this->uid;
        //获得 体验  类型
        $myInfo = Caches::MyuserInfoFiled($uid);
        if($myInfo['closslx'] ==9 || $myInfo['closslx'] == 3){
            $user_lx = 0;
        }else{
            $user_lx = 1;
        }



        if(empty($_POST['action'])){
           return $this->echo_json(140001);
        }
        if(empty($_POST['tid'])){
            return $this->echo_json(140001);
        }

        $lan = input('lan')==2?2:1;
        $type = trim($_POST['action']);
        $tid = intval($_POST['tid']);
        $array['type'] = $type;
        $array['tid'] = $tid;
        try {
            $plandata = Label::plan_generate_has( $uid, $array );
        }  catch (DbException $e) {
            return $this->echo_json(30001);
        }

        if(!$plandata){
            return $this->echo_json(140002);
        }

        $planid = $plandata['id'];
        $every = $plandata['every'];
        if($every == 1){
            $tyal1 = 0; // 开放式案例题
            $tyal2 = 0; //命运题
            $tyal3 = 1; // 选择题
            $tyal1name = 0; // 开放式案例题 通用
            $tyal3name = 0; // 选择题 通用
        }elseif($every == 3){
            $tyal1 = 1; // 开放式案例题
            $tyal2 = 1; //命运题
            $tyal3 = 2; // 选择题
            $tyal1name = 0; // 开放式案例题 通用
            $tyal3name = 0; // 选择题 通用
        }

        if($type == 'zhuanti'){
            $type = 'ztid';
        }
        //$every = 3;

        //删除 无效 体检记录

        UserTy::where('uid',$uid)->where('valid',0)->delete();
        TyUser::where('uid',$uid)->where('valid',0)->delete();

        //使用掉题类
        $ti = new Generateproblem($uid,$user_lx,$every,$type,$tid,$planid,$lan);
        // 体验时间  开放式案例题 命运题  选择题 选择题通用类型  开放式案例通用题
        $A = $ti->transfer($tyal1,$tyal2,$tyal3,$tyal1name,$tyal3name);
        if($A){
            return $this->echo_json(0,'',$A);
        }else{
            return $this->echo_json(140003);
        }
    }
    //调题方法

    /**
     * @return \think\response\Json
     */
    public function transfer(){
        if(empty($_GET['tid'])){
            return $this->echo_json(140001);
        }
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $tid = intval($_GET['tid']);
        $ti = new Transferproblem($uid,$tid);
        $tyinfo = $ti ->Transfer_problem();

        if($tyinfo){

            $myuserztpy =MyuserZtPay::name('myuser_zt_eval_pay')->where('uid',$uid)->where('state',1)->find();
            $tyinfo['eval_has'] = $myuserztpy!=null?1:0;
            $tyinfo['eval_num'] = Myuser::name('myuser_account')->where('uid',$uid)->value('eval_num');
        }
        // 评价剩余条数

        return $this->echo_json(0,'',$tyinfo);
    }


    public function handle(){

        //pj_num
        if(empty($_POST['tid'])){
            return $this->echo_json(140001);
        }
        if(empty($_POST['id'])){
            return $this->echo_json(140001);
        }
        if(empty($_POST['tyid'])){
            return $this->echo_json(140001);
        }
        //提交给教练
        $pj_num= 0;
        if(!empty($_POST['pj_num']) && $_POST['pj_num'] ==1){
            $pj_num =1;
        }

        
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $tid = intval($_POST['tid']);
        $id = intval($_POST['id']);
        $tyid = intval($_POST['tyid']);
        $shop_content = '';
        $daname = '';
        $qita = '';
        $insert_data=['userid'=>$uid,'usertyid'=>$tid,'tyuserid'=>$id,'tyid'=>$tyid];
        if($tyid==1){
            if(empty($_POST['shop_content'])){
                return $this->echo_json(140004);
            }
            $shop_content =  urlencode(input('shop_content'));

            $insert_data['content'] = $shop_content;
        }elseif($tyid==2){

        }elseif($tyid==3){
            if(empty($_POST['daname'])){
                return $this->echo_json(140005);
            }
            $daname = $_POST['daname'];
            $qita = $_POST['qita'];
            $insert_data['content'] = urlencode($qita);
        }




        $handle = new Handleproblem($uid,$tid,$id);
        $data = $handle->Handle($shop_content,$daname,$qita);


        if(!$data){
            return $this->echo_json(140006);
        }else{
            if($pj_num ){
                $eval_num=Myuser::name('myuser_account')->where('uid',$uid)->value('eval_num');
                if($eval_num){
                    $res1 = MyuserZtPay::name('push_coa_eval')->where('tyuserid',$id)->find();
                    if($res1==null){
                        $res =MyuserZtPay::name('push_coa_eval')->data($insert_data)->insert();
                        if($res){
                            $res= Db::name('myuser_account')->where('uid',$uid)->setDec('eval_num');
                        }
                    }
                }

            }

            // 发送体验报告
            UserMessage::send_experience_message($tid,$uid);
            Label::plan_zi_comp($tid,$uid);
            return $this->echo_json(0,'',$data);
        }


    }

    public function marknub()
    {
        if(empty($_GET['tid']) || !is_numeric($_GET['tid'])){
            return $this->echo_json(30004);
        }

        $tid = input('get.tid');
        $uid = $this->uid;
        $data =UserTy::where('uid',$uid)->where('id',$tid)->field('ztid,hangye,lx')->find();

        if($data ==null){
            return $this->echo_json(130021);
        }
        $result['skip'] =1;
        if(!empty($data['ztid'])){
            $result['skip'] = 0;

        }
        //1.专业知识 2.正向心态 3.辩证思维 4. 自我认知  5.形象礼仪, 6.学业规划 7.安全意识 8.社会认知
        $array =array(
            'nub1'=>'专业知识',
            'nub2'=>'正向心态',
            'nub3'=>'辩证思维',
            'nub4'=>'自我认知',
            'nub5'=>'形象礼仪',
            'nub6'=>'学业规划',
            'nub7'=>'安全意识',
            'nub8'=>'社会认知'
        );
        $numbers = range (1,8);
        //shuffle 将数组顺序随即打乱
        shuffle ($numbers);
        //array_slice 取该数组中的某一段
        $num = empty($_GET['num'])?3:intval($_GET['num']);
        $results = array_slice($numbers,0,$num);
        $result['num'] = $num;
        foreach ($results as $value) {
            $result['list'][$value]=$array['nub'.$value];
        }
        return $this->echo_json(0,'',$result);
    }

    //自我评价
    public function mark(){
        if(empty($_POST['tid'])|| empty($_POST['nubAll']) || !intval($_POST['tid'])){
            return $this->echo_json(140001);
        }
        $uid = $this->uid;
        $tid = intval($_POST['tid']);
        $nuball = $_POST['nubAll'];
        $nuball['oid'] = 1;

        $has = UserTy::where(['id'=>$tid,'uid'=>$uid,'nub1'=>0,'nub2'=>0,'nub3'=>0,'nub4'=>0,'nub5'=>0,'nub6'=>0,'nub7'=>0,'nub8'=>0])->find();
        if($has == null){
            return $this->echo_json(140007);
        }
        $data = UserTy::where(['id'=>$tid,'uid'=>$uid])->update($nuball);
        if($data){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(130002);
        }
    }

    public function experiencereport()
    {
        if(empty($_GET['tid']) || !is_numeric($_GET['tid'])){
            return $this->echo_json(30004);
        }
        $tid = intval($_GET['tid']);
        $uid = $this->uid;

        $planid = UserTy::where('id',$tid)->value('planid');

        $data =Evaluate::experience_report($uid,$tid);

        if($data){
            $data['planid'] = $planid;
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(130002);
        }
    }

    /**
     * 行业动态  体验结束后
     */
    public function dynamic(){
        if(empty($_GET['tid']) || !is_numeric($_GET['tid'])){
            return $this->echo_json(30004);
        }

        $tid = input('get.tid');
        $uid = $this->uid;
        $data =UserTy::where('uid',$uid)->where('id',$tid)->field('ztid,hangye,lx')->find();

        if($data ==null){
            return $this->echo_json(130021);
        }
        $result =[];
        if(!empty($data['ztid'])){
            $result['skip'] = 1;
            return $this->echo_json(0,'',$result);
        }

        $hangyedata =  Caches::HangYeClass();
        $lxdata =  Caches::LxClass();

        $anlitpids = array_column($hangyedata,'tpid');
        $anlitptpnames = array_column($hangyedata,'tpname');
        $hangyedatas = array_combine($anlitpids,$anlitptpnames);

        $lxids = array_column($lxdata,'id');
        $lxitpnames = array_column($lxdata,'tpname');
        $lxdatas = array_combine($lxids,$lxitpnames);



        $result['skip']=0;

        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $scid = $myinfo['scid'];
        //$limit = $this->search_page();
        $limit = 'limit 3';

        $result['video'] ='';
        $result['video_pic'] ='';
        if(!empty($data['hangye'])){
            $hangye = intval($data['hangye']);
            $datas = HangyeDynamic::sel_hangye_new($hangye,'',$scid,$limit);
            $coachids = Coach::belong_hang($hangye);
            $result['title']=$hangyedatas[$hangye];

            $anlidata = Anli_class::where('tpid',$data['hangye'])->field('video,video_pic')->find();
            $result['video'] = $anlidata['video'];
            $result['video_pic'] = $anlidata['video_pic'];
        }elseif(!empty($data['lx'])){
            $lx = intval($data['lx']);
            $datas = HangyeDynamic::sel_lx_new($lx,$scid,$limit);
            $coachids = Coach::belong_lx($lx);
            $result['title']=$lxdatas[$lx];
            $lxclassdata =Lx_class::where('id',$data['lx'])->field('video,video_pic')->find();
            $result['video'] =$lxclassdata['video'];
            $result['video_pic'] =$lxclassdata['video_pic'];
        }

        if(empty($datas['data'])){
            $result['newlist'] ='';
        }else{
            $result['newlist'] =$datas['data'];
        }

        $coachlist = Coach::dynamcoachlist($coachids);

        $result['coachlist'] = $coachlist?$coachlist:'';

        return $this->echo_json(0,'',$result);
    }

    /**
     *今日报告
     */
    public function todayReport(){
        $uid = $this->uid;
        // 体验进度
        $pro = Label::plan_pro($uid);
        $pro = $pro ? $pro:0;
        //今日经验

        $result['pro'] = $pro;
        $result['todayjifen'] = Evaluate::dotay_intergral($uid);
        $result['sumjifen'] = Evaluate::rep_integral($uid);
        $result['competence'] = Evaluate::competency($uid);
        $result['pass'] = 4;

        return $this->echo_json(0,'',$result);
    }




}
