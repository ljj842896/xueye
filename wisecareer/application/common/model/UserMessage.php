<?php
namespace app\common\model;

use app\api\controller\Navigation;
use app\common\Caches;
use app\common\MobileSms;
use app\common\WechatCallback;
use think\Model;

class UserMessage extends Model
{

    /**获取用户消息
     * @param $uid
     * @param $type
     * @param $page
     * @param $array
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_message($uid, $type, $page, $array ,$typein){


        if($type==1){
            $data = self::where('uid',$uid)->where('sop',0)->whereIn('type',$typein)->field('titlename,content,datetime')->order('datetime','desc')->limit($page)->select();
            foreach ($data as $key => $value) {
                $data[$key]['datetime'] = date('Y-m-d H:i:s' , $value['datetime']);
            }
        }elseif($type==2){
            $data = self::name('system_message')->wherein('lx',$array['lx'])->wherein('scid',$array['scid'])->wherein('sdid',$array['sdid'])->field('titlename,content,datetime')->order('datetime','desc')->limit($page)->select();
            foreach ($data as $key => $value) {
                $data[$key]['datetime'] = date('Y-m-d H:i:s' , $value['datetime']);
            }
        }elseif($type==3){
            //教练
            $data = self::name('app_message')->where('userid',$uid)->order('rtime','desc')->limit($page)->select();
            foreach ($data as $key => $value) {
                $data[$key]['datetime'] = date('Y-m-d H:i:s' , $value['rtime']);
                $data[$key]['content'] = $value['replay'];
                $data[$key]['titlename'] =self::app_member_Info($value['uid']);
            }
        }else{
            $data1 = self::where('uid',$uid)->where('sop',0)->whereIn('type',$typein)->field('titlename,content,datetime')->order('datetime','desc')->select();
            foreach ($data1 as $key => $value) {
                $data1[$key]['datetime'] = date('Y-m-d H:i:s' , $value['datetime']);
            }
            $data2 = self::name('system_message')->field('titlename,content,datetime')->order('datetime','desc')->select();
            foreach ($data2 as $key => $value) {
                $data2[$key]['datetime'] = date('Y-m-d H:i:s' , $value['datetime']);
            }
            $data3 = self::name('app_message')->field('uid,replay,rtime')->where('userid',$uid)->order('rtime','desc')->select();
            foreach ($data3 as $key => $value) {
                $data3[$key]['datetime'] = date('Y-m-d H:i:s' , $value['rtime']);
                unset($data3[$key]['rtime']);
                $data3[$key]['content'] = $value['replay'];
                unset($data3[$key]['replay']);
                $data3[$key]['titlename'] =self::app_member_Info($value['uid']);
                unset($data3[$key]['uid']);
            }
            $num = (($page-1)*10);
            $data1 = gettype($data1)=='object'?$data1->toArray():$data1;
            $data2 = gettype($data2)=='object'?$data2->toArray():$data2;
            $data3 = gettype($data3)=='object'?$data3->toArray():$data3;
            $res =  array_merge_recursive($data1,$data2,$data3);
            $result= array_sort($res,'datetime','desc');
            $data = array_slice($result,$num,10);

        }
        foreach($data as $key =>$value){
            $data[$key]['pic'] = ' ';
        }
        return $data;
    }


    /**教练的名称
     * @param $uid
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function app_member_Info($uid ){
        //获取数据库字段
        $data = self::name('center_user')->whereid($uid)->field('oid,uid')->find();
        if($data['oid'] == 1){
            $table = 'school_useradmin';
        }elseif($data['oid'] == 2){
            $table = 'app_member';
        }
        $datas = self::name($table)->whereid($data['uid'])->value("coach_nickname");
        return  $datas;
    }


    /**
     *发送预警消息
     */
    public static function send_subject_message($uid){
        $has = self::where('uid',$uid)->where('type',1)->find();
        if($has){
            return false;
        }
        $myinfo = Caches::MyuserInfoFiled($uid);
        $schoolid = $myinfo['schoolname'];
        $viptype = $myinfo['viptype'];
        $classid = $myinfo['classname'];
        $subject = $myinfo['subject'];
        // 判断决策树是否完成  选科 是否完成
        if(empty($myinfo['wensrid']) || $myinfo['wensrid'] ==0 || empty( $myinfo['subject']) || $myinfo['wensrid'] ==0){
            return false;
        }
        // 专业是否收藏
        $sc_edu_num = count(Caches::EduUserScId($uid));
        if($sc_edu_num < 1 ){
            return false;
        }
        $cids= array(3,7,10,30,39,46);
        $data = Shcool::collection($uid,$schoolid,$cids);
        $numplociy = 0;
        $num = 0;
        $smg = '';
        foreach ($data as $key => $value) {
            $numplociy = $numplociy+$value['tages'];
            $num+=1;
        }
        $edu_sc_policynum = is_null( @round(($numplociy / $num),2))?0: @round(($numplociy / $num),2);
        if($edu_sc_policynum <60){
            $smg .= '匹配度小于 60% ；';
        }

        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $data2 = Navigationa::get_planning($uid,$schoolid,$viptype,$classid);
        $data2['interestprompt'] && $smg .= $data2['interestptmsg'] .'；';
        $data2['industryprompt'] && $smg .= $data2['industryptmsg'] .'；';
        $data2['targetprompt'] && $smg .= $data2['targetpromptmsg'] .'；';
        $data2['advantageprompt'] && $smg .= $data2['advantagepromptmsg'] .'；';
        $data2['advantageprompt1'] && $smg .= $data2['advantagepromptmsg1'] .'；';
        $data2['disciplineprompt'] && $smg .= $data2['disciplinepromptmsg'] .'；';
        $data2['disciplineprompt1'] && $smg .= $data2['disciplinepromptmsg1'] .'；';
        $result['subjectno'] = 0;
        $result['subjectnomsg'] ='';
        $data = Navigationa::get_combination($uid,$schoolid,$subject,$viptype,$classid);
        if(!empty($data)){
            $result['nagigt'] = 1;
            foreach($data['arrno'] as $value){
                if($value['tpid'] == $subject){
                    $smg .= "你的选科方案与学业决策参数存在冲突，建议调整！";
                    $smgstr = "【彩虹联】你的选科方案与学业决策参数存在冲突，建议调整！";
                    $mobile = Caches::MyuserInfoFiled($uid)['dianhua'];
                    if(Phone($mobile)){
                        $MolileSms =  new MobileSms();
                        $MolileSms->send_sms($mobile,$uid,$smgstr);
                    }
                }
            }
        }

        // 微信预警发信息
      //  $WechatCallback = new WechatCallback();
       // $WechatCallback->run();
        $insertdata=['uid'=>$uid,'titlename'=>'决策预警','type'=>1,'content'=>$smg,'datetime'=>time()];
        return self::name('user_message')->data($insertdata)->insert();

    }

