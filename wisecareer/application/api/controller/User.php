<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\HttpApi;
use app\common\model\Area;
use app\common\model\Class_class;
use app\common\model\Myuser;
use app\common\model\MyuserZtPay;
use app\common\model\Recharge;
use app\common\model\School_set;
use app\common\model\Shcool;
use app\common\model\Shcool_class_zx;
use app\common\model\StCeping;
use app\common\model\StCepingMul;
use app\common\model\User_write;
use app\common\model\UserMessage;
use app\common\model\UserTy;
use app\common\model\Fxuser;
use app\index\controller\Action;

use app\index\controller\Basics;
use think\Controller;
use think\Db;
use think\facade\Cache;
use Config;
use think\Request;
use Validate;

/**
 * Class Index
 * @package app\api\controller
 */
class User extends Action
{
    protected $batchValidate = true;

    /**
     * 学业 站
     * 个人中新首页
     */
    public function xygetuser(){
        $uid = $this->uid;
        //  缓存中获取个人心思
        $myinfo = Caches::MyuserInfo($uid);
        //个人资料
        $myuserStatus = Caches::MyuserXyStatus($uid);
        $AreaPhonesdata = Caches::AreaPhonesAll();

        $advisory = empty($AreaPhonesdata[$myinfo['scid']])?'4000-740-520':$AreaPhonesdata[$myinfo['scid']];
		 $scoreshas =  Class_class::name('user_scores_avg')->where('uid',$uid)->find();
         $scorehas= !empty($scoreshas)?1:0;
		 
        if(empty($myinfo['pic'])){
            if($myinfo['sex'] =='男'){
                $pic = "userpic/man_s.png";
            }else{
                $pic = "userpic/woman_s.png";
            }
        }else{
            $pic = $myinfo['pic'];
        }

        $id = 5;
        $type = 110;
        $id = Db::name('myuser_coupon')->where('type',$type)->value('id');
        $hasid = Db::name('myuser_coupon_rule')->where('couponid',$id)->where('uid',$uid)->value('id');
        $hasid = $hasid==null?0:1;
		$userretailid = Fxuser::userretailid($uid);
        $result = [
            'pic'=>$pic,
            'id'=>$id,
            'type'=>$type,
            'hasid'=>$hasid,
            'sex'=>$myinfo['sex'],
            'user_name'=>$myinfo['user_name'],
            'school_name'=>$myinfo['school_name'],
            'status'=>$myuserStatus['status'],
            'advisory'=>$advisory,
            'viptype'=>$myinfo['viptype'],
			 'classname'=>$myinfo['classname'],
			'scorehas'=>$scorehas,
			'userretailid'=>empty( $userretailid )? '0': 1, 
            'xy_e_time'=>$myuserStatus['xy_e_time']?date('Y-m-d ',$myuserStatus['xy_e_time']):'',
        ];


//4000-740-520
        $money = Myuser::userBalance($uid);
        $result['money'] = $money ==null ?0:$money; 
        $ots = StCeping::where('uid',$uid)->value('ots');
        $ots_mul = StCepingMul::where('uid',$uid)->value('ots');
        $result['ots']  = $ots ==1?1:0;
        $result['ots_mul']  = $ots_mul ==1?1:0;
        $result['userinfo'] = userinfo_is($myinfo);
        return $this->echo_json(0,'',$result);

    }

