<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\Evaluate;
use app\common\model\Label;
use app\common\model\Lx_class;
use app\common\model\Myuser;
use app\common\model\MyuserZtPay;
use app\common\model\Shcool;
use app\common\model\UserMessage;
use app\common\Paydef;
use app\common\StringRsa;
use app\index\controller\Action;
use think\Controller;
use think\Db;
use think\facade\Cache;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Experience extends Action
{
    public function index(){




        $uid  = $this->uid;
        $mysetpay  = MyuserZtPay::userSetMealHas($uid);
        if($mysetpay==null){
            return  $this->echo_json(260009);
        }
        $data1  = Label::get_plan_label($uid);

        $data['pro'] =0;
        if($data1){
            $data['pro']  = Label::plan_pro($uid);
        }

        $data['xinnum']  = Evaluate::user_evaluate_xin_num($uid);
        $data['nums']  = Evaluate::user_evaluate_num($uid);
        $res = Evaluate::user_atten_recom($uid);
        if($data1){
            $result = array_merge($data,$data1,$res);
        }else{
            $result = array_merge($data,$res);
        }
        if(!$data1){
            return $this->echo_json(130001,'',$result);
        }

        return $this->echo_json(0,'',$result);
    }
    //标签
    public function lable(){

        $uid = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);

        $data1  = Label::get_plan_label($uid);
        $expire = 0;
        if($data1 ==false || $data1['expire']==1){
            $expire = 1;
        }

        if($myinfo['viptype']==1){
            $myuserStatus = Caches::MyuserZtStatus($uid);
            if($myuserStatus['status'] == 2){
                $schoolnamnid = $myinfo['schoolname'];
                $schooldata =Shcool::name('school_versions')->where('sid',$schoolnamnid)->field('txid as type,aid as tid')->select();
                if($schooldata ==null){
                    return $this->echo_json(260009);
                }
                $data =Label::label_tpname($schooldata);
                $result = [];
                foreach ($data as $k =>$v){
                    $data[$k]['language'] = 1;
                    if($v['type'] =='hangye'){
                        $data[$k]['tpid'] = $data[$k]['tid'];
                        $result['hangye'][] = $data[$k];
                    }elseif($v['type'] =='lx'){
                        $data[$k]['id'] = $data[$k]['tid'];
                        $result['lx'][] = $data[$k];
                    }elseif($v['type'] =='zhuanti'){
                        $data[$k]['id'] = $data[$k]['tid'];
                        $result['zhuanti'][] = $data[$k];
                    }

                }
                $result['expire'] = $expire;
                return $this->echo_json(0,'',$result);
            }
        }
        $hasdata = Db::name('myuser_zt_pay')->where('uid',$uid)->where('state',1)->order('id','desc')->find();
        if($hasdata==null){
            return $this->echo_json(260009);
        }
        $tpid = $hasdata['id'];
        $en_case = $hasdata['en_case'];
        if($en_case ==1){
            // 英文案例通道
            $labids =Lx_class::where('language',2)->column('id');
        }

        // 获取开通的通道
        $indata =  MyuserZtPay::getztpayzi($uid,$tpid);
        if($indata ===false){ return $this->echo_json(260009);  }
        $data = Label::label();

        foreach ($data as $key=>$val){
            foreach ($val as $ke =>$va){
                $data[$key][$ke]['language'] = 1;
                if($key=='hangye'  && !empty($indata['hangye']) && !in_array($va['tpid'],$indata['hangye'])){
                    unset($data[$key][$ke]);
                }elseif($key =='hangye' && empty($indata['hangye'])){
                    unset($data[$key][$ke]);
                }

                if($key=='lx' && !empty($indata['lx']) && !in_array($va['id'],$indata['lx'])){
                    if($en_case && in_array($va['id'],$labids)){
                        $data[$key][$ke]['language']= 2;
                    }else{
                        unset($data[$key][$ke]);
                    }
                }elseif($key =='lx' &&  empty($indata['lx']) && $en_case ){
                   if($en_case && in_array($va['id'],$labids)){
                       $data[$key][$ke]['language']= 2;
                   }else{
                       unset($data[$key][$ke]);
                   }
                }elseif($key =='lx' && empty($indata['lx'])){
                   unset($data[$key][$ke]);
                }elseif($key=='lx' ){
                    if($en_case && in_array($va['id'],$labids)){
                        $data[$key][$ke]['language']= 3;
                    }
                }

                if($key=='zhuanti' && !empty($indata['zhuanti']) && !in_array($va['id'],$indata['zhuanti'])){
                    unset($data[$key][$ke]);
                }elseif($key =='zhuanti' && empty($indata['zhuanti'])){
                    unset($data[$key][$ke]);
                }
            }
        }
        $data['expire'] = $expire;
        return $this->echo_json(0,'',$data);
    }



    /** 计划标签
     * @return \think\response\Json
     */
    public function plan_label(){
        $uid  = $this->uid;

        $mystatus = Caches::MyuserZtStatus($uid);


        $myinfo = Caches::MyuserInfoFiled($uid);
        if($mystatus['status']==0){
            return $this->echo_json(260009);
        }

        $endtime = $mystatus['zt_e_time'];

        $data1  = Label::get_plan_label($uid);
        $expire = 0;
        if($data1 ==false || $data1['expire']==1){
            $expire = 1;
        }

        $data  = Label::get_plan_label_comp($uid);
        $result['list'] = $data;
        $result['endtime'] = date('Y-m-d',$endtime);
        $result['expire'] =$expire ;
        return $this->echo_json(0,'',$result);
    }

    //放弃计划
    public function giveupplan()
    {
        if(empty($_POST['id'])   ||  !intval($_POST['id'])){
            return   $this->echo_json(30004);
        }
        $id = intval($_POST['id']);
        $uid = $this->uid;

        $data  = Label::give_up_plan($id,$uid);
        if($data){
            return  $this->echo_json(0);
        }else{
            return  $this->echo_json(130002);
        }

    }

    //添加计划
    public function write_plan(){

        if(empty($_POST['type'])){
            return $this->echo_json(30004);
        }
        if(count($_POST['type']) < 3){
            return $this->echo_json(130003);
        }
        $uid = $this->uid;

        $myinfo = Caches::MyuserInfoFiled($uid);
        if(!userinfo_is($myinfo)){
            return $this->echo_json(50012);
        }

        $array =[];
        foreach ($_POST['type'] as $value) {
           $temp = explode('_',$value);
            if((!in_array($temp[0],array('lx','hangye','zhuanti')))  || (!in_array($temp[2],array(1,2,3)))  ){
                continue;
            }
            $array[]=$temp;
        }

        if(Label::plan_has_no($uid)){
            if(!Label::has_update_plan($uid)){
                return $this->echo_json(130004);
            }

            $data = Label::update_plan_zi($uid,$array);
            if($data){
                return $this->echo_json(0);
            }else{
                return $this->echo_json(130002);
            }
        }else{
            $data = Label::add_plan($uid,$array);
            if($data){
                return $this->echo_json(0);
            }else{
                return $this->echo_json(130002);
            }
        }
    }
    /**
     *修改计划
     */
    public function plan_modfiy(){

//        if(empty($_POST['tpid'])  || ! intval($_POST['tpid']) ){
//            return $this->echo_json(30004);
//        }
//        if(empty($_POST['starttime'])){
//            return $this->echo_json(130005);
//        }
//        if(empty($_POST['endtime'])){
//            return $this->echo_json(130006);
//        }
//        if(empty($_POST['setting'])){
//            return $this->echo_json(130007);
//        }
//        if(empty($_POST['every']) ||  !intval($_POST['every']) ){
//            return $this->echo_json(130008);
//        }
//        if(empty($_POST['nums']) ||  !intval($_POST['nums'])){
//            return $this->echo_json(130009);
//        }
        $uid = $this->uid;
        $nums = intval($_POST['nums']);
        $tpid = intval($_POST['tpid']);
        $minnums = Label::labelnum($tpid,$uid);
        if($nums<$minnums){
           return $this->echo_json(130010);
        }
        $_POST['endtime'] = $_POST['endtime'] . " 23:59:59 ";
        $data = Label::update_plan($uid,$_POST);
        if($data){
            return  $this->echo_json(0);
        }
    }

    // 获取计划
    public function get_plan(){
        $uid = $this->uid;
        $has =Label::plan_has($uid);
        if($has){
            $data = Label::get_plan($uid);
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(0,'无');
        }
    }

    //计划日志
    public function planlog()
    {
        $uid = $this->uid;
        $num = 10;
        $page = intval($num * (!empty($_GET['page'])?$_GET['page']-1:0));
        $data =Label::plan_log($uid,$page);
        if($data){
           return $this->echo_json(0,'',$data);
        }else{
            return  $this->echo_json(130001);
        }
    }


    /**
     *  主题 数选择
     */
    public function getaisle(){
        $aisleid = '';
        $tpidstr = '';
        $redirect=0;
        $uid = $this->uid;
        if( !empty(input('get.tpid')  )  ){
            $tpidstr = trim(input('get.tpid'));
            $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
            $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();
        }else{
            $hasdata = Db::name('myuser_zt_pay')->where('state',0)->where('uid',$uid)->order('id','desc')->find();
            if($hasdata!=null && $hasdata['state'] ==1){
                $redirect=1;
                $tpid = $hasdata['id'];
                $tpidstr = (new \app\common\StringRsa)->encodeStr($tpid);
            }

        }
        if($hasdata!=null){
            $aisleid = $hasdata['aisleid'];
        }

        $data = Db::name('myuser_zt_aisle')->select();
        if($data ==null){
            $data ='';
        }

        foreach ($data as $key =>$value){
            $data[$key]['has']  = 0;
            if($value['id'] == $aisleid) $data[$key]['has']  = 1;

        }
        $result['balance'] = Myuser::userBalance($uid);
        $result['redirect'] =$redirect;
        $result['list'] =$data;
        $result['tpid'] =$tpidstr;
        return $this->echo_json(0,'',$result);

    }

    /**
     *  主题 确认主题数
     */
    public function dogetaisle(){
        if( empty(input('post.id')  )  || !is_numeric(input('post.id'))){
            return  $this->echo_json(260001);
        }
        $id = intval(input('post.id'));
        $uid = $this->uid;
        // 判断是否存在 所选择的
        $number = Db::name('myuser_zt_aisle')->where('id',$id)->value('number');
        if($number===null){
            return  $this->echo_json(260001);
        }
        $time = $this->request->time();

        $insertdata = [
            'uid'=>$uid,
            'aisleid'=>$id,
            'aislenum'=>$number
        ];

        $hasdata = Db::name('myuser_zt_pay')->where('state',0)->where('uid',$uid)->find();

        if($hasdata !=null){
            $res= Db::name('myuser_zt_pay')->where('state',0)->where('uid',$uid)->data($insertdata)->update();
            $tpid  = $hasdata['id'];
        }else{
            $hasdata2 = Db::name('myuser_zt_pay')->where('state',1)->where('endtime','>',$time)->where('uid',$uid)->find();
            if($hasdata2 !=null){
                return $this->echo_json(260010);
            }
            $res= Db::name('myuser_zt_pay')->insertGetId($insertdata);
            $tpid = $res;
        }


        if($number ==0){
            $Labeldata = Label::label();
            $insertdata =[];
            foreach ($Labeldata['hangye'] as $k =>$v){
                $inserttemp =['type'=>'hangye','tid'=> $v['tpid'],'uid'=>$uid,'tpid'=>$tpid];
                $insertdata[] = $inserttemp;
            }
            foreach ($Labeldata['zhuanti'] as $k =>$v){
                $inserttemp =['type'=>'zhuanti','tid'=>$v['id'],'uid'=>$uid,'tpid'=>$tpid];
                $insertdata[] = $inserttemp;
            }
            foreach ($Labeldata['lx'] as $k =>$v){
                $inserttemp =['type'=>'lx','tid'=>$v['id'],'uid'=>$uid,'tpid'=>$tpid];
                $insertdata[] = $inserttemp;
            }
            Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->delete();
            $res = Db::name('myuser_zt_pay_zi')->data($insertdata)->insertAll();
        }



        $resd['tpid'] = (new \app\common\StringRsa)->encodeStr($tpid);
        $resd['num'] = $number;
        return  $this->echo_json(0,'',$resd);

    }


    //标签
    public function aislelable(){
        $data = Label::label();
        $indata = [];
        if( !empty(input('get.tpid')  )  ){
            $tpidstr = trim(input('get.tpid'));
            $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
            $uid = $this->uid;
            $indata = MyuserZtPay::getztpayzi($uid,$tpid);
            if($indata===false){
                $indata =[];
            }
        }
        /*
         * ( ) &&  (count($data['lx']) ==count($indata['lx'])) && (count($data['zhuanti']) ==count($indata['zhuanti']) )*/

        foreach ($data as $key=>$val){
            foreach ($val as $ke =>$va){
                $data[$key][$ke]['has'] =0;
                if( empty($indata) || (  (count($data['hangye']) == count($indata['hangye'])) &&  (count($data['lx']) ==count($indata['lx'])) && (count($data['zhuanti']) ==count($indata['zhuanti']) ) ) ){

                }else{
                    if($key=='hangye' && !empty($indata['hangye']) && in_array($va['tpid'],$indata['hangye'])) $data[$key][$ke]['has'] =1;
                    if($key=='lx' && !empty($indata['lx'])&& in_array($va['id'],$indata['lx'])) $data[$key][$ke]['has'] =1;
                    if($key=='zhuanti' && !empty($indata['zhuanti']) && in_array($va['id'],$indata['zhuanti'])) $data[$key][$ke]['has'] =1;
                }
            }
        }


        return $this->echo_json(0,'',$data);
    }

    /**
     *  主题 通道确实
     */
    public function dopayzi(){
        if( empty(input('post.tpid')  )  ){
            return  $this->echo_json(260001);
        }

        $tpidstr = trim(input('post.tpid'));
        $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        $uid = $this->uid;
        $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();

        if($hasdata==null){
            return  $this->echo_json(260003);
        }
        $aislestr = trim(input('post.aisle'));
        $aislesarr = explode(',',$aislestr);
        $aislesarr = array_filter($aislesarr);
        //通道数
        $aislenum = $hasdata['aislenum'];
        if($aislenum !=0 && count($aislesarr) >$aislenum){
            return  $this->echo_json(260004);
        }

        $Labeldata = Label::label();

        foreach ($Labeldata['hangye'] as $k =>$v){

            $hangye_ds[] = $v['tpid'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[]  = $v['id'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[]  = $v['id'];
        }

        Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->delete();
        $insertdata =[];

        foreach ($aislesarr as $key =>$value){
            $temp = explode('_',$value);
            if(is_array($temp) && count($temp)==2){
                if( ($temp[0] =='hangye' && in_array($temp[1],$hangye_ds ) || ($temp[0] =='lx' && in_array($temp[1],$lx_ds) ) || ($temp[0] =='zhuanti' && in_array($temp[1],$zhuanti_ds))) ){
                    $inserttemp =['type'=>$temp[0],'tid'=>$temp[1],'uid'=>$uid,'tpid'=>$tpid];
                    $insertdata[] = $inserttemp;
                }

            }
        }


        if(empty($insertdata)){
            return  $this->echo_json(260005);
        }
        $res = Db::name('myuser_zt_pay_zi')->data($insertdata)->insertAll();

        return  $this->echo_json(0);

    }

    /**
     * 获取 开通时间
     */
    public function getztplan(){

        if( empty(input('get.tpid')  )  ){
            return  $this->echo_json(260001);
        }

        $tpidstr = trim(input('get.tpid'));
        $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        $uid = $this->uid;
        $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();
        if($hasdata==null){
            return  $this->echo_json(260003);
        }

        $aisleid = $hasdata['aisleid'];
        $classid = $hasdata['classid'];
        $evlaid = $hasdata['coa_eval'];
        $money = $hasdata['money'];

        $classdata  = Db::name('myuser_zt_class')->where('cid',$aisleid)->field('cid',true)->select();
        foreach ($classdata as $key =>$val){
            $classdata[$key]['has'] = 0;
            if($val['id'] == $classid ) $classdata[$key]['has'] = 1;
        }

        $Labeldata = Label::label();

        foreach ($Labeldata['hangye'] as $k =>$v){
            $hangye_ds[$v['tpid']] = $v['tpname'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[ $v['id']]  = $v['tpname'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[ $v['id']]  = $v['tpname'];
        }

        $aisle_list =[];
        $res = Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->select();
        if($res !==null){
            foreach ($res as $k =>$v){
                if($v['type'] =='hangye'){
                    $aisle_list[]=$hangye_ds[$v['tid']];
                }elseif($v['type'] =='lx'){
                    $aisle_list[]=$lx_ds[$v['tid']];
                }elseif($v['type'] =='zhuanti'){
                    $aisle_list[]=$zhuanti_ds[$v['tid']];
                }
            }
        }

        $eval_list = Db::name('myuser_zt_eval')->select();

        foreach ($eval_list as $k =>$v){
            $eval_list[$k]['has'] = 0;
            if($v['id'] == $evlaid ) $eval_list[$k]['has'] = 1;
        }
        $resd['aislenum'] = $hasdata['aislenum'];
        $resd['aisle_list'] = $aisle_list;
        $resd['eval_list'] = $eval_list;



        $resd['tpid'] = $tpidstr;
        $resd['money'] = $money;
        $resd['list'] = $classdata;
        $resd['balance'] = Myuser::userBalance($uid);
        $resd['coa_eval_has'] = $hasdata['coa_eval'];
        $resd['en_case_has'] = $hasdata['en_case'];

        return  $this->echo_json(0,'',$resd);

    }


    /**
     * 提交套餐
     */
    public function dosetmeal(){

        if( empty(input('post.tpid')  )  ){
            return  $this->echo_json(260001);
        }
        // 开通时间
        if( empty(input('post.hour'))   ){
            return  $this->echo_json(260006);
        }
        //总金额
        if( empty(input('post.money'))   ){
            return  $this->echo_json(260008);
        }
        $tpidstr = trim(input('post.tpid'));
        $hourid  = trim(input('post.hour'));
        $money  = trim(input('post.money'));

        $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        $uid = $this->uid;
        // 开通时间  是否在范围   tpid 是否存在对应的 数据
        $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();
        $has_class = Db::name('myuser_zt_class')->where('id',$hourid)->find();
        if($has_class==null){
            return  $this->echo_json(260007);
        }

        if($hasdata==null){
            return  $this->echo_json(260003);
        }



        $coa_eval = 0;
        $eval_num = 0;
        $eval_money = 0;
        $eval_num_surplus = 0;
        $has_class_money = 0;
        //是否开通教练评价
        if(!empty(input('post.coa_eval')) && intval(input('post.coa_eval')) >0  ){
            $evalid= intval(input('post.coa_eval'));
            $evaldata =Db::name('myuser_zt_eval')->where('id',$evalid)->find();
            if($evaldata ==null){
                return  $this->echo_json(260011);
            }
            $coa_eval = $evalid;
            $eval_num = $evaldata['strip'];
            $eval_num_surplus = $evaldata['strip'];
            $eval_money = $evaldata['money'];

        }



        $en_case = 0;
        $en_money = 0;
        //是否开通 英文案例
        if(!empty(input('post.en_case'))  ){
            $en_case = 1;
            $en_money = $has_class['en_case_mon'];
        }

        $moneys = $has_class['money']+$en_money+$eval_money;

        if($moneys != $money){
            return  $this->echo_json(260012);
        }


        $months = $has_class['months'];
        $datetime  = $this->request->time();
        $endtime= Paydef::delay_time($months);

        //'eval_num'=>$eval_num,'eval_num_surplus'=>$eval_num_surplus,

        $insertdata =['classid'=>$hourid,'money'=>$moneys,'coa_eval'=>$coa_eval,'en_case'=>$en_case,'en_money'=>$en_money,'months'=>$months,'endtime'=>$endtime,'datetime'=>$datetime];

        $res = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->data($insertdata)->update();
        if($eval_num){
            $insertdata_eval =['uid'=>$uid,'classid'=>$tpid,'eval_num'=>$eval_num,'money'=>$eval_money,'datetime'=>$datetime];
            $res = Db::name('myuser_zt_eval_pay')->insert($insertdata_eval);
        }
        return  $this->echo_json(0,'',$tpidstr);
        /*
         * tpid
         * hour : 开通时间  月份Id
         * coa_eval: 教练评价条数  0  未开通

         * en_case : 是否开通 英文案例 0 未开通
         * money : 总金额
         * */

    }

    /**
     * 套餐 xiangqing
     * @return \think\response\Json
     */
    public function case_deail(){
        $uid = $this->uid;
        if( empty(input('get.tpid')  )  ){
            $tpid = MyuserZtPay::userSetMealHas($uid)['id'];
            $tpidstr = (new \app\common\StringRsa)->encodeStr($tpid);
            //return  $this->echo_json(260001);
        }else{
            $tpidstr = trim(input('get.tpid'));
            $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        }



        // 开通时间  是否在范围   tpid 是否存在对应的 数据
        $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();
        if($hasdata==null){
            return  $this->echo_json(260003);
        }

        $Labeldata = Label::label();

        foreach ($Labeldata['hangye'] as $k =>$v){
            $hangye_ds[$v['tpid']] = $v['tpname'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[ $v['id']]  = $v['tpname'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[ $v['id']]  = $v['tpname'];
        }

        $aisle_list =[];
        $res = Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->select();
        $eval_num =0;
        if($res !==null){
            foreach ($res as $k =>$v){
                if($v['type'] =='hangye'){
                    $aisle_list[]=$hangye_ds[$v['tid']];
                }elseif($v['type'] =='lx'){
                    $aisle_list[]=$lx_ds[$v['tid']];
                }elseif($v['type'] =='zhuanti'){
                    $aisle_list[]=$zhuanti_ds[$v['tid']];
                }
            }

            $res1 = Db::name('myuser_zt_eval_pay')->where('uid',$uid)->where('classid',$tpid)->value('eval_num');
            if($res1!=null){
                $eval_num = $res1;
            }

        }



        $result['aislenum'] = $hasdata['aislenum'];
        $result['aisle_list'] = $aisle_list;
        $result['months'] =  $hasdata['months'];
        $result['coa_eval'] =  $eval_num ;
        $result['en_case'] =  $hasdata['en_case'];
        $result['tpid'] =  $tpidstr;
        $result['money'] =  $hasdata['money'];;
        $result['balance'] =  $money = Myuser::userBalance($uid);


        return $this->echo_json(0,'',$result);
    }


    /**
     * 支付方式  ；
     */
    public function dopay(){
        if( empty(input('post.tpid')  )  ){
            return  $this->echo_json(260001);
        }
        if( empty(input('post.action') ) || trim(input('post.action')) !='yu' ){
            return  $this->echo_json(260013);
        }


        $tpidstr = trim(input('post.tpid'));
        $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        $uid = $this->uid;
        // 开通时间  是否在范围   tpid 是否存在对应的 数据
        $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();
        if($hasdata==null){
            return  $this->echo_json(260003);
        }
        $money = $hasdata['money'];
        $balance = Myuser::userBalance($uid);
        if($money>$balance){
            return  $this->echo_json(23001);
        }
        $res =  Db::name('myuser_account')->where('uid',$uid)->setDec('money', $money);;
        if($res){
            $code = Paydef::ordernum();
            $insertdata = ['code'=>$code,'state'=>1] ;
            $res= Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->data($insertdata)->update();
            $res1= Db::name('myuser_zt_eval_pay')->where('classid',$tpid)->where('uid',$uid)->find();
            if($res1!=null){
                Db::name('myuser_zt_eval_pay')->where('classid',$tpid)->where('uid',$uid)->data($insertdata)->update();
                $eval_num = $res1['eval_num'];
                Myuser::name('myuser_account')->where('uid',$uid)->setInc('eval_num', $eval_num);
            }

            $add_consume['record'] = '余额支付套餐' ;
            $add_consume['code']=$code;
            $add_consume['uid']=$uid;
            $add_consume['money']=$money;
            $add_consume['pay_type']=8;
            $add_consume['datetime']=time();
            Paydef::consume_add($add_consume);
            $smg ='购买套餐，消费 '.$money.'元';
            $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
            UserMessage::insert($insertdata);
            UserMessage::send_meal_mess($uid);
        }


        return $this->echo_json(0);
    }


    /**
     * 支付方式  ；
     */
    public function dopay_zeng(){
        if( empty(input('post.action') ) || trim(input('post.action')) !='yu' ){
            return  $this->echo_json(260013);
        }
        $coa_eval = intval(input('post.coa_eval'));
        $uid = $this->uid;
        $time = $this->request->time();
        $eval_num = 0;
        $eval_money = 0;
        //是否开通教练评价
        if(!empty(input('post.coa_eval')) && intval(input('post.coa_eval')) >0  ){
            $evalid= intval(input('post.coa_eval'));
            $evaldata =Db::name('myuser_zt_eval')->where('id',$evalid)->find();
            if($evaldata ==null){
                return  $this->echo_json(260011);
            }
            $eval_num = $evaldata['strip'];
            $eval_money = $evaldata['money'];
        }else{
            return  $this->echo_json(260011);
        }


        $money = $eval_money;
        $balance = Myuser::userBalance($uid);
        if($money>$balance){
            return  $this->echo_json(23001);
        }
        $res =  Db::name('myuser_account')->where('uid',$uid)->setDec('money', $money);;
        if($res){
            $code = Paydef::ordernum();
            $insertdata = ['code'=>$code,'state'=>1,'money'=>$money,'eval_num'=>$eval_num,'datetime'=>$time,'uid'=>$uid] ;
            $res1 = Db::name('myuser_zt_eval_pay')->insert($insertdata);
            if($res1){
                Myuser::name('myuser_account')->where('uid',$uid)->setInc('eval_num', $eval_num);
                $add_consume['record'] = '购买教练评价' ;
                $add_consume['code']=$code;
                $add_consume['uid']=$uid;
                $add_consume['money']=$money;
                $add_consume['pay_type']=8;
                $add_consume['datetime']=time();
                Paydef::consume_add($add_consume);

                $smg ='购买教练评价，消费 '.$money.'元';
                $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
                UserMessage::insert($insertdata);
                UserMessage::send_eval_mess($uid);
            }

        }


        return $this->echo_json(0);
    }

    public function case_deail_get(){
        $uid = $this->uid;

        $myinfo = Caches::MyuserInfoFiled($uid);
        if( empty(input('get.tpid')  )  ){
            $tpid = MyuserZtPay::userSetMealHas($uid)['id'];
            $tpidstr = (new \app\common\StringRsa)->encodeStr($tpid);
            //return  $this->echo_json(260001);

        }else{
            $tpidstr = trim(input('get.tpid'));
            $tpid = (new \app\common\StringRsa)->decodeStr($tpidstr);
        }



        if($myinfo['viptype']){
            return $this->echo_json(0);
        }


        // 开通时间  是否在范围   tpid 是否存在对应的 数据
        $hasdata = Db::name('myuser_zt_pay')->where('id',$tpid)->where('uid',$uid)->find();
        if($hasdata==null){
            return  $this->echo_json(260003);
        }

        $Labeldata = Label::label();

        foreach ($Labeldata['hangye'] as $k =>$v){
            $hangye_ds[$v['tpid']] = $v['tpname'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[ $v['id']]  = $v['tpname'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[ $v['id']]  = $v['tpname'];
        }

        $aisle_list =[];
        $res = Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->select();
        $eval_num =0;
        if($res !==null){
            foreach ($res as $k =>$v){
                if($v['type'] =='hangye'){
                    $aisle_list[]=$hangye_ds[$v['tid']];
                }elseif($v['type'] =='lx'){
                    $aisle_list[]=$lx_ds[$v['tid']];
                }elseif($v['type'] =='zhuanti'){
                    $aisle_list[]=$zhuanti_ds[$v['tid']];
                }
            }
            $res1 = Db::name('myuser_account')->where('uid',$uid)->value('eval_num');
            if($res1!=null){
                $eval_num = $res1;
            }
        }

        $result['aislenum'] = $hasdata['aislenum'];
        $result['aisle_list'] = $aisle_list;
        $result['months'] =  $hasdata['months'];
        $result['coa_eval'] =  $eval_num;
        $result['en_case'] =  $hasdata['en_case'];
        $result['tpid'] =  $tpidstr;
        $result['money'] =  $hasdata['money'];;
        $result['balance'] =  $money = Myuser::userBalance($uid);



        return $this->echo_json(0,'',$result);
    }

    /**
     * 获取 开通时间
     */
    public function zxgetztplan(){


        $uid=  $this->uid;
        $eval_list = Db::name('myuser_zt_eval')->select();

        foreach ($eval_list as $k =>$v){
            $eval_list[$k]['has'] = 0;
//            if($v['id'] == $evlaid ) $eval_list[$k]['has'] = 1;
        }

        $resd['eval_list'] = $eval_list;
        $resd['balance'] = Myuser::userBalance($uid);
//        $resd['tpid'] = $tpidstr;
//        $resd['money'] = $money;
//        $resd['list'] = $classdata;
//        $resd['balance'] = Myuser::userBalance($uid);
//        $resd['coa_eval_has'] = $hasdata['coa_eval'];
//        $resd['en_case_has'] = $hasdata['en_case'];

        return  $this->echo_json(0,'',$resd);

    }



    /**
     *  主题 数选择
     */
    public function getaisle_set(){

        $uid = $this->uid;

        $hasdata = MyuserZtPay::userSetMealHas($uid);
        if($hasdata==null ){
            return  $this->echo_json(260009);
        }

        $aisleid = $hasdata['aisleid'];
        $aislenum = $hasdata['aislenum'];


        $data = Db::name('myuser_zt_aisle')->select();
        if($data ==null){
            $data ='';
        }

        foreach ($data as $key =>$value){
            $data[$key]['has']  = 0;
            if($value['id'] == $aisleid) $data[$key]['has']  = 1;

        }
        $result['balance'] = Myuser::userBalance($uid);

        $result['list'] =$data;
        $result['aislenum'] =$aislenum;

        return $this->echo_json(0,'',$result);

    }



    public function dogetaisle_set(){
        if( empty(input('post.id')  )  || !is_numeric(input('post.id'))){
            return  $this->echo_json(260001);
        }

        $id = intval(input('post.id'));
        $uid = $this->uid;
        $myztpay = MyuserZtPay::userSetMealHas($uid);
        if($myztpay==null ){
            return  $this->echo_json(260009);
        }
        // 判断是否存在 所选择的
        $number = Db::name('myuser_zt_aisle')->where('id',$id)->value('number');
        if($number===null){
            return  $this->echo_json(260001);
        }
        $time = $this->request->time();

        if( ($myztpay['aislenum'] ==0 && $number !=0)  ||  ( ($myztpay['aislenum'] > $number ) && $number !=0 ) ){
            return  $this->echo_json(260014);
        }

        if($number ==0){
            $Labeldata = Label::label();
            $insertdata =[];
            foreach ($Labeldata['hangye'] as $k =>$v){
                $inserttemp =['type'=>'hangye','tid'=> $v['tpid'],'uid'=>$uid];
                $insertdata[] = $inserttemp;
            }
            foreach ($Labeldata['zhuanti'] as $k =>$v){
                $inserttemp =['type'=>'zhuanti','tid'=>$v['id'],'uid'=>$uid];
                $insertdata[] = $inserttemp;
            }
            foreach ($Labeldata['lx'] as $k =>$v){
                $inserttemp =['type'=>'lx','tid'=>$v['id'],'uid'=>$uid];
                $insertdata[] = $inserttemp;
            }

            $insertdata_str = json_encode($insertdata);

            Cache::set('userlabel'.$uid,$insertdata_str);
            //Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->delete();
            //$res = Db::name('myuser_zt_pay_zi')->data($insertdata)->insertAll();
        }

        $user_classid = $myztpay['classid'];
        $myuser_zt_class =Db::name('myuser_zt_class')->where('id',$user_classid)->find();
        $months = $myuser_zt_class['months'];
        $classid  =Db::name('myuser_zt_class')->where('months',$months)->where('cid',$id)->value('id');

        $insertpay= [
            'uid'=>$uid,
            'aisleid'=>$id,
            'classid'=>$classid,
            'aislenum'=>$number,
            'endtime'=>$myztpay['endtime']
        ];
        Cache::set('userinsertpay'.$uid,$insertpay);

        $resd['num'] = $number;
        return  $this->echo_json(0,'',$resd);

    }

    public function aislelable_set(){
        $data = Label::label();
        $indata = [];
        $uid = $this->uid;

        $myztpay =MyuserZtPay::userSetMealHas($uid);
        if($myztpay==null){
            return  $this->echo_json(260009);
        }

        $tpid = $myztpay['id'];
        $indata = MyuserZtPay::getztpayzi($uid,$tpid);
        if($indata===false){
            $indata =[];
        }


        /*
         * ( ) &&  (count($data['lx']) ==count($indata['lx'])) && (count($data['zhuanti']) ==count($indata['zhuanti']) )*/

        foreach ($data as $key=>$val){
            foreach ($val as $ke =>$va){
                $data[$key][$ke]['has'] =0;
                if( empty($indata) || (  (count($data['hangye']) == count($indata['hangye'])) &&  (count($data['lx']) ==count($indata['lx'])) && (count($data['zhuanti']) ==count($indata['zhuanti']) ) ) ){

                }else{
                    if($key=='hangye' && !empty($indata['hangye']) && in_array($va['tpid'],$indata['hangye'])) $data[$key][$ke]['has'] =1;
                    if($key=='lx' && !empty($indata['lx'])&& in_array($va['id'],$indata['lx'])) $data[$key][$ke]['has'] =1;
                    if($key=='zhuanti' && !empty($indata['zhuanti']) && in_array($va['id'],$indata['zhuanti'])) $data[$key][$ke]['has'] =1;
                }
            }
        }
        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        $result['list'] = $data;
        $result['number'] = $userinsertpay['aislenum'] ;

        return $this->echo_json(0,'',$result);
    }



    public function dopayzi_set(){
        $uid = $this->uid;
        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        $number = $userinsertpay['aislenum'] ;


        $aislestr = trim(input('post.aisle'));
        $aislesarr = explode(',',$aislestr);
        $aislesarr = array_filter($aislesarr);
        //通道数
        $aislenum = $number;
        if($aislenum !=0 && count($aislesarr) >$aislenum){
            return  $this->echo_json(260004);
        }

        $Labeldata = Label::label();

        foreach ($Labeldata['hangye'] as $k =>$v){

            $hangye_ds[] = $v['tpid'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[]  = $v['id'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[]  = $v['id'];
        }

       // Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->delete();
        $insertdata =[];

        foreach ($aislesarr as $key =>$value){
            $temp = explode('_',$value);
            if(is_array($temp) && count($temp)==2){
                if( ($temp[0] =='hangye' && in_array($temp[1],$hangye_ds ) || ($temp[0] =='lx' && in_array($temp[1],$lx_ds) ) || ($temp[0] =='zhuanti' && in_array($temp[1],$zhuanti_ds))) ){
                    $inserttemp =['type'=>$temp[0],'tid'=>$temp[1],'uid'=>$uid];
                    $insertdata[] = $inserttemp;
                }

            }
        }


        if(empty($insertdata)){
            return  $this->echo_json(260005);
        }
        //$res = Db::name('myuser_zt_pay_zi')->data($insertdata)->insertAll();

        $insertdata_str = json_encode($insertdata);

        Cache::set('userlabel'.$uid,$insertdata_str);

        return  $this->echo_json(0);

    }


    /**
     * @return \think\response\Json
     */
    public function getztplan_set(){

        $uid = $this->uid;

        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        if(!$userinsertpay){
            return  $this->echo_json(260003);
        }

        $number = $userinsertpay['aislenum'] ;
        $aisleid = $userinsertpay['aisleid'];
        $classid = $userinsertpay['classid'];

        $myztpay = MyuserZtPay::userSetMealHas($uid);

        $user_classid = $myztpay['classid'];
        $myuser_zt_class =Db::name('myuser_zt_class')->where('id',$user_classid)->find();

        $list  =Db::name('myuser_zt_class')->where('id',$classid)->find();

        $Labeldata = Label::label();

        foreach ($Labeldata['hangye'] as $k =>$v){
            $hangye_ds[$v['tpid']] = $v['tpname'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[ $v['id']]  = $v['tpname'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[ $v['id']]  = $v['tpname'];
        }

        $aisle_list =[];
        //$res = Db::name('myuser_zt_pay_zi')->where('uid',$uid)->where('tpid',$tpid)->select();

        $insertdata_str  = Cache::get('userlabel'.$uid);
        $res = json_decode($insertdata_str,true);

        if($res !==null){
            foreach ($res as $k =>$v){
                if($v['type'] =='hangye'){
                    $aisle_list[]=$hangye_ds[$v['tid']];
                }elseif($v['type'] =='lx'){
                    $aisle_list[]=$lx_ds[$v['tid']];
                }elseif($v['type'] =='zhuanti'){
                    $aisle_list[]=$zhuanti_ds[$v['tid']];
                }
            }
        }


        $resd['aislenum'] = $number;
        $resd['aisleid'] = $aisleid;
        $resd['aisle_list'] = $aisle_list;

        $endtime = $myztpay['endtime'];
        $ksdatetime = $myztpay['datetime'];
        $days = intval(( $endtime-$this->request->time()) / 86400); ;
        $daysmonth = intval(($endtime- $ksdatetime) / 86400);

        if( $myztpay['en_case']){
            $resd['money'] = round((($myuser_zt_class['money'] + $myuser_zt_class['en_case_mon'])/$daysmonth )*$days,1) ;// $money;
        }else{
            $resd['money'] = round((($myuser_zt_class['money'])/$daysmonth )*$days,1)   ;// $money;
        }


        $insertpay= Cache::get('userinsertpay'.$uid);
        $insertpay['money'] = $resd['money'];
        Cache::set('userinsertpay'.$uid,$insertpay);

        $resd['list'] = $list;// $money;

        $resd['balance'] = Myuser::userBalance($uid);

        $resd['en_case_has'] = $myztpay['en_case'];

        return  $this->echo_json(0,'',$resd);

    }



    /**
     * 提交套餐
     */
    public function dosetmeal_set(){


        // 开通时间
        if(empty(input('money'))){
            return  $this->echo_json(260008);
        }


        $poost_money = input('money');
        $en_case = empty(input('en_case'))?0:1;
        $uid =$this->uid;
        $time =$this->request->time();

        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        if(!$userinsertpay){
            return  $this->echo_json(260003);
        }

        $myztpay = MyuserZtPay::userSetMealHas($uid);
        $money_list = MyuserZtPay::name('myuser_zt_class')->where('id',$userinsertpay['classid'])->find();
        if($en_case){
            $money = $money_list['money']+$money_list['en_case_mon'];
        }else{
            $money = $money_list['money'];
        }

        $pay_money = $money-$userinsertpay['money'];

        if((string)$pay_money != $poost_money){
            return  $this->echo_json(260008);
        }
        $insertpay= Cache::get('userinsertpay'.$uid);
        $insertpay['moneys'] = $pay_money;
        $insertpay['en_case'] = $en_case;
        $insertpay['months'] = $money_list['months'];
        $insertpay['en_money'] = $money_list['en_case_mon'];
        Cache::set('userinsertpay'.$uid,$insertpay);


        return  $this->echo_json(0);
        /*
         * tpid
         * hour : 开通时间  月份Id
         * coa_eval: 教练评价条数  0  未开通

         * en_case : 是否开通 英文案例 0 未开通
         * money : 总金额
         * */

    }
    public function case_deail_set(){

        $uid = $this->uid;

        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        if(!$userinsertpay){
            return  $this->echo_json(260003);
        }

        $number = $userinsertpay['aislenum'] ;
        $aisleid = $userinsertpay['aisleid'];
        $classid = $userinsertpay['classid'];

        $myztpay = MyuserZtPay::userSetMealHas($uid);

        $user_classid = $myztpay['classid'];
        $myuser_zt_class =Db::name('myuser_zt_class')->where('id',$user_classid)->find();
        $Labeldata = Label::label();
        foreach ($Labeldata['hangye'] as $k =>$v){
            $hangye_ds[$v['tpid']] = $v['tpname'];
        }
        foreach ($Labeldata['zhuanti'] as $k =>$v){
            $zhuanti_ds[ $v['id']]  = $v['tpname'];
        }
        foreach ($Labeldata['lx'] as $k =>$v){
            $lx_ds[ $v['id']]  = $v['tpname'];
        }
        $aisle_list =[];
        $insertdata_str  = Cache::get('userlabel'.$uid);
        $res = json_decode($insertdata_str,true);
        if($res !==null){
            foreach ($res as $k =>$v){
                if($v['type'] =='hangye'){
                    $aisle_list[]=$hangye_ds[$v['tid']];
                }elseif($v['type'] =='lx'){
                    $aisle_list[]=$lx_ds[$v['tid']];
                }elseif($v['type'] =='zhuanti'){
                    $aisle_list[]=$zhuanti_ds[$v['tid']];
                }
            }
        }

        $resd['aislenum'] = $number;
        $resd['aisleid'] = $aisleid;
        $resd['aisle_list'] = $aisle_list;


        $resd['balance'] = Myuser::userBalance($uid);
        $resd['en_case_has'] = $userinsertpay['en_case'];
        $resd['months'] = $userinsertpay['months'];
        $resd['money'] = $userinsertpay['moneys'];
        return  $this->echo_json(0,'',$resd);

    }

    public function dopay_set(){

        if( empty(input('post.action') ) || trim(input('post.action')) !='yu' ){
            return  $this->echo_json(260013);
        }


        $uid = $this->uid;
        $time = $this->request->time();
        $userinsertpay=  Cache::get('userinsertpay'.$uid);
        if(!$userinsertpay){
            return  $this->echo_json(260003);
        }
        $myztpay = MyuserZtPay::userSetMealHas($uid);

        // 开通时间  是否在范围   tpid 是否存在对应的 数据

        $money = $userinsertpay['moneys'];
        $balance = Myuser::userBalance($uid);
        if($money>$balance){
            return  $this->echo_json(23001);
        }

        $tpid = $myztpay['id'];

        $insertdata_str  = Cache::get('userlabel'.$uid);
        $insertdata_label = json_decode($insertdata_str,true);

        $code = Paydef::ordernum();
        $insertdata = ['code'=>$code,'state'=>1,'uid'=>$uid,'aisleid'=>$userinsertpay['aisleid'],'classid'=>$userinsertpay['classid'],'en_case'=>$userinsertpay['en_case'],'en_money'=>$userinsertpay['en_money'],'aislenum'=>$userinsertpay['aislenum'],'months'=>$userinsertpay['months'],'money'=>$userinsertpay['moneys'],'endtime'=>$userinsertpay['endtime'],'datetime'=>$time];


// 启动事务
        MyuserZtPay::startTrans();
        try {
            $res =  MyuserZtPay::name('myuser_account')->where('uid',$uid)->setDec('money', $money);;
            if($res){
                $res = MyuserZtPay::where('id',$tpid)->data(['state'=>2])->update();
                $lastid = MyuserZtPay::insertGetId($insertdata);
                if($lastid){
                    $insertdata_label = array_map(function ($arr)use($lastid){$temp['tpid']=$lastid; return  $arr = array_merge($temp,$arr);},$insertdata_label);
                    $res1= MyuserZtPay::name('myuser_zt_pay_zi')->insertAll($insertdata_label);
                    $add_consume['record'] = '余额支付套餐' ;
                    $add_consume['code']=$code;
                    $add_consume['uid']=$uid;
                    $add_consume['money']=$money;
                    $add_consume['pay_type']=8;
                    $add_consume['datetime']=time();
                    Paydef::consume_add($add_consume);
                    $smg ='购买套餐，消费 '.$money.'元';
                    $insertdata=['uid'=>$uid,'titlename'=>'消费信息','type'=>8,'content'=>$smg,'datetime'=>time()];
                    UserMessage::insert($insertdata);
                    UserMessage::send_meal_mess($uid);
                }

            }
            MyuserZtPay::commit();
        } catch (\Exception $e) {
            // 回滚事务
            MyuserZtPay::rollback();
        }

        return $this->echo_json(0);
    }

}