    /**
     *发送体验信息
     */
    public static function send_experience_message($tid,$uid){
        $data = UserTy::where(['id'=>$tid,'uid'=>$uid])->field('stopdate,jifen,datetime,ztid,hangye,lx,zdefen,defen,tysj')->find();
        if($data == null){
            return false;
        }
        $result = [];
        $tpname = Evaluate::get_tpname($data);
        $result = array_merge($result,$tpname);
        $result['jifen']= Evaluate::zongjifen($uid,$tid);
        $result['zjifen']=Evaluate::rep_integral($uid);

        $title = $result['tpname'];
        $jifen = $result['jifen'];
        $zjifen = $result['zjifen'];

        $webstr ="你已完成".$title."体验学习。获得".$jifen."体验积分。积分总为".$zjifen."。";
        $wxstr  ="你已完成".$title."体验学习。获得".$jifen."体验积分。积分总为".$zjifen."。";

        //发送web 消息
        $insertdata=['uid'=>$uid,'titlename'=>'体验积分','type'=>4,'content'=>$webstr,'datetime'=>time()];
        return self::name('user_message')->data($insertdata)->insert();

    }


    /**
     *发送代金券 消息
     */
    public static function send_coupon_message($uid,$rate){
        $money = Myuser::userBalance($uid);
        $webstr ="你成功获得".$rate."元代金券。账户余额".$money."职拓币。";//账户有效期至    年  月  日。
        //发送web 消息
        $insertdata=['uid'=>$uid,'titlename'=>'代金券','type'=>5,'content'=>$webstr,'datetime'=>time()];
        return self::name('user_message')->data($insertdata)->insert();

    }