    /**
     * 学业 站
     * 个人中新首页
     */
    public function ztgetuser(){

        $uid = $this->uid;
        //  缓存中获取个人心思
        $myinfo = Caches::MyuserInfo($uid);
        //个人资料
        $myuserStatus = Caches::MyuserZtStatus($uid);
        $AreaPhonesdata = Caches::AreaPhonesAll();
        $advisory = empty($AreaPhonesdata[$myinfo['scid']])?'4000-740-520':$AreaPhonesdata[$myinfo['scid']];
        if(empty($myinfo['pic'])){
            if($myinfo['sex'] =='男'){
                $pic = "userpic/man_s.png";
            }else{
                $pic = "userpic/woman_s.png";
            }
        }else{
            $pic = $myinfo['pic'];
        }

        if($myinfo['viptype']==1){
            $hasmeal_time  =$myuserStatus['zt_e_time']?date('Y-m-d ',$myuserStatus['zt_e_time']):'' ;
            $has_en = 0;
        }else{
            $hasmeal  = MyuserZtPay::userSetMealHas($uid);
            $hasmeal_time  = $hasmeal?date('Y-m-d',$hasmeal['endtime']):0;
            $has_en = $hasmeal['en_case'] ;
        }

        $has_eval = Myuser::name('myuser_zt_eval_pay')->where('uid',$uid)->where('state',1)->find() ==null?0:1 ;
        $eval_num_surplus = Myuser::name('myuser_account')->where('uid',$uid)->value('eval_num');
		$userretailid = Fxuser::userretailid($uid); 
        $result = [
            'pic'=>$pic,
            'sex'=>$myinfo['sex'],
            'user_name'=>$myinfo['user_name'],
            'school_name'=>$myinfo['school_name'],
            'advisory'=>$advisory,
            'viptype'=>$myinfo['viptype'],
            'hasmeal_time'=>$hasmeal_time,
            'has_en'=>$has_en,
            'has_eval'=>$has_eval,
            'has_eval_num'=>$eval_num_surplus,
            'status'=>$myuserStatus['status'],
			'userretailid'=>empty( $userretailid )? '0': 1, 
            'zt_e_time'=>empty($myuserStatus['zt_e_time'])?'': date('Y-m-d ',$myuserStatus['zt_e_time'])
        ];

        // ,

        //4000-740-520
        $money = Myuser::userBalance($uid);
        $result['money'] = $money ==null ?0:$money;
        $ots = StCeping::where('uid',$uid)->value('ots');
        $result['ots']  = $ots ==1?1:0;
        $result['userinfo'] = userinfo_is($myinfo);
        $result['isretail'] = 0;
        return $this->echo_json(0,'',$result);

    }

    /**
     * 个人资料 信息
     * @return mixed
     */
    public function userinfo()
    {
        $uid = $this->uid; 
      		  // 缓存中获取个人心思
        $myinfo = Caches::MyuserInfo($uid);
        if(empty($myinfo['pic'])){
            if($myinfo['sex'] =='男'){
                $pic = "userpic/man_s.png";
            }else{
                $pic = "userpic/woman_s.png";
            }
        }else{
            $pic = $myinfo['pic'];
        }
       		 $myinfo = Caches::MyuserInfo($uid);
		     $userinfo = userinfo_is($myinfo);
        $result = [
            'pic'=>$pic,
            'sex'=>$myinfo['sex'],
            'user_name'=>$myinfo['user_name'],
            'nickname'=>$myinfo['nickname'],
            'scid'=>$myinfo['scid'],
            'sdid'=>$myinfo['sdid'],
            'ol_type'=>$myinfo['ol_type'],
            'closslx'=>$myinfo['closslx'],
            'schoolname'=>$myinfo['schoolname'],
            'classname'=>$myinfo['classname'],
            'dianhua'=>$myinfo['dianhua'],
            'email'=>$myinfo['email'],
            'viptype'=>$myinfo['viptype'],
			'userinfo'=>$userinfo,
            'sc_name'=>$myinfo['sc_name'],
            'sd_name'=>$myinfo['sd_name'],
            'school_name'=>$myinfo['school_name'],
            'classname_name'=>$myinfo['classname_name'],
            'classroom_name'=>$myinfo['classroom_name'],
            'birthday_data'=>$myinfo['birthday']?date('Y-m-d ',$myinfo['birthday']):'',
        ];
 
        $nianjis = [];
        $banjis = [];
        $ajax = 1;
        if(!empty($myinfo['schoolname']) && $myinfo['schoolname'] != 0){
            $nianjis =  Db::name('school_classroom')
                    ->alias('a')
                    ->join('shcool_class_zx zx','a.tpname = zx.class_id')
                    ->where('a.classid',$myinfo['schoolname'])->where('a.tporder',0)->where('zx.class_type',1)
                    ->field('a.tpid,zx.class_id,zx.class_name')
                    ->select();
            if(!$nianjis){
                $ajax=0;
                $nianjis =  Db::name('shcool_class_zx')
                    ->where('class_type',1)
                    ->field('class_id,class_name')->order('class_id','asc')
                    ->select();
                $banjis =  Db::name('shcool_class_zx')
                    ->where('class_type',2)
                    ->field('class_id,class_name')->order('class_id','asc')
                    ->select();
            }

        }

        $result['nianji_banji']['nianjis'] = $nianjis;
        $result['nianji_banji']['banjis'] = $banjis;
        $result['nianji_banji']['ajax'] = $ajax;
        return $this->echo_json(0,'',$result);
    }

