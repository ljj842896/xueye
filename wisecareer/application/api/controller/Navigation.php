<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\ActivityModel;
use app\common\model\AppUserEvaluate;
use app\common\model\Evaluate;
use app\common\model\Navigationa;
use app\common\model\Recommend;
use app\common\model\Shcool;
use app\common\WechatCallback;
use app\index\controller\Action;
use think\Controller;
use think\Db;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Navigation extends Action
{

    public function getuserwensr(){
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $viptype = $myinfo['viptype'];
        $data = Caches::EduWenSrUid($uid,1);
        unset($data['uid']);
        unset($data['id']);
        $SchoolSetIddata = Caches::SchoolSetId($schoolid);

        if( $viptype ==1 && !empty($SchoolSetIddata) && ( !empty($SchoolSetIddata['xk_strong']) || !empty($SchoolSetIddata['xk_feeble'])) ){
            $data['strong_feeble'] = 1;
        }else{
            $data['strong_feeble'] = 0;
        }
        return $this->echo_json(0,'',$data);
    }

    /**
     * 做决策 模型
     */
    public function makeplanning(){
        $uid = $this->uid;
        $inarray = ['list1','list6','list7','list8','list9','list10','list11','list12','list13','list14','list21','list22','list23'];
        $updatedata = [];
        foreach ($this->request->param() as $key =>$value){
                if(in_array($key,$inarray)){
                    $updatedata[$key] = intval($value);
                }
        }

        if(empty($updatedata)){
            return $this->echo_json(15013);
        }

        if(  $this->request->param('list6')==='0' ){
            $updatedata['list7'] =0 ;  $updatedata['list8'] =0;
        }elseif(!empty($this->request->param('list6'))){
            if($this->request->param('list6') !=0 &&  empty( $updatedata['list7'] ))  $updatedata['list7'] =0;
            if($this->request->param('list6') !=0 &&  empty( $updatedata['list8'] ))  $updatedata['list8'] =0;
        }


        if( $this->request->param('list9')==='0' ){
            $updatedata['list10'] =0 ;  $updatedata['list11'] =0;
        }elseif(!empty($this->request->param('list9'))){
            if($this->request->param('list9') !=0 &&  empty( $updatedata['list10'] ))  $updatedata['list10'] =0;
            if($this->request->param('list9') !=0 &&  empty( $updatedata['list11'] ))  $updatedata['list11'] =0;
        }

        if( $this->request->param('list12')==='0'){
            $updatedata['list13'] =0 ;  $updatedata['list14'] =0;
        }elseif(!empty($this->request->param('list12'))){
            if($this->request->param('list12') !=0 &&  empty( $updatedata['list13'] ))  $updatedata['list13'] =0;
            if($this->request->param('list12') !=0 &&  empty( $updatedata['list14'] ))  $updatedata['list14'] =0;
        }

        $res =Navigationa::makeEduwensr($uid,$updatedata);

        return $this->echo_json(0);


    }
    /**
     *      智能学业导航
     */
    public function planning(){
        $uid =$this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $viptype = $myinfo['viptype'];
        $classid = $myinfo['classname'];
        $subject = $myinfo['subject'];
        $SchoolSetIddata = Caches::SchoolSetId($schoolid);

        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);

        $result=Navigationa::get_planning($uid,$schoolid,$viptype,$classid);
        if(!$result){
            return   $this->echo_json(90003);
        }
        if($subject){
            $EduGkClassdata = Caches::EduGkClass();
            $result['subject']= preg_replace("/#/","-",$EduGkClassdata[$subject]);
        }else{
            $result['subject'] ='空';
        }

        $result['subjectno'] = 0;
        $result['subjectnomsg'] ='';
        $data = Navigationa::get_combination($uid,$schoolid,$subject,$viptype,$classid);
        if(!empty($data)){
            $result['nagigt'] = 1;
            foreach($data['arrno'] as $value){
                if($value['tpid'] == $subject){
                    $result['subjectno'] = 1;
                    $result['subjectnomsg'] = "你的选科方案与学业决策参数存在冲突，建议调整！";
                }
            }
        }else{
            $result['nagigt'] = 0;
        }

        if( $viptype == 1 && !empty($SchoolSetIddata) && ( !empty($SchoolSetIddata['xk_strong']) || !empty($SchoolSetIddata['xk_feeble'])) ){
            $result['strong_feeble'] = 1;
        }else{
            $result['strong_feeble'] = 0;
        }

       return $this->echo_json(0,'',$result);
    }


    /**
     * 选科决策 修改
     */
    public function planningedit(){
        $uid =$this->uid;
        $data  =Navigationa::planningedit($uid);
        if($data == false){
            return $this->echo_json(50012);
        }

        return $this->echo_json(0,'',$data);
    }
    /**
     * 选科方案
     */
    public function combination(){
        $uid = $this->uid;//$this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $subject = $myinfo['subject'];
        $viptype = $myinfo['viptype'];
        $classid = $myinfo['classname'];

        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];


        $data = Navigationa::get_combination($uid,$schoolid,$subject,$viptype,$classid);
        if($status!=1  &&  $status !=2 ){
            if(!empty($data['arrfuhe'])){
                $data['arr_yiban'] = array_slice($data['arrfuhe'],0,1);
            }else{
                $data['arr_yiban'] = array_slice($data['arrqita'],0,1);
            }

            $data['arrqita'] =[];
            $data['arrfuhe']=[];
            $data['arrno'] = [];
        }


        $data['subject']= $subject;
        $EduGkClassdata   = Caches::EduGkClass();
        if($subject){
            $data['subjectmsg']=  empty($EduGkClassdata[$subject])?'': preg_replace("/#/","-",$EduGkClassdata[$subject]);
        }else{
            $data['subjectmsg'] ='无';
        }
       return $this->echo_json(0,'',$data);
    }

    /**符合 专业 推荐专业
     * @return \think\response\Json
     */
    public function get_like_wen(){

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];

        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        $limit = $this->search_page();
        if($status!=1  &&  $status !=2 ){
            $limit = ' LIMIT 3 ';
        }

        $type = empty($this->request->param('type'))?1:intval($this->request->param('type'));

        $result = Navigationa::recommended_profession($uid,$schoolid,$type,$limit);
        $num  = Navigationa::recommended_profession($uid,$schoolid,$type);

        if(!empty($result['list'])){
            $result['num'] = $num;
        }else{
            $result['list'] = null;
            $result['num'] = 0;
        }
        return $this->echo_json(0,'',$result);
    }
    /**
     *报告接口
     */
    public function presentation(){

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $myinfoall = Caches::MyuserInfo($uid);
        $schoolid  = $myinfo['schoolname'];
        $subject  = $myinfo['subject'];
        $viptype  = $myinfo['viptype'];
        $classid  = $myinfo['classname'];

        $result['user_name']  = $myinfo['user_name'];
        $result['pic']  = $myinfo['pic'];
        $result['sex']  = $myinfo['sex'];
        $result['schoool_name']  =$myinfoall['school_name'];

        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }
        $nums = 20;
        $policynums = 60;

        $EduGkClassdata = Caches::EduGkClass();
        if($subject){
            $result['subject']= empty($EduGkClassdata[$subject])?'':preg_replace("/#/","-",$EduGkClassdata[$subject]);
        }else{
            $result['subject'] ='无';
        }

        $result['subjectno'] = 0;
        $data =Navigationa::get_combination($uid,$schoolid,$subject,$viptype,$classid);

        if(!empty($data)){
            $result['nagigt']=1;
            foreach($data['arrno'] as $value){
                if($value['tpid'] == $subject){
                    $result['subjectno'] = 1;
                }
            }
        }else{
            $result['nagigt']=0;
        }
        $cids= array(3,7,10,30,39,46);
        $data = Shcool::collection($uid,$schoolid,$cids);
        $numplociy = 0;
        $num = 0;
        foreach ($data as $key => $value) {
            $numplociy = $numplociy+$value['tages'];
            $num+=1;
        }

        $result['subjectnomsg']='';
        if($result['subjectno'] ==1){
            $result['subjectnomsg']="根据你的学业决策不推荐选择此选科组合";
        }
        $result['num'] =$num;

        if($num >=$nums){
            $result['numprotesmg'] = "收藏专业过多建议调整";
            $result['numprote'] = 1;
        }else{
            $result['numprotesmg'] = "";
            $result['numprote'] = 0;
        }
        $result['policynum'] = is_nan(@round(($numplociy / $num),2))?0:@round(($numplociy / $num),2);


        if($result['policynum'] <= $policynums){
            $result['policyprotesmg'] = "你的收藏专业跟学业决策参数的匹配度小于60%，建议检查并调整";
            $result['policyprote'] = 1;
        }else{
            $result['policyprotesmg'] = "";
            $result['policyprote'] = 0;
        }

        return $this->echo_json(0,'',$result);

    }




    /**
     *  //推荐专业
     */
    public function whereabouts(){

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid  = $myinfo['schoolname'];
        $viptype  = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
//        if($status!=1  &&  $status !=2 ){
//            return   $this->echo_json(22001);
//        }


        $data1= Recommend::recomm_hangye($uid);
        $data2= Recommend::recomm_lilun($uid);
        $limit = $this->search_page();
        $data3= Recommend::recomm_school($uid,$schoolid,$limit);

        $result['hy']=$data1;
        $result['xk']=$data2;
        $result['gx']=$data3;
        return $this->echo_json(0,'',$result);

    }


    /**
     *职拓报告
     */
    public function ztPresentation()
    {
        $uid = $this->uid;
        $userinfo = Caches::MyuserInfoFiled($uid);
        $schoolid  = $userinfo['schoolname'];
        $result['username'] = $userinfo['user_name'];
        $result['pic'] = $userinfo['pic'];
        $result['titlename'] = Shcool::whereid($schoolid)->value('titlename');  ;//($schoolid,'titlename');

        $result['sex'] = $userinfo['pic'] =='女'?2 :1 ;
        $result['jifen'] =Evaluate::rep_integral($uid);
        $result['competence'] =Evaluate::competency($uid);
        $result['recom'] =Evaluate::rep_recom($uid);
        $result['atten'] =Evaluate::rep_atten($uid);;
        // $result['general'] =0;
        // $result['indus'] =0;

        $data =ActivityModel::get_based_fen($uid);

        $result['accom'] = empty($data[0])?0:$data[0];
        $result['study'] = empty($data[2])?0:$data[2];
        $result['leader'] =empty($data[3])?0:$data[3];
        $result['jourl'] =5;
        $result['defen'] =Evaluate::eval_defens($uid);

        return $this->echo_json(0,'',$result);
    }


    // 教练评价
    public function evaluationRreport(){
        $uid = $this->uid;
        // 专业态度  书面表达 辩证思维  专业知识  观点新颖
        $sql = "SELECT FORMAT(avg(target1),1) as t1 , FORMAT(avg(target2),1)  as t2,  FORMAT(avg(target3),1) as t3 ,FORMAT(avg(target4),1)as t4,FORMAT(avg(target5),1) as t5 FROM kw_app_user_evaluate where userid ={$uid} ";
        $data1s = AppUserEvaluate::query($sql);
        $count = AppUserEvaluate::where('userid',$uid)->count('id');

        $titlearray = ['专业态度','书面表达','辩证思维','专业知识','观点新颖'];
        $list1['t1']['titlename'] = '专业态度';
        $list1['t2']['titlename'] = '书面表达';
        $list1['t3']['titlename'] = '辩证思维';
        $list1['t4']['titlename'] = '专业知识';
        $list1['t5']['titlename'] = '观点新颖';

        $list1['t1']['num'] = $count;
        $list1['t2']['num'] = $count;
        $list1['t3']['num'] = $count;
        $list1['t4']['num'] = $count;
        $list1['t5']['num'] = $count;

        $list1['t1']['avg'] = $data1s[0]['t1'];
        $list1['t2']['avg'] = $data1s[0]['t2'];
        $list1['t3']['avg'] = $data1s[0]['t3'];
        $list1['t4']['avg'] = $data1s[0]['t4'];
        $list1['t5']['avg'] = $data1s[0]['t5'];

        $sql2 = "SELECT  k2.titlename, COUNT(k1.id) as num , FORMAT (avg(jifen),1) as avg  FROM  `kw_app_user_site_base` k1  LEFT JOIN kw_base_index  k2 on k1.baseid=k2.id  WHERE userid = 9566 and titlename !='' GROUP BY baseid";
        $list2 = Db::query($sql2);
        foreach ($list2 as $key =>$value){
            if($value['titlename'] == '专业态度'){
                $list2[$key]['avg'] =  round( (($value['avg'] * $value['num'])+($data1s[0]['t1'] * $count ))/($value['num'] + $count ),2);
                $list2[$key]['num'] = $value['num']+$count;
                unset($list1['t1']);
            }elseif($value['titlename'] == '书面表达'){
                $list2[$key]['avg'] =  round( (($value['avg'] * $value['num'])+($data1s[0]['t2'] * $count ))/($value['num'] + $count ),2);
                $list2[$key]['num'] = $value['num']+$count;
                unset($list1['t2']);
            }elseif($value['titlename'] == '辩证思维'){
                $list2[$key]['avg'] =  round( (($value['avg'] * $value['num'])+($data1s[0]['t3'] * $count ))/($value['num'] + $count ),2);
                $list2[$key]['num'] = $value['num']+$count;
                unset($list1['t3']);
            }elseif($value['titlename'] == '专业知识'){
                $list2[$key]['avg'] =  round( (($value['avg'] * $value['num'])+($data1s[0]['t4'] * $count ))/($value['num'] + $count ),2);
                $list2[$key]['num'] = $value['num']+$count;
                unset($list1['t4']);
            }elseif($value['titlename'] == '观点新颖'){
                $list2[$key]['avg'] =  round( (($value['avg'] * $value['num'])+($data1s[0]['t5'] * $count ))/($value['num'] + $count ),2);
                $list2[$key]['num'] = $value['num']+$count;
                unset($list1['t5']);
            }

        }



        $result = array_merge($list1,$list2);
        return $this->echo_json(0,'',$result);
        exit;
    }

}