    /**
     * 报名 通知
     * @return int|string
     */
    public static function send_invi_sms($uid,$array){
        $code = $array['code'];
        $titlename = $array['titlename'];
        $title = $array['title'];
        $ksdata =  date('Y-m-d H:i:s',$array['ksdate']);
        $smgstr="【彩虹联】你已成功报名“".$titlename."”。时间 ".$ksdata."，地点在".$title."。请准时于现场签到";

        $myinfo = Caches::MyuserInfoFiled($uid);
        $mobile =$myinfo['dianhua'];
        $ztopenid =$myinfo['ztopenid'];
        if(Phone($mobile)){
            $MolileSms =  new MobileSms();
            $MolileSms->send_sms($mobile,$uid,$smgstr);
        }
        $webstr=" 祝贺你成功预约了 ".$titlename."实习机会。实习确认号 ".$code."。 ";
        if($ztopenid){
            $WechatCallback = new WechatCallback('zt');
            $WechatCallback->send_invi_sms($ztopenid,$webstr,$titlename,$ksdata);
        }


        $insertdata=['uid'=>$uid,'titlename'=>'报名通知','type'=>7,'content'=>$webstr,'datetime'=>time()];
        return self::name('user_message')->data($insertdata)->insert();
    }



    public static function send_meal_mess($uid){
    //您的账户成功充值 xxxx 元。账户余额为xxxxx职拓币。有效期至    年  月  日。
        $myuser = Caches::MyuserInfoFiled($uid);
        $ztopenid = $myuser['ztopenid'];
        $balance = Myuser::userBalance($uid);
        //$mess=  "您的账户成功充值 {$money} 元。账户余额为{$balance}职拓币。";
        $data =MyuserZtPay::userSetMealHas($uid);
        $date =date('Y-m-d ',$data['endtime']);
        $money =$data['money'].'元';
        $webstr = "您的账户成功开通体验套餐，有效期至{$date}。";
        $titlename = "套餐开通";
        $datetime = date('y-m-d H:i:s');
        if($ztopenid){
            $WechatCallback = new WechatCallback('zt');
            $WechatCallback->send_meal_sms($ztopenid,$webstr,$titlename,$money,$datetime);
        }


    }


    public static function send_eval_mess($uid){
        //您的账户成功充值 xxxx 元。账户余额为xxxxx职拓币。有效期至    年  月  日。
        $myuser = Caches::MyuserInfoFiled($uid);
        $ztopenid = $myuser['ztopenid'];
        $balance = Myuser::userBalance($uid);
        //$mess=  "您的账户成功充值 {$money} 元。账户余额为{$balance}职拓币。";
        $data =MyuserZtPay::userEvalHas($uid);
        //$date =date('Y-m-d ',$data['endtime']);
        $money =$data['money'].'元';
        $webstr = "您的账户成功购买教练评价。";
        $titlename = "教练评价开通";
        $datetime = date('y-m-d H:i:s');
        if($ztopenid){
            $WechatCallback = new WechatCallback('zt');
            $WechatCallback->send_meal_sms($ztopenid,$webstr,$titlename,$money,$datetime);
        }


    }


}