    /**
     *
    `*  修改个人信息
     * @return object
     */
    public function midfyUserinfo (Request $request)
    {
        //用户ID
        $update_myuserdate=[];

        $uid =$this->uid;

        if(!empty($request->param('subject'))){
            $update_myuserdate['subject'] = intval($request->param('subject'));
        }
        if(!empty($request->param('user_name'))){
            $update_myuserdate['user_name'] = trim($request->param('user_name'));
        }
        if(!empty($request->param('nickname'))){
            $update_myuserdate['nickname'] = trim($request->param('nickname'));
        }
        if(!empty($request->param('sex'))){
            $update_myuserdate['sex'] = trim($request->param('sex'));
        }
        if(!empty($request->param('email'))){
            $update_myuserdate['email'] = trim($request->param('email'));
        }
        if(!empty($request->param('classname'))){
            $update_myuserdate['classname'] = intval($request->param('classname'));
        }
        if(!empty($request->param('classroom'))){
            $update_myuserdate['classroom'] = intval($request->param('classroom'));
        }
        if(!empty($request->param('ol_type'))){
            if(intval($request->param('ol_type'))==1){
                $update_myuserdate['schoolname'] = 0;
                $update_myuserdate['scid'] = 0;
                $update_myuserdate['sdid'] = 0;
                $update_myuserdate['classname'] = 0;
                $update_myuserdate['classroom'] = 0;
                $update_myuserdate['closslx'] = 0;
                $update_myuserdate['viptype'] = 0;
                //Db::name('user_required')->where('uid',$uid)->data(['userinfo'=>0])->update();
            }
            $update_myuserdate['ol_type'] = intval($request->param('ol_type'));
        }
        if(!empty($request->param('birthday'))){
            $birthday = strtotime(trim($request->param('birthday')));
            $date = strtotime('-10 year', time());
            if($date< $birthday){
                return $this->echo_json(50001 );
            }
            $update_myuserdate['birthday'] = $birthday;
        }

        if(!empty($request->param('schoolname'))){
            try {
                $schooldata = Shcool::scope( 'Titlename', trim($request->param( 'schoolname' )) )->find();
            }catch (DbException $e) {
                return $this->echo_json(30001 );
            }
            if(!$schooldata){
                return $this->echo_json(40007 );
            }

            $update_myuserdate['scid']  =  $schooldata->scid;
            $update_myuserdate['sdid']  =   $schooldata->sdid;
            $update_myuserdate['closslx']  =   $schooldata->lx;
            $update_myuserdate['schoolname'] = $schooldata->id;
            $update_myuserdate['classname']  = 0;
            $update_myuserdate['classroom']  = 0;
        }

        if(!empty($update_myuserdate)){
            $this->renewSession($uid,$update_myuserdate);
            //          校内外信息同步
            $this->http_post_data();
            if(!empty($schooldata->cooperation)){
                return $this->echo_json(0,'',1);
            }else{
                $myinfo = Caches::MyuserInfoFiled($uid);
                $redata['userinfo'] = userinfo_is($myinfo);
                return $this->echo_json( 0,'',$redata);
            }
        }
        return $this->echo_json( 50003);

    }
	
	


    /**
     *
    `*  修改个人选科
     * @return object
     */
    public function midfyUsersubject (Request $request)
    {
        //用户ID
        $update_myuserdate=[];
        $uid =$this->uid;
        if(!empty($request->param('subject'))){
            $update_myuserdate['subject'] = intval($request->param('subject'));
        }
        if(!empty($update_myuserdate)){
            $update_myuserdate['subject_time']= time();
            $this->renewSession($uid,$update_myuserdate);
            UserMessage::send_subject_message($uid);

            return $this->echo_json( 0);
        }
        return $this->echo_json( 50003);
    }


