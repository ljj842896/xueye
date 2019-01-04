<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\Libs;
use app\common\model\Cares;
use app\common\model\Navigationa;
use app\common\model\Shcool;
use app\index\controller\Action;
use think\db\exception\BindParamException;
use think\exception\DbException;
use think\exception\PDOException;
use think\facade\Cache;
use think\Controller;
use think\Db;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class School extends Action
{
    /**
     * 大学检索
     * @return mixed
     */
    public function search_school()
    {
        $where = ' ';
        if(!empty($_GET['titlename'])){
            $titlename =  preg_replace('# #','',  trim($_GET['titlename']));
            $where .= " and titlename like  '%".$titlename."%' ";
        }
        if(!empty($_GET['scid'])){
            $where .=  " and scid in (".$_GET['scid'].") ";
        }
        if(!empty($_GET['sdid'])){
            $where .= " and sdid in ( ".$_GET['sdid']." ) ";
        }
        if(!empty($_GET['lx'])){
            $where .=  " and lx = '".$_GET['lx']."' ";
        }
        if(!empty($_GET['schooltagid'])){
            $tpids = '';
            foreach($_GET['schooltagid'] as $a=>$b){
                $tpids .= " FIND_IN_SET(".$b.",tagid) or";
            }
            $where .=  " and (".substr($tpids,0,-2).") ";

        }

        $limit = $this->search_page();
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];

        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        if($myuserStatus['status'] == 0 ){
            //$this->echo_json('您不是个人会员',220001);
            $limit  = ' LIMIT 3';
        }

        $ShcoolModel = new Shcool();
        $data = $ShcoolModel->search_school_where($uid,$where,$limit);

        return $this->echo_json(0,'',$data);
    }

    /**
     * // 学校详情
     */
    public function  get_school_details(){
        // 学校ID
        if(empty($this->request->param('schoolid')) ){
            return $this->echo_json(70001);
        }
        $uid = $this->uid;
        $schoolid = $this->request->param('schoolid');
        $result =   Cache::get('SchoolInfo'.$schoolid);
        $school_scdata = Caches::SchoolUserScId($uid);
        if($result){
            $result['has'] = in_array($schoolid,$school_scdata)?1:0;
            return $this->echo_json(0,'',$result);
        }

        $schoolinfo = Shcool::whereid($schoolid)->find();
        $areadata=Caches::AreaAll();
        if($schoolinfo){
            $result['titlename'] = $schoolinfo['titlename'];
            $result['wtiss'] = $schoolinfo['wtiss'];
            $result['jxtime'] = $schoolinfo['jxtime'];
            $result['name'] = $schoolinfo['name'];
            $result['lx'] = $schoolinfo['lx'];
            $result['content'] = $schoolinfo['content'];
            $result['scid'] = $schoolinfo['scid'];
            $result['sdid'] = $schoolinfo['sdid'];
            $result['sd_name'] = empty($areadata[$schoolinfo['sdid']])?'':$areadata[$schoolinfo['sdid']];
            $result['sc_name'] = empty($areadata[$schoolinfo['scid']])?'':$areadata[$schoolinfo['scid']];
            $result['tagid'] =  Libs::checkleiArr($schoolinfo['tagid'],'class_class','id','tpname',','); $schoolinfo['tagid'];
            $result['pic'] = empty($schoolinfo['pic'])?"/images/zanwu.png":$schoolinfo['pic'];
            $result['pics'] = Shcool::get_school_pic($schoolid);
            Cache::set('SchoolInfo'.$schoolid,$result,CACHE_DATA_TIME);
            $result['has'] = in_array($schoolid,$school_scdata)?1:0;
        }else{
            $result = '';
        }

        return $this->echo_json(0,'',$result);
    }

    //院系结构
    public function sel_structure(){
        if(empty($this->request->param('schoolid')) ){
            return $this->echo_json(70001);
        }
        $schoolid = $this->request->param('schoolid');
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolname = $myinfo['schoolname'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        if($myuserStatus['status'] == '0' ){
            return $this->echo_json(22001);
        }

        $result =   Cache::get('SchoolStructure'.$schoolid);
        if($result){
            return $this->echo_json(0,'',$result);
        }
        $result=Shcool::school_structure($schoolid);
        Cache::set('SchoolStructure'.$schoolid,$result,CACHE_DATA_TIME);
        return $this->echo_json(0,'',$result);
    }

    /**
     *专业发展报告
     */
    public function get_major_report(){
        $majorid ='';
        $rankid ='';
        if(!empty($_GET['majorid'])){
            $majorid = intval($this->request->param('majorid'));
        }elseif(!empty($_GET['rankid'])){
            $rankid = intval($this->request->param('rankid'));
        }else{
            return $this->echo_json(80002);
        }

        $result=Shcool::major_report($majorid,$rankid);
        if($result){
            return $this->echo_json(0,'',$result);
        }else{
            return $this->echo_json(80006);
        }

    }
    /**
     *z专业详情
     */
    public function get_major_details(){
        if(empty($_GET['majorid'])){
            return $this->echo_json(80002);
        }
        $majorid = intval($this->request->param('majorid'));
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];

        $data =   Cache::get('MajorDetails'.$majorid);
        if(!$data){
            $data = Shcool::majorDetails($majorid,$status);
            Cache::set('MajorDetails'.$majorid,$data,CACHE_DATA_TIME);
        }

        if(empty($data)){
            $result = '';
        }else{
            $result['has']= in_array($majorid,Caches::EduUserScId($uid))?1:0;
            $result['titlename'] = $data['titlename'];
            $result['da'] = $data['da'];
            $result['lilun'] = $data['lilun'];
            $result['type'] = $data['type'];
            $result['get_content'] = $data['get_content'];
            $result['alclass'] = $data['alclass'];
            $result['profession'] = $data['profession'];
            $result['content'] = $data['content'];
            $result['closer'] = $data['closer'];
            $result['cidname'] = $data['cidname'];
            $result['didname'] = $data['didname'];
        }
        return   $this->echo_json(0,'',$result);
    }

    /**
     *开始院校数   相关专业 地区 开设数
     */
    public function professional_colleges(){
        if(empty($_GET['majorid'])){
            return $this->echo_json(80002);
        }

        if(!empty($_GET['tj'])){
            $tagid = trim($_GET['tj']);
        }else{
            $tagid ='';
        }

        $majorid = intval($_GET['majorid']);
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $schoolid = $myinfo['schoolname'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        if($status !=1 &&  $status !=2 ){
            return $this->echo_json(22001);
        }

        if(!empty($_GET['selection'])){
            $where =  $this->set_where();
        }else{
            $where = '';
        }
        if(!empty($_GET['scid'])){
            $scid = " and c.scid = ".intval($_GET['scid']);
        }else{
            $scid = '';
        }

        $lilun='';
        if(!empty($_GET['lilun']) ){
            $lilun = trim($_GET['lilun']);
        }

        $limit =  $this->search_page();

        $keynum = md5($majorid.$schoolid.$tagid);

        $num = Cache::get('ProfessionalColleges:'.$keynum.':Num');

        if(empty($lilun)){
            $schoolid=0;
        }

        if(!$num){
            $num = Shcool::get_professional_colleges($majorid,$schoolid,$tagid,'',$scid,$where,$lilun);
            Cache::set('ProfessionalColleges:'.$keynum.':Num',$num,CACHE_DATA_TIME);
        }

        $key = md5($majorid.$schoolid.$tagid.$limit);
        $data = Cache::get('ProfessionalColleges:'.$key.':page');
        if(!$data){
            $data = Shcool::get_professional_colleges($majorid,$schoolid,$tagid,$limit,$scid,$where,$lilun);
            Cache::set('ProfessionalColleges:'.$key.':page',$data,CACHE_DATA_TIME);
        }

        $result['num'] = $num;
        $result['list'] = $data;
        return $this->echo_json(0,'',$result);

    }

    //3+3 选科 借口
    public function selection(){
        $where = $this->set_where();
        if(!empty($_GET['titlename'])){
            $where .= " and c.titlename like '%".trim($_GET['titlename'])."%'";
        }


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


        $keynum  = md5($schoolid.$where);


        $num =  Cache::get('Shcool:elective:'.$keynum);

        if(!$num){
            $num = Shcool::elective($schoolid,$where);
            Cache::set('Shcool:elective:'.$keynum,$num,CACHE_DATA_TIME);
        }

        $rediskey  = md5($schoolid.$where.$limit);
        $data =  Cache::get('Shcool:elective:page:'.$rediskey);
        if(!$data){
            $data = Shcool::elective($schoolid,$where,$limit);
            Cache::set('Shcool:elective:page:'.$rediskey,$data,CACHE_DATA_TIME);
        }
        if(!$data){
            $data = '';
        }else{
            foreach ($data as $key =>$val){
                $data[$key]['has'] =  in_array($val['id'],Caches::EduUserScId($uid))?1:0;
            }
        }

        $result['num']= $num;
        $result['list']= $data;
        return  $this->echo_json(0,'',$result);

    }

    /**  //专业搜索
     * @return \think\response\Json
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function  search_major(){
        $where = '';
        //专业名
        if(!empty( $this->request->param('titlename') )){
            $titlename =  $this->request->param('titlename') ;
            $where .= " and i.titlename like '%".$titlename."%' ";
        }
        //就业方向
//        if(!empty($_REQUEST['content'])){
//            $content = $_REQUEST['content'];
//            $where .= " and i.content like '%".$content."%' ";
//        }

        // 人文科学	1
        // 理论科学	3
        // 社会科学	2
        // 工程技术科学	4
        if(!empty($_GET['lx'])){
            $lx =implode(',',$_GET['lx']) ;
            $where .= " and i.lx in ({$lx})";
        }
//        if(!empty($_REQUEST['did'])){
//            $did = $_REQUEST['did'];        if(!empty($_GET['titlename']['cid'])){
//            $where .= " and i.did = {$did} ";
//        }
        if(!empty($_GET['cid'])){
            $cid =trim( $_GET['cid'] );
            $where .= " and i.cid in ({$cid})  ";
        }
        //感兴趣的特定行业
        if(!empty($_GET['alclass'])){
            $where .=  "  and  i.alclass like '%".trim($_GET['alclass'])."%'  ";
        }
        //优势学科
        if(!empty(  $_GET['lilun'] )){
            $lilun = trim($_GET['lilun']) ;
            $where .= " and i.lilun like '%".$lilun."%' ";
        }

        //你感兴趣的职业
//        if(!empty($_GET['zhiye'])){
//            $zhiyeid=$this->schoolmodel->search_z($_GET['zhiye']);
//            $tpids = substr(implode(',',$zhiyeid),0,-1);
//            if($tpids>0){
//                $where .= " i.and  id in (".$tpids.")";
//        }
        if(!empty($this->request->param('type1'))){
            $type[] =  $_GET['type1'];  //专科
        }
        if(!empty($_GET['type2'])){
            $type[] =  $_GET['type2'];  //本科
        }
        if(!empty($_GET['type1']) || !empty($_GET['type2'])){
            $types = rtrim(implode(',',$type),',');
            $where .=" and  i.type in ({$types}) ";
        }
        $wheretag = 0;
        $tagidar =[];
        $tagid = '';
        if(!empty($_GET['tj'])  && empty(input('get.type1')) ){
            $tagidar = $_GET['tj'];
            $tagid = implode(',',$tagidar);
            $where .=" and ( ";
            foreach ( $tagidar as $key =>$v){
                $where .= " FIND_IN_SET({$v},zy.tagid) or";
            }
            $where = rtrim($where,'or');
            $where .=") ";
            $wheretag = 1;
        }

        $limit =  $this->search_page();

        if(!$where){
            return $this->echo_json(80003);
        }

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        if($status!=1  &&  $status !=2 ){
            $limit = ' LIMIT 3 ';
        }
        // 数据缓存

        $rediskey=md5($schoolid.$where.$tagid.$wheretag.$limit);
        $data= Cache::get('Shcool:'.$rediskey.':SearchMajor:page');
        if(!$data){
            $data = Shcool::search_major($schoolid,$where,$tagid,$wheretag,$limit);
            Cache::set('Shcool:'.$rediskey.':SearchMajor:page',$data,CACHE_DATA_TIME);
        }

        $keynum=md5($schoolid.$where.$tagid.$wheretag);
        $num= Cache::get('Shcool:'.$keynum.':SearchMajor:num');
        if(!$num){
            $num = Shcool::search_major($schoolid,$where,$tagid,$wheretag);
            Cache::set('Shcool:'.$keynum.':SearchMajor:num',$num,CACHE_DATA_TIME);
        }
        $result['num']=$num;
        if(!$data){
            $result['list'] = '';

        }else{
            $result['tagid'] = $tagid;
            foreach($data as $key =>$value){
                $result['list'][$key]['titlename'] = $value['titlename'];
                $result['list'][$key]['lilun'] = $value['lilun'];
                $result['list'][$key]['alclass'] = $value['alclass'];
                $result['list'][$key]['zid'] = $value['id'];
                $result['list'][$key]['type'] = $value['type'];
                $result['list'][$key]['colleges'] = $value['colleges'];
                $result['list'][$key]['has']= in_array($value['id'], Caches::EduUserScId($uid))?1:0;
            }
        }

        return $this->echo_json(0,'',$result);
    }

    //高考 3+3
    private function set_where(){
        $selection=array("1"=>"历史","2"=>"物理","3"=>"化学","4"=>"生物","5"=>"地理","6"=>"思想政治","7"=>"技术");
        $where = " and a.lilun !='' ";
        if(!empty($_GET['selection']) && $_GET['selection'][0] !='不限'){
            $where .= "  and a.lilun not like '%不限%'  "; //默认直s
            $search = $_GET['selection'];
            $selection = array_diff($selection,$search);
        }
        foreach ($selection as $key=>$value) {
            $where .= " and  a.lilun  not like '%".$value."%' "; // 本专业默认值
        }
        if(!empty($_GET['scid'])){
            $scid  = trim($_GET['scid']);
            $where .= " and c.cid in ({$scid}) ";
        }

        //感兴趣的特定行业
        if(!empty($_GET['alclass'])){
            $where .=  "  and  c.alclass like '%".trim($_GET['alclass'])."%'  ";
        }

        //优势学科
        if(!empty(  $this->request->param('lilun') )){
            $lilun = trim($this->request->param('lilun')) ;
            $where .= " and c.lilun like '%".$lilun."%' ";
        }


        return $where;
    }

    /**
     *    //专业类
     */
    public function majorlei_ranklist(){
        $result= Cache::get('Majorlei:Ranklist');
        if(!$result){
            $result =Shcool::majorlei_ranklist();
            Cache::set('Majorlei:Ranklist',$result,CACHE_DATA_TIME);
        }
        return $this->echo_json(0,'',$result);
    }

    /**类排名
     * @param $zids
     * @param $schoolid
     * @return mixed
     */
    public function majorlei_rank(){
        if(empty($_GET['zids'])){
            return $this->echo_json(80004);
        }
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $zids = explode(',',$_GET['zids']);

        $key = md5($_GET['zids'].$schoolid);
        $result= Cache::get('Majorlei:Rank:Zids:'.$key);
        if(!$result){
            $result =Shcool::majorlei_rank($zids,$schoolid);
            Cache::set('Majorlei:Rank:Zids:'.$key,$result,CACHE_DATA_TIME);
        }
        return $this->echo_json(0,'',$result);
    }

    /**
     *    //专业类
     */
    public function major_rankitle(){
        if(empty($_GET['ranktitle'])){
            return $this->echo_json(80005);
        }
        $ranktitle = trim($_GET['ranktitle']);
        $key = md5($ranktitle);
        $data= Cache::get('MajorRankitle:'.$key);
        if(!$data){
            $data=Db::name('edu_info')->field('id,titlename')->whereLike('ranktitle',$ranktitle)->where('open',1)->select();
            Cache::set('MajorRankitle:'.$key,$data,CACHE_DATA_TIME);
        }
        return $this->echo_json(0,'',$data);

    }

    //专业收藏
    public function major_collection(){

        if(empty($_GET['cid'])){
            $cids= array(3,7,10,30,39,46);
        }else{
            $cids = explode(',',trim($_GET['cid']));
        }
        $uid =$this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        // 收藏专业数

        $has = Shcool::name('edu_wen_sr')->where('uid',$uid)->where('openok',1)->count();

        if(!$has){
            return $this->echo_json(90003);
        }

        $nums  = Shcool::name('user_edu_sc')->where('uid',$uid)->count();
        if($nums){
            $result = Shcool::collection($uid,$schoolid,$cids);
        }else{
            $result = '';
        }

        $data['nums'] = $nums;
        $data['eids'] = implode('_',Caches::EduUserScId($uid)) ;
        $data['list'] = $result;
        $data['has'] = $has;
        return $this->echo_json(0,'',$data);
    }


    /**
     *   //3+3  选科策略
     */
    public  function aggregate()
    {
        if(empty($_GET['majorid'])){
           return $this->echo_json(80002);
        }
        $majorid = intval($_GET['majorid']);
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $data = Shcool::aggre_gate($schoolid,$majorid);
        return $this->echo_json(0,'',$data);
    }

    /**
     * 专业匹配度
     */
    public function major_policy(){
        if(empty($_GET['eduscid'])){
            return $this->echo_json(80002 );
        }
        $eduscid = intval($_GET['eduscid']);
        $uid = $this->uid;
        $data = Navigationa::userPolicy( $uid, $eduscid );
        return $this->echo_json(0,'',$data);
    }

    /** 收藏学校  删除收藏
     * @return \think\response\Json
     */
    public function collection_add(){
        // 收藏的学不能为空
        if(empty($this->request->param('schoolid')) ){
           return $this->echo_json(70001);
        }
        $uid  = $this->uid;
        $schoolid=intval(trim($this->request->param('schoolid')));

        $result = Shcool::add_school_sc($uid,$schoolid);
        if($result){
            return  $this->echo_json(0);
        }else{
            return $this->echo_json(70004);
        }
    }

    /**
     * 收藏专业
     * @return \think\response\Json
     */
    public function collection_major_add(){

        if(empty($this->request->param('majorid'))){
            return $this->echo_json(70002);
        }
        $majorid= intval($this->request->param('majorid'));
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        if($status!=1  &&  $status !=2 ){
            return   $this->echo_json(90001);
        }
        $result = Shcool::add_user_edu_sc($uid,$majorid);
        if($result == 'duo'){
            return $this->echo_json(90002);
        }elseif($result){
           return $this->echo_json(0);
        }else{
            return $this->echo_json(70004);
        }
    }


    /**
     *收藏高校 列表
     */
    public function schoolid_collection(){
        $uid = $this->uid;
        $result=Shcool::get_school_sc($uid);
        return $this->echo_json(0,'',$result);
    }

}
