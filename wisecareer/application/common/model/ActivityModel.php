<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class ActivityModel extends Model
{
    public static $table4 ='experience';
    public static  $table1 ='experience_activity';
    public static $table2 ='experience_enter';
    public static  $table3 ='experience_coachenter';

    /**
     * @param $userid
     * @param $tid
     * @return bool|static
     */
    public static  function file_img($userid, $tid)
    {

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->validate(['size'=>10000000,'ext'=>'jpg,png,gif'])->move( '/data/www/pic/scene/');
        if($info){
            $pic['work_pic'] ="/pic/scene/".$info->getSaveName();;

            $result  = self::name(self::$table2)->where('userid',$userid)->where('tid',$tid)->update($pic);
            return $result;
        }else{
            return false;
            // 上传失败获取错误信息
           // echo $file->getError();
        }
//        $up = new FileUploadModel();
//        $up -> set("maxsize", 10000000);
//        $up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
//        $up -> set("path", "/data/www/pic/scene/");
//
//        if($up -> upload("file")) {
//            $pic['work_pic'] ="/pic/scene/".$up->getFileName();
//            $result =$this->update($this->table2,$pic,['AND'=>['userid'=>$userid,'tid'=>$tid]]);
//            return $result;
//        }
        //return false;
        # code...
    }


    /**
     * @param $uid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function sel_activity($uid,$myinfofiled){
        $time = time();
        $data = self::name(self::$table4)->alias('t1')
                ->leftJoin(self::$table1 .' t2 ',' t1.id = t2.eid')
                ->where('t2.open',0)
                ->where('t2.ksdate','>',$time)
                ->field('t1.address,t2.user_type,t1.scid,t1.sdid,t2.scid as scids,t2.sdid as sdids,t2.titlename,t2.content,t2.ksdate,t2.jsdate,t2.srate,t2.id,t2.eid,t2.usernum')
                ->select();


        if($data ==null) return false;

        $AreaData = Caches::AreaAll();
        $money = Myuser::userBalance($uid);


        foreach ($data as $key => $value) {

            if($value['user_type']==1 && $myinfofiled['ol_type']==1){
                unset($data[$key]); continue;
            }elseif($value['user_type']==2 && !in_array($myinfofiled['closslx'],[3,9]) ){
                unset($data[$key]); continue;
            }elseif($value['user_type']==3 && !in_array($myinfofiled['closslx'],[15,32]) ){
                unset($data[$key]); continue;
            }

            if($value['scids']!='' && $value['scids']!=$myinfofiled['scid'] ){
                unset($data[$key]); continue;
            }

            $has = self::has_accept($uid,$value['id']);
            if($has && ($time + 1200 >= $value['ksdate']) ){
                unset($data[$key]);
                continue;
            }
            $data[$key]['hasaccept'] = $has;
            $data[$key]['ksdate'] = date('Y-m-d H:i:s',$value['ksdate']);
            $data[$key]['jsdate'] = date(' Y-m-d H:i:s',$value['jsdate']);
            //			$data[$key]['date'] = date('Y-m-d',$value['ksdate']);
            $data[$key]['money'] = $money;
            $data[$key]['usernum'] = $value['usernum'];
            $data[$key]['sc']=empty($AreaData[$value['scid']])?'':$AreaData[$value['scid']];
            $data[$key]['sd']=empty($AreaData[$value['sdid']])?'':$AreaData[$value['sdid']];
            $data[$key]['address'] = $value['address'];
            $data[$key]['num'] = self::experience_enter_nums($value['id']);
        }


        return $data;
    }


    /**
     * @param $uid
     * @param $tid
     * @return bool|null|static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function leave($uid, $tid)
    {
        $data = self::name(self::$table2)->where(['tid'=>$tid,'endtime'=>0,'userid'=>$uid])->field('id,uid,eid,tid,has_verify,content1,content2,content3,datetime,endtime,work_pic')->order('id','desc')->find();
        if(	$data ==null){
            return false;
        }

        $data['content1'] = utf8_filter(urldecode($data['content1']));
        $data['content2'] =  utf8_filter(urldecode($data['content2']));
        $data['content3'] =  utf8_filter(urldecode($data['content3']));


        $data['title']= self::name(self::$table4)->where('id',$data['eid'])->value('titlename');
        $v = self::name(self::$table1)->where('id',$data['tid'])->field('titlename,jsdate')->find();
        $data['titlename']=$v['titlename'];
        $data['datetime'] = date('Y-m-d H:i:s',$data['datetime']);
        if(empty($data['endtime'] ) ){
            $data['endtime'] = date('Y-m-d H:i:s',$v['jsdate']);
        }else{
            $data['endtime'] = date('Y-m-d H:i:s',$data['endtime']);
        }
        $s =self::getindex($data['tid'],$uid);
        if($data['uid']){
            $data['coach_nickname'] = Myuser::whereid($uid)->value('user_name');
        }else{
            $data['coach_nickname'] = '';
        }
        $data['work_has'] =$data['work_pic']? 1:0;
        $data['rcom_has'] =( self::name('student_recommen')->where(['tid'=>$data['tid'],'uid'=>$data['uid'],'userid'=>$uid])->find()  ==null)?0:1;
        $data['count'] = count($s);
        $s =self::getindex($data['tid'],$uid);
        $data['list'] = $s;
        return $data;
    }

    /**
     *
     * @param $uid
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static  function Internship_record($uid)
    {
        $data = self::name(self::$table2)->where('userid',$uid)->where('datetime','neq',0)->field('id,uid,eid,tid,datetime,has_verify,invi_datetime,endtime,work_pic')->order('id','desc')->select();
        if(	$data==null){
            return false;
        }
        foreach ($data as $key => $value) {
            // if($this->get($this->table1,'jsdate',['id'=>$value['tid']]) < $this->time ){
            // 	unset($data[$key]);
            // 	continue;
            // }
            $data[$key]['title']= self::name(self::$table4)->where('id',$value['eid'])->value('titlename');
            $v = self::name(self::$table1)->where('id',$value['tid'])->field('titlename,jsdate')->find();
            $data[$key]['titlename']=$v['titlename'];
            $data[$key]['datetime'] =$value['datetime']? date('Y-m-d H:i:s',$value['datetime']):"";
            $data[$key]['endtime'] =$value['endtime']? date('Y-m-d H:i:s',$value['endtime']):"";
            $data[$key]['has_verify'] =$value['has_verify'];

            $s =self::getindex($value['tid'],$uid);
            $data[$key]['count'] = count($s);
            $data[$key]['list'] = $s;
        }
        return $data;
    }

    /**历史邀约
     * @param $uid
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function history($uid)
    {
        $data = self::name(self::$table2)->where('userid',$uid)->field('id,uid,eid,tid,datetime,invi_datetime,endtime')->order('id','desc')->select();


        if(	$data ==null){
            return false;
        }
        $time = time();
        foreach ($data as $key => $value) {
            $jstime = self::name(self::$table1)->where('id',$value['tid'])->value('jsdate');
            if( $jstime < $time ){
                unset($data[$key]);
                continue;
            }
            $data[$key]['title']=self::name(self::$table4)->where('id',$value['eid'])->value('titlename');
            $v = self::name(self::$table1)->whereid($value['tid'])->field('titlename,srate') ->find();


            $data[$key]['titlename']=$v['titlename'];
            $data[$key]['datetime'] =$value['datetime']? date('Y-m-d H:i:s',$value['datetime']):"";
            $data[$key]['endtime'] =$value['endtime']? date('Y-m-d H:i:s',$value['endtime']):"";
            $data[$key]['invi_datetime'] =$value['invi_datetime']? date('Y-m-d H:i:s',$value['invi_datetime']):"";
            $data[$key]['srate'] =$v['srate'];

            if($value['invi_datetime'] && $value['datetime']){
                $data[$key]['status'] = 1;
            }else if( self::name(self::$table1)->whereid($value['tid'])->value('ksdate') > $time){
                $data[$key]['status'] = 3;
            } else{
                $data[$key]['status'] = 2;
            }
        }
        return $data;
    }

    /**
     * @param $tid
     * @param $uid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getindex($tid, $uid)
    {
        $result = [];
        $data =self::name('app_user_site_base')->where('ex_act_id',$tid)->where('userid',$uid)->field('uid,baseid,jifen')->select();
        if($data ==null) return false;
        foreach ($data as $key => $value) {
            $result[$key]['jifen']  = $value['jifen'];
            $result[$key]['basetitle']   = self::name('base_index')->where('id',$value['baseid'])->value('titlename');
            $data1 =self::name('center_user')->where('oid',2)->where('id',$value['uid'])->field('id,uid')->find();
            $result[$key]['coach_nickname']= self::name('app_member')->where('id',$data1['uid'])->value('coach_nickname');
        }
        return $result;

    }
    /**
     * @param $tid
     * @return int|string
     */
    public static function experience_enter_nums($tid)
    {
        return self::name(self::$table2)->where('tid',$tid)->where('invi_datetime','neq',0)->count();
    }
    //
    public static function has_accept_invi($uid,$tid)
    {
        return self::name(self::$table2)->where(['tid'=>$tid,'userid'=>$uid,'invi_datetime'=>0])->find();
    }


    /**是否接受邀约
     * @param $uid
     * @param $tid
     * @return \think\db\Query
     */
    public static function has_accept($uid, $tid)
    {

        $has = self::name(self::$table2)->where('tid',$tid)->where('userid',$uid)->where('invi_datetime','neq',0)->find();
        if($has ==null) return false;
        return $has;
    }


    //接受接受邀约
    public static function accept_invitation($array,$usernum)
    {
        $num = self::name(self::$table2)->where('tid',$array['tid'])->where('invi_datetime','neq',0)->count();
        if($num>=$usernum){
            return false;
        }
        $array['invi_datetime'] =time();
        $has = self::name(self::$table2)->where(['tid'=>$array['tid'],'userid'=>$array['userid']])->find();
        if($has !=null){
            $res = self::name(self::$table2)->where(['tid'=>$array['tid'],'userid'=>$array['userid']])->update($array);
        }else{
            $res =  self::name(self::$table2)->insertGetId($array);
        }
        return $res;

    }


    /**取消邀约
     * @param $array
     * @return bool|static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function cancel_invitation($array)
    {
        $has =self::name(self::$table2)->where('tid',$array['tid'])->where('datetime',0)->where('userid',$array['userid'])->where('invi_datetime','neq',0)->find();
        if($has !=null){
            $res =self::name(self::$table2)->where(['tid'=>$array['tid'],'userid'=>$array['userid']])->update(['invi_datetime'=>0]);
            return $res;
        }
        return false;

    }

    /**
     * @param $tid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_srate_usernum($tid)
    {
        return self::name(self::$table1)->where('id',$tid)->field('titlename,enter,srate,usernum,coachjifen')->find();
    }

    /**
     * @param $tid  查询空间名 以及 活动信息
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_exper_activ_all($tid)
    {
        return self::name(self::$table1)->alias('t1')
            ->leftJoin(self::$table4 . ' t2','t1.eid = t2.id')
            ->where('t1.id',$tid)->field('t2.titlename as title,t1.titlename,t1.ksdate,t1.enter,t1.srate,t1.usernum,t1.coachjifen')->find();
    }

    /**反馈意见
     * @param $array
     * @param $where
     * @return static
     */
    public static function user_base_opinion($array, $where)
    {
        return self::name('app_user_site_base')->where($where)->update($array);

    }

    //判断是否开始
    public static function verify_enter_time($uid,$code,$has_invi_tid)
    {
        $data = self::name(self::$table1)->where('enter',$code)->where('id',$has_invi_tid)->field('id,ksdate')->find();
        if($data ==null){
            return false;
        }
        return $data;
    }
    //判断入场码是否正确

    //验证 进场码正确么

    public function verify_enter($uid,$code,$has_invi_tid)
    {

        // $has_invi_tid=$this->select($this->table2,'tid',['AND'=>['uid'=>$uid,'invi_datetime[!]'=>0,'endtime'=>0]]);

        // if(!$has_invi_tid){
        // 	return 1;
        // }

        $tid = $this->get($this->table1,'id',['AND'=>['ksdate[<=]'=>$this->time,'jsdate[>]'=>$this->time,'enter'=>$code,'id'=>$has_invi_tid]]);

        if(!$tid){
            return false;
        }

        return $tid;

        //return  $this->get($this->table2.'(t2)',["[>]$this->table1(t1)"=>['t2.tid'=>'id']],'t2.tid',['AND'=>['t2.userid'=>$uid,'t2.invi_datetime[!]'=>0,'t1.enter'=>$code,'t1.ksdate[<]'=>$this->time,'t1.jsdate[>]'=>$this->time]]);

    }




    /**进入 活动
     * @param $uid
     * @param $tid
     * @return bool|static
     */
    public static function insert_activity($uid, $tid)
    {
        $has = self::name(self::$table2)->where('userid',$uid)->where('tid',$tid)->where('datetime','neq',0)->find();
        if($has !=null){
            return false;
        }
        $count = self::name(self::$table2)->where('tid',$tid)->where('datetime','neq',0)->count();
        $count++;
        $time = time();
        return self::name(self::$table2)->where(['userid'=>$uid,'tid'=>$tid])->update(['datetime'=>$time,'number'=>$count]);

    }

    /**写入 离场时间
     * @param $userid
     * @param $tid
     * @return static
     */
    public static function degree_endtime($userid, $tid){
        $time = time();
        return self::name(self::$table2)->where('userid',$userid)->where('tid',$tid)->update(['endtime'=>$time]);
    }

    //写入 实习程度
    public static function insert_degree($userid,$tid,$degree, $content = '')
    {
        if(!self::has_time_activity($tid)){
            return false;
        }
        if($content != ''){
            self::name(self::$table2)->where(['userid'=>$userid,'tid'=>$tid])->update($content);
        }
        return self::name(self::$table2)->where(['userid'=>$userid,'tid'=>$tid])->update(['degree'=>$degree]);
    }

    public static function update_degree($userid,$tid,$degree,$uid )
    {
        if(!self::has_time_activity($tid)){
            return false;
        }
        return self::name(self::$table2)->where(['userid'=>$userid,'tid'=>$tid])->update(['degree'=>$degree,'uid'=>$uid]);
    }

    //活动是否到期
    public static function has_time_activity($tid)
    {
        $time = time();
        $has  = self::name(self::$table1)->where('id',$tid)->where('jsdate','>',$time)->find();
        if ($has ==null) return false;
        return true;

    }


    /**
     * @param $uid
     * @return bool|null|static
     * @throws \think\exception\DbException
     */
    public static function getin_activity($uid)
    {
        //,'datetime[!]'=>0,'endtime'=>0

        $time = time();
        $data1 = self::name(self::$table2)->where('userid',$uid)->where('datetime','neq',0)->where('endtime',0)->where('invi_datetime','neq',0)->order('datetime','desc')->find();

        $data = self::name(self::$table1)->alias('t1')
            ->leftJoin(self::$table4.' t','t1.eid = t.id')
            ->where('t1.id',$data1['tid'])
            ->where('t1.ksdate','<',$time)
            ->where('t1.jsdate','>',$time)
            ->field('t1.titlename,t.titlename as tname')
            ->find();
        if($data ==null){
            return false;
        }

        $data['number'] = $data1['number'];
        $data['tid'] = $data1['tid'];
        $data['degree'] = $data1['degree'];
        return $data;
    }

    /**空间人
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function field_space($uid)
    {
        $data = self::name(self::$table2)->where('userid',$uid)->where('datetime','neq',0)->where('endtime',0)->field('tid,datetime')->order('id','desc')->find();
        if(!$data['datetime']){
            return false;
        }
        $tid = $data['tid'];
        $time = time();

        $titlenamedata = self::name(self::$table1)->where('id',$tid)->where('ksdate','<=',$time)->where('open',0)->where('jsdate','>=',$time)->field('prepare,jsdate,titlename,content,syget,target,type2,type3')->find();

        if(!$titlenamedata){
            return false;
        }

        $uids = self::name(self::$table3)->where('tid',$tid)->where('uid','neq',$uid)->where('datetime','neq',0)->column('uid');
        if($uids){
            $result['has_coach']=1;

            $result['name']  = Coach::coach_info($uids);
        }else{
            $result['has_coach']=0;
        }

        //教练

        $array1 = explode(',',$titlenamedata['syget']);
        $array2 = explode(',',$titlenamedata['target']);
        $array3 = explode(',',$titlenamedata['type2']);
        $array4 = explode(',',$titlenamedata['type3']);
        $array5 = array_merge($array1,$array2,$array3,$array4);

        $index_base = self::name('base_index')->wherein('id',$array5)->column('titlename');
        $result['basestr'] =implode(',',$index_base);

        $result['titlename'] =$titlenamedata['titlename'];
        $result['content'] =$titlenamedata['content'];
        $result['prepare'] =$titlenamedata['prepare'];
        $result['jsdate'] =date('Y-m-d H:i:s',$titlenamedata['jsdate']);
        $result['tid'] =$tid;

        $userids = self::name(self::$table2)->where('datetime','neq',0)->where('tid',$tid)->value('userid');
        //学员



        $userdata=   Myuser::wherein('id',$userids)->field('user_name,pic')->select();
        foreach ($userdata as $key =>$value){
            if(!$value['pic']){ $userdata[$key]['pic']='http://jiaolian.wisecareer.org/public/img/user-image.png';
            }else{
                $userdata[$key]['pic'] = "http://www.wisecareer.org/od/".$value['pic'];
            }

        }
        $result['list']  =$userdata;
        $result['eval_base'] = self::name('app_user_site_base')->alias('ub')
            ->leftJoin('base_index bi','ub.baseid = bi.id')
            ->where('ub.ex_act_id',$tid)
            ->where('userid',$uid)
            ->field('ub.id,ub.content,ub.jifen,bi.titlename,ub.opinion')
            ->order('ub.datetime','desc')->select();
        $result['degree']  = self::name(self::$table2)->where(['tid'=>$tid,'userid'=>$uid])->value('degree');
        return $result;
    }


    // 实习记录
    public static function scene_history($uid)
    {
        return self::name(self::$table2)->alias('t2')
            ->leftJoin(self::$table1.'t1','t2.tid = t1.id')
            ->where('t2.userid',$uid)
            ->field('t2.datetime,t2.degree,t1.titlename')
            ->select();
        # code...
    }


    // 学生报告记录
    public function record($userid)
    {
        $data1 =$this->select($this->table2,['eid','tid','uid','datetime'],['AND'=>['userid'=>$userid,'degree'=>4],'ORDER'=>['datetime'=>'DESC'],'LIMIT'=>1]);

        $result=[];

        foreach ($data1 as $key => $value) {
            $result[$key]=  $this->get($this->table1,['titlename','content'],['id'=>$value['tid']]) ;
            $result[$key]['title']=$this->get($this->table,'titlename',['id'=>$value['eid']]);
            $result[$key]['datetime']=date('Y-m-d',$value['datetime']);
            $result[$key]['list']  = $this->recom_evalt($userid,$value['uid'],$value['tid']);

        }


        return $result;

    }


    //教练 评价
    public function recom_evalt($userid,$uid,$tid){
        $userModel = new userModel();
        $app_jf = new app_jfModel();
        $data =$this->get('student_recommen','content',['AND'=>['tid'=>$tid,'userid'=>$userid]]);
        //foreach ($data as $key => $value) {
        $result[0]['name']= $userModel->userInfo($uid,'user_name');
        $result[0]['pic']= $userModel->userInfo($uid,'pic');
        $result[0]['content']= $data;
        $result[0]['jifen']= $this->sum('app_jf','jifen',['uid'=>$uid]);

        $grade = $app_jf->grades($uid);
        $result[0]['grade']= grade($grade,20);
        //$result[$key]['datetime']=date('Y-m-d',$value['datetime']);
        $result[0]['base_list']=$this->site_base_user($userid,$uid,$tid);
        //}


        return $result;
    }


    // 评价标签
    public function site_base_user($userid,$uid,$tid)
    {
        return $this->select('app_user_site_base(b1)',['[>]base_index(b2)'=>['b1.baseid'=>'id']],['b2.titlename','b1.content','b1.jifen'],['AND'=>['b1.uid'=>$uid,'b1.userid'=>$userid,'b1.ex_act_id'=>$tid,'b1.jifen[>=]'=>5]]);
    }


    public function site_base_users($userid,$tid)
    {
        return $this->select('app_user_site_base(b1)',['[>]base_index(b2)'=>['b1.baseid'=>'id']],['b2.titlename','b1.content','b1.jifen'],['AND'=>['b1.userid'=>$userid,'b1.ex_act_id'=>$tid]]);
    }

    //报告附件
    public function accessory($userid)
    {
        $userModel =new userModel();
        $data1 =$this->select($this->table2,['eid','uid','tid','datetime','work_pic'],['userid'=>$userid,'ORDER'=>['datetime'=>'DESC']]);

        $result=[];

        foreach ($data1 as $key => $value) {

            $result[$key]['title']=$this->get($this->table1,'titlename',['id'=>$value['tid']]);
            $result[$key]['pic']="http://www.wisecareer.org/od/".$this->get('experience_pic','pic',['eid'=>$value['eid']]);
            $result[$key]['titlename']=$this->get($this->table,'titlename',['id'=>$value['eid']]);
            $result[$key]['work_pic']=$value['work_pic'];
            $result[$key]['datetime']=date('Y-m-d',$value['datetime']);
            $result[$key]['content']=$this->get($this->table1,'content',['id'=>$value['tid']]);
            $result[$key]['name']= $userModel->userInfo($value['uid'],'user_name');
            $result[$key]['recom']= $this->count('student_recommen',['AND'=>['tid'=>$value['tid'],'userid'=>$userid]]);
            $result[$key]['atten']= $this->count('student_attention',['AND'=>['tid'=>$value['tid'],'userid'=>$userid]]);
            $result[$key]['list']  = $this->site_base_users($userid,$value['tid']);

        }


        return $result;
    }

    //推荐教练
    public function recom_coach_list($userid){
        $userModel = new userModel();
        $app_jf = new app_jfModel();
        $data_uids = $this->select('student_recommen',['uid','tid','datetime','content'],['userid'=>$userid,'ORDER'=>['datetime'=>'DESC']]);
        $result=[];
        foreach ($data_uids as $key => $value) {
            $result[$key]['title']= $this->get($this->table1,'titlename',['id'=>$value['tid']]);
            $result[$key]['datetime']= date('Y-m-d',$value['datetime']) ;

            $result[$key]['name']= $userModel->userInfo($value['uid'],'user_name');
            $result[$key]['content']= $value['content'];
            $result[$key]['pic']= $userModel->userInfo($value['uid'],'pic');
            $result[$key]['phone']= $userModel->userInfo($value['uid'],'shouji');
            $result[$key]['gongl']=getAge($userModel->userInfo($value['uid'],'gongl')) ;
            $label = $userModel->Label($value['uid']);
            $labelstr = '';
            foreach ($label as $k => $val) {
                $labelstr .=$val['tpname'].',';
            }
            $labelstr = rtrim($labelstr,',');
            $result[$key]['label']= $labelstr;
            $result[$key]['jifen']= $this->sum('app_jf','jifen',['uid'=>$value['uid']]);
            $grade = $app_jf->grades($value['uid']);
            $result[$key]['grade']= grade($grade,20);

        }


        return $result;
    }


    //
    public function recom_hangye($userid)
    {
        $name = $this->get('myuser','user_name',['id'=>$userid]);
        $sql ="SELECT hangye, TRUNCATE(sum(defen),2)  as defen, sum(zdefen) as zdefen   FROM  kw_user_ty  WHERE uid = {$userid} and hangye neq0 GROUP BY hangye ";


        $sql1 = "SELECT ut.hangye, sum(de.defen) as defen  FROM  kw_user_ty ut LEFT JOIN kw_ty_user_defen de on ut.id =de.userty WHERE ut.uid = {$userid} and ut.hangye neq0  GROUP BY ut.hangye";


        $data = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $data1 = $this->query($sql1)->fetchAll(\PDO::FETCH_ASSOC);

        $key = array_column($data1,'hangye');
        $val = array_column($data1,'defen');
        $datas = array_combine($key, $val);
        $array=[];
        foreach ($data as $key => $value) {
            if(!empty($datas[$value['hangye']])){
                $array[$value['hangye']]=@round((($value['defen'] + $datas[$value['hangye']])/$value['zdefen'])*100,2);
            }else{
                $array[$value['hangye']]=@round(($value['defen'] / $value['zdefen'])*100,2);
            }


        }


        arsort($array);

        $array1  = array_keys($array);
        $array2 = array_values($array);

        $array1  =array_slice($array1,0,5);
        $array2 =array_slice($array2,0,5);




        $hangyestr = implode(',',$array1);


        $sqlz ="SELECT hangye, TRUNCATE(sum(defen),2)  as defen, sum(zdefen) as zdefen   FROM  kw_user_ty  WHERE hangye in({$hangyestr}) and hangye neq0 GROUP BY hangye ";


        $sqlz1 = "SELECT ut.hangye, sum(de.defen) as defen  FROM  kw_user_ty ut LEFT JOIN kw_ty_user_defen de on ut.id =de.userty WHERE ut.hangye in({$hangyestr}) and ut.hangye neq0  GROUP BY ut.hangye";


        $dataz = $this->query($sqlz)->fetchAll(\PDO::FETCH_ASSOC);
        $dataz1 = $this->query($sqlz1)->fetchAll(\PDO::FETCH_ASSOC);

        $keyz = array_column($dataz1,'hangye');
        $valz = array_column($dataz1,'defen');
        $datazs = array_combine($keyz, $valz);
        $arrayz=[];
        foreach ($dataz as $key => $value) {
            if(!empty($datazs[$value['hangye']])){
                $arrayz[$value['hangye']]=round((($value['defen'] + $datas[$value['hangye']])/$value['zdefen'])*100,2);
            }else{
                $arrayz[$value['hangye']]=round(($value['defen'] /$value['zdefen'])*100,2);
            }


        }

        $arrayzh = [];
        foreach ($array1 as $key => $value) {
            $arrayzh[] = $arrayz[$value];
        }



        $result =$this->select('anli_class','tpname',['tpid'=>$array1]);

        $re['title'] = $result;
        $re['data'] =array(array('name'=>$name,'data'=>$array2),array('name'=>"平均值",'data'=>$arrayzh));



        return $re;
    }


    public function recom_zn($userid)
    {
        $name = $this->get('myuser','user_name',['id'=>$userid]);



        $sql ="SELECT lx, TRUNCATE(sum(defen),2)  as defen, sum(zdefen) as zdefen   FROM  kw_user_ty  WHERE uid = {$userid} and lx neq0 GROUP BY lx ";



        $sql1 = "SELECT ut.lx, sum(de.defen) as defen  FROM  kw_user_ty ut LEFT JOIN kw_ty_user_defen de on ut.id =de.userty WHERE ut.uid = {$userid} and ut.lx neq0  GROUP BY ut.lx";


        $sqlz ="SELECT lx, TRUNCATE(sum(defen),2)  as defen, sum(zdefen) as zdefen   FROM  kw_user_ty  WHERE  lx neq0 and lx neq8 GROUP BY lx ";

        $sqlz1 = "SELECT ut.lx, sum(de.defen) as defen  FROM  kw_user_ty ut LEFT JOIN kw_ty_user_defen de on ut.id =de.userty WHERE ut.lx neq8 and ut.lx neq0  GROUP BY ut.lx";

        $dataz = $this->query($sqlz)->fetchAll(\PDO::FETCH_ASSOC);
        $dataz1 = $this->query($sqlz1)->fetchAll(\PDO::FETCH_ASSOC);


        $keyz = array_column($dataz1,'lx');
        $valz = array_column($dataz1,'defen');
        $datazs = array_combine($keyz, $valz);


        $arrayz=[];
        foreach ($dataz as $key => $value) {
            if(!empty($datazs[$value['lx']])){
                $arrayz[$value['lx']]=round((($value['defen'] + $datazs[$value['lx']])/$value['zdefen'])*100,2);
            }else{
                $arrayz[$value['lx']]=round(($value['defen'] /$value['zdefen'])*100,2);
            }


        }






        $data = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        $data1 = $this->query($sql1)->fetchAll(\PDO::FETCH_ASSOC);

        $key = array_column($data1,'lx');
        $val = array_column($data1,'defen');
        $datas = array_combine($key, $val);
        $array=[];
        foreach ($data as $key => $value) {
            if(!empty($datas[$value['lx']])){
                $array[$value['lx']]=round((($value['defen'] + $datas[$value['lx']])/$value['zdefen'])*100,2);
            }else{
                $array[$value['lx']]=round(($value['defen'] /$value['zdefen'])*100,2);
            }

        }




        // $array1  = array_keys($array);
        // $array2 = array_values($array);

        $name_array=[];
        foreach ($arrayz as $key => $value) {
            if(!empty($array[$key] )){
                $name_array[]  = $array[$key];
            }else{
                $name_array[] =0;
            }
        }

        $arrayzk= array_keys($arrayz);
        $arrayzv= array_values($arrayz);

        // $array  =array_slice($array,);
        // $array1 =array_slice($array1,0,2);

        $result =$this->select('lx_class','tpname',['id'=>$arrayzk]);

        $re['title'] = $result;
        $re['data'] = array(array('name'=>$name,'data'=>$name_array),
            array('name'=>"平均值",'data'=>$arrayzv) );



        return $re;
    }

    // 能力组合
    public function base_user($userid)
    {
        $name = $this->get('myuser','user_name',['id'=>$userid]);
        $sql ="SELECT baseid,avg(jifen) as jifen FROM `kw_app_user_site_base` WHERE userid ={$userid} GROUP BY baseid  ORDER BY jifen desc limit 5";
        $data = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        if(!$data){
            return false;
        }
        $key = array_column($data,'baseid');
        $val = array_column($data,'jifen');


        foreach ($val as $k=> $v) {
            $val[$k] = intval($v);
            # code...
        }
        $keystr =implode(',',$key);

        $sqlz ="SELECT baseid,avg(jifen) as jifen FROM `kw_app_user_site_base` WHERE baseid in ({$keystr}) GROUP BY baseid  ORDER BY jifen desc limit 5";




        $dataz = $this->query($sqlz)->fetchAll(\PDO::FETCH_ASSOC);

        $keyz = array_column($dataz,'baseid');
        $valz = array_column($dataz,'jifen');
        $datasz = array_combine($keyz,$valz);


        $pingjun=[];
        foreach ($key as $k=> $v) {

            $pingjun[]= intval($datasz[$v]);
        }



        $result=$this->select('base_index','titlename',['id'=>$key]);

        $re['title'] = $result;
        $re['data'] = array( array('name'=>$name,'data'=>$val), array('name'=>'平均值','data'=>$pingjun) );



        return $re;
    }

    /**评价分数
     * @param $userid
     * @return array|bool
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function get_based_fen($userid)
    {
        $sql ="SELECT SUM(kw_b1.jifen) as sum,type FROM kw_app_user_site_base AS kw_b1 LEFT JOIN kw_base_index AS kw_b2 ON kw_b1.baseid = kw_b2.id WHERE kw_b1.userid = '{$userid}' GROUP BY kw_b2.type";

        $data = self::query($sql);
        if($data ==null){
            return false;
        }
        $key = array_column($data[0],'type');
        $value = array_column($data[0],'sum');
        $result = array_combine($key,$value);
        return $result;
    }

}