    /**
     * 校内外信息同步
     * @return bool
     */
    public  function http_post_data()
    {
        $uid = $this->uid;
        $myuserinfo =  Caches::MyuserInfo($uid);
        if(empty($myuserinfo['memberId']) || $myuserinfo['memberId'] !=17){
            return false;
        }
        $http_apiModel = new HttpApi();
        $uid = $myuserinfo['memberuid'];
        $array['name'] = $myuserinfo['user_name'];
        $array['gender'] = $myuserinfo['sex']=='男'?1:0;
        $array['area'] = $myuserinfo['sc_name'] 	;
        $array['school'] =  $myuserinfo['school_name'] ;
        $array['class'] =$myuserinfo['classname_name'] .$myuserinfo['classroom_name'] ;
        $http_apiModel->xnw_http_modify_user($uid,$array);
    }


    /**
     * 验证学校 验证码是否正确
     * @param bool $batchValidate
     */
    public function shcoolVerification (Request $request)
    {
        $uid = $this->uid;
        $myuserinfo = Caches::MyuserInfo($uid);
        if($myuserinfo['viptype']==1){
            return $this->echo_json(50005);
        }
        $schoolid = $myuserinfo['schoolname'];
        $ver_code = trim($request->param('ver_code'));
        $result = School_set::where('classid',$schoolid)->where('verify',$ver_code)->find();
        if(empty($result)){
            return $this->echo_json(40008);
        }
        $update_myuserdate['viptype']=1;
        Caches::MyuserXyStatus($uid,1,1);
        $this->renewSession($uid,$update_myuserdate);
        // 合作机构到期时间
        $datetime = Shcool::whereid($result->classid)->value('xy_e_time');
        if($datetime < time()){
            return   $this->echo_json(50004,'你所在学校的使用权限已到期！为不影响正常使用，请到个人中心升级为VIP会员，享受更多VIP福利');
        }

        $datetnyr = $datetime?date('Y年m月d日',$datetime):'';
        return   $this->echo_json(50004,'你所在学校为你设置的使用有效期到'.$datetnyr.'。请先定制自己的“决策树”模型，然后了解推荐的目标专业，以及选科走班方案');

    }

    /**
     * 更新session 数据
     * @param $uid
     * @param $update_myuserdate
     */
    private function renewSession($uid, $update_myuserdate){
        $result = Myuser::whereid($uid)->update($update_myuserdate);
        Caches::MyuserInfoFiled($uid,1);
        Caches::MyuserInfo($uid,1);

    }


    /**
     * 修改密码
     */
    public function midfy_passwd(){

        if(empty($this->request->param('newpassword')) ){
            return $this->echo_json(40002);
        }

        if(empty($this->request->param('password')) ){
            return $this->echo_json(50017);
        }
        if(trim($this->request->param('newpassword')) != trim($this->request->param('newpasswords')) ){
            return $this->echo_json(50006);
        }

        if(trim($this->request->param('password')) == trim($this->request->param('newpassword')) ){
            return $this->echo_json(0);
        }

        $uid =$this->uid;
        $data= Myuser::whereid($uid)->where('password',md5(trim($this->request->param('password'))))->find();
        if(!$data){
            return $this->echo_json(50007);
        }

        $res  = Myuser::whereid($uid)->update(['password_od'=>trim($this->request->param('newpassword')),'password'=>md5(trim($this->request->param('newpassword')))]);
        if($res){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(50009);
        }

    }


    /**
     * 考生 地区
     */
    public function city(){

        $uid = $this->uid;
        $userinfo = Caches::MyuserInfoFiled($uid);
        if(!empty($this->request->param('scid') ) ) {
            $scid = intval($this->request->param('scid'));
            $result = Area::getArOrder($scid);
        }else{
            $result = Area::getArOrder();
        }
        foreach ($result as $key => $value) {
            if($value['ar_id'] == $userinfo['scid']){
                $result[$key]['has'] = 1;
            }else{
                $result[$key]['has'] = 0;
            }
        }
        return  $this->echo_json(0,'',$result);
    }

    /**
     * 会员状态
     */
    public function userstatus()
    {
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        $data['userinfo'] = 0;
        $data['planning'] = 0;
        $data['edu_sc'] = 0;
        // 个人资料 是否完善

        $data['userinfo'] = userinfo_is($myinfo);
        //  '决策树是否做0否，1是'
        if($myinfo['wensrid']==1){
            $data['planning'] = 1;
        }
		
        //  是否收藏专业
        if(count(Caches::EduUserScId($uid)) ==1){
            $data['edu_sc'] = 1;
        }
			$data['status'] = $status;
			$data['memberid'] = $myinfo['memberId']; //第三方机构ID
			$data['viptypeid'] = $myinfo['viptype']; //学员身份0代表个人会员，1代表机构会员
			$data['isretail'] = 0; 
			
		return $this->echo_json(0,'',$data);
    }

    /**
     * 会员状态
     */
    public function ztuserstatus()
    {
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserZtStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        $data['userinfo'] = 0;
        $data['planning'] = 0;
        $data['edu_sc'] = 0;
        // 个人资料 是否完善

        $data['userinfo'] = userinfo_is($myinfo);
        //  '决策树是否做0否，1是'
        if($myinfo['wensrid']==1){
            $data['planning'] = 1;
        }
        //  是否收藏专业
        if(count(Caches::EduUserScId($uid)) ==1){
            $data['edu_sc'] = 1;
        }

        $data['status'] = $status;

        return $this->echo_json(0,'',$data);
    }

    /**
     * @return mixed
     */
    public function wallet ()
    {
        $uid= $this->uid;
        $myinfo  = Caches::MyuserInfoFiled($uid);
        $result['dianhua'] =$myinfo['dianhua'];
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        $result['status'] =$status;
        $result['jsdate'] =empty($myuserStatus['xy_e_time'])?'': date('Y-m-d ',$myuserStatus['xy_e_time']);
        // 用户余额
        $money = Myuser::userBalance($uid);

        $result['money'] = $money ==null ?0:$money;
        return $this->echo_json(0,'',$result);
    }

    /**
     * 职拓账户余额
     * @return mixed
     */
    public function ztwallet ()
    {
        $uid= $this->uid;
        $myinfo  = Caches::MyuserInfoFiled($uid);
        $result['dianhua'] =$myinfo['dianhua'];
        $viptype = $myinfo['viptype'];
        $myuserStatus=Caches::MyuserZtStatus($uid,$viptype);
        $status = $myuserStatus['status'];

        $result['jsdate'] =empty($myuserStatus['zt_e_time'])?'': date('Y-m-d ',$myuserStatus['zt_e_time']);
        // 用户余额
        // 是否购买套餐
        $result['status']=$status;
        $result['days'] = empty($myuserStatus['zt_e_time'])?0:  intval(round(($myuserStatus['zt_e_time'] - time())/(24*3600))  );
        $casehas=  MyuserZtPay::userSetMealHas($uid);
        $result['casehas']=$casehas?  date('Y-m-d ', $casehas['endtime']):0 ;
        $money = Myuser::userBalance($uid);
        $result['money'] = $money ==null ?0:$money;
        return $this->echo_json(0,'',$result);
    }

    /**
     * 充值记录
     */

    public function recharge_list(){

        $uid = $this->uid;
        $limit=$this->search_page('array');
        $has =Recharge::name('myuser_pay')->where('uid',$uid)->find();
        if($has ==null){
            return $this->echo_json(30005);
        }
        $data = Recharge::list_recharge($uid,$limit);
        foreach ($data as $key => $value) {
            $data[$key]['datetime']=date('Y-m-d H:i:s',$value['datetime']);
            $data[$key]['state']=$value['state']?"充值成功":"取消充值";
            if($value['pay_type']==1){
                $data[$key]['pay_type']="充值卡充值";
            }elseif($value['pay_type']==2){
                $data[$key]['pay_type']="微信充值";
            }elseif($value['pay_type'] ==3){
                $data[$key]['pay_type']="代金券支付";
            }else{
                $data[$key]['pay_type']="其他支付";
            }
        }
       return $this->echo_json(0,'',$data);
    }

    /**
     * 消费记录
     */

    public function  consume_list(){
        $uid = $this->uid;
        $limit=$this->search_page('array');


        $has =Recharge::name('myuser_consume')->where('uid',$uid)->find();
        if($has ==null){
            return $this->echo_json(30005);
        }

        $data = Recharge::list_consume($uid,$limit);
        foreach ($data as $key => $value) {
            $data[$key]['datetime']=date('Y-m-d H:i:s',$value['datetime']);

            if($value['pay_type']==1){
                $data[$key]['pay_type']="学业E站包月续费";
            }elseif($value['pay_type']==2){
                $data[$key]['pay_type']="体验消费";
            }else{
                $data[$key]['pay_type']="预约教练";
            }
        }
        return $this->echo_json(0,'',$data);
    }


    /**
     * 高考考区
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public  function district(){
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $kaoquid = Shcool::get_district($schoolid);
        $data = Caches::AreaAll()[$kaoquid];

        return $this->echo_json(0,'',$data);

    }

    /**
     *成绩录入
     */
    public function scores_list()
    {
        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $classname = $myinfo['classname'];
        if($classname <10  || $classname > 12){
            return $this->echo_json(50013);
        }

        $dm = date('m',time());
        if((4<=$dm &&  $dm < 8  )|| ( 10 <=$dm ||  $dm < 2)   ){
            $m = 3;
        }elseif ((2<=$dm &&  $dm < 4 ) ||  ( 8 <=$dm &&  $dm < 10)  ){
            $m = 2;
        }else{
            $m = 1;
        }

        $limt = ( 1 + ($classname -10) * 4) + $m;
        $data = Class_class::where('cid',19)->order('tbid','asc')->order('id','desc')->limit($limt)->select();
        foreach ($data as $key => $value) {
            $has =  Class_class::name('user_scores')->where('uid',$uid)->where('classid',$value['id'])->find();
            $data[$key]['has'] = !empty($has)?1:0;
        }

        if(!empty($data)){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(30005);
        }

    }

    /**
     * 成绩录入
     * @return \think\response\Json
     */
    public function scores_lists()
    {
        $uid = $this->uid;
        if(empty($_GET['classid']) || !is_numeric($_GET['classid'])){
            return $this->echo_json(50014);
        }
        $classid = intval($_GET['classid']);
        $ClassClassdata =  Caches::ClassClass();
        $tpname = $ClassClassdata[$classid];
        $data = Db::name('user_scores')->where('uid',$uid)->where('classid',$classid)->field('scores5,scores2,scores4,scores8,scores9,scores10,scores15,scores17,scores27,number2,number4,number5,number8,number9,number10,number27,number17,number15')->find();

        $myinfo = Caches::MyuserInfoFiled($uid);
        $scid = $myinfo['scid'];
        $result = Db::name('subject_grade')->where('scid',$scid)->where('classid',$classid)->find();

        empty($data['number2']) &&  $data['number2'] = ($result['scores2']);
        empty($data['number4']) &&  $data['number4'] =  ($result['scores4']);
        empty($data['number5']) &&  $data['number5'] =( $result['scores5']) ;
        empty($data['number8']) &&  $data['number8'] =($result['scores8']) ;
        empty($data['number9']) &&  $data['number9'] = ($result['scores9']);
        empty($data['number10']) &&  $data['number10'] =($result['scores10'])  ;
        empty($data['number15']) &&  $data['number15'] =($result['scores15']) ;
        empty($data['number17']) &&  $data['number17'] =($result['scores17']) ;
        empty($data['number27']) &&  $data['number27'] = ($result['scores27']);

        $data['tpname'] = $tpname;
        if(!empty($data)){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(30005);
        }
    }

    /**
     *t添加 成绩
     */
    public function scores_add()
    {
        $uid = $this->uid;
        if(empty($_POST['classid']) || !is_numeric($_POST['classid']) ){
           return $this->echo_json(50014);
        }

        $array['classid'] = intval($_POST['classid']);
        unset($_POST['classid']);
        foreach ($_POST as $key =>$value){
            if($value <=0 ) $_POST[$key]=0;
        }
        $num = 0;
        floatval($_POST['scores2']) != 0 && $num++;
        floatval($_POST['scores4']) != 0 && $num++;
        floatval($_POST['scores5']) != 0 && $num++;
        floatval($_POST['scores8']) != 0 && $num++;
        floatval($_POST['scores9']) != 0 && $num++;
        floatval($_POST['scores10']) != 0 && $num++;
        floatval($_POST['scores15']) != 0 && $num++;
        floatval($_POST['scores17']) != 0 && $num++;
        floatval($_POST['scores27']) != 0 && $num++;
        if($num <4){
            return $this->echo_json(50019);
        }
        if($_POST['scores2']==0 &&  $_POST['scores4']==0 && $_POST['scores5']==0 && $_POST['scores8']==0 && $_POST['scores9']==0 && $_POST['scores10']==0 && $_POST['scores15']==0 && $_POST['scores17']==0 && $_POST['scores27']==0 ){
            return $this->echo_json(50018);
        }
        !isset($_POST['scores2']) || $array['scores2'] = floatval ($_POST['scores2']);
        !isset($_POST['scores4']) || $array['scores4'] = floatval ($_POST['scores4']);
        !isset($_POST['scores5']) || $array['scores5'] = floatval ($_POST['scores5']);
        !isset($_POST['scores8']) || $array['scores8'] = floatval ($_POST['scores8']);
        !isset($_POST['scores9']) || $array['scores9'] = floatval ($_POST['scores9']);
        !isset($_POST['scores10']) || $array['scores10'] = floatval ($_POST['scores10']);
        !isset($_POST['scores15']) || $array['scores15'] = floatval ($_POST['scores15']);
        !isset($_POST['scores17']) || $array['scores17'] = floatval ($_POST['scores17']);
        !isset($_POST['scores27']) || $array['scores27'] = floatval ($_POST['scores27']);

        !isset($_POST['number2']) || $array['number2'] = floatval ($_POST['number2']);
        !isset($_POST['number4']) || $array['number4'] = floatval ($_POST['number4']);
        !isset($_POST['number5']) || $array['number5'] = floatval ($_POST['number5']);
        !isset($_POST['number8']) || $array['number8'] = floatval  ($_POST['number8']);
        !isset($_POST['number9']) || $array['number9'] = floatval ($_POST['number9']);
        !isset($_POST['number10']) || $array['number10'] = floatval ($_POST['number10']);
        !isset($_POST['number15']) || $array['number15'] = floatval ($_POST['number15']);
        !isset($_POST['number17']) || $array['number17'] = floatval ($_POST['number17']);
        !isset($_POST['number27']) || $array['number27'] = floatval ($_POST['number27']);

        $array['perc2'] =  @round(($array['scores2'] /$array['number2'])*100,1);
        $array['perc4'] = @round(($array['scores4']/$array['number4'])*100,1);
        $array['perc5'] = @round(($array['scores5']/$array['number5'])*100,1);
        $array['perc8'] = @round(($array['scores8']/$array['number8'])*100,1);
        $array['perc9'] = @round(($array['scores9']/$array['number9'])*100,1);
        $array['perc10'] = @round(($array['scores10']/$array['number10'])*100,1);
        $array['perc15'] = @round(($array['scores15']/$array['number15'])*100,1);
        $array['perc17'] = @round(($array['scores17']/$array['number17'])*100,1);
        $array['perc27'] = @round(($array['scores27']/$array['number27'])*100,1);

        $array['uid']= $uid;
        $array['datetime']= time();

        $has =  Db::name('user_scores')->where('uid',$uid)->where('classid',intval($array['classid']))->find();
        if(!empty($has)){
            $data = Db::name('user_scores')->where('uid',$uid)->where('classid',intval($array['classid']))->data($array)->update();
        }else{
            $data =Db::name('user_scores')->data($array)->insert();
        }
        if($data){
            Myuser::avg_score($uid);
            return $this->echo_json(0);
        }else{
            return$this->echo_json(50015);
        }

    }


    /**用户优惠券
     * @return \think\response\Json
     */
    public function card(){
        $uid = $this->uid;
        $data = Db::name('myuser_coupon_rule')->where('uid',$uid)->select();


        if($data ==null){
            return $this->echo_json(0);
        }

        foreach ($data as $key =>$val){
            $data1 = Db::name('myuser_coupon')->where('id',$val['couponid'])->find();
            $res[$key]['title'] =$data1['titlename'] ;
            $res[$key]['rate'] =$data1['rate'] ;
            $res[$key]['use'] =$val['use'] ;
            $res[$key]['use_time'] =date('Y-m-d H:i:s',$val['use_time']) ;
            $res[$key]['kdate'] =date('Y-m-d',$data1['ks_time'] );
            $res[$key]['jdate'] =date('Y-m-d ',$data1['js_time'] );

        }

        return $this->echo_json(0,'',$res);

    }
}
