<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Coach extends Model
{
    private static  $table1 = 'center_user';


    public static function userInfo($uid ,$file =''){
        //获取数据库字段
        $data = self::name(self::$table1)->where('id',$uid)->find();
        if($data['oid'] == 1){
            $table = 'school_useradmin';
        }elseif($data['oid'] == 2){
            $table = 'app_member';
        }
        $datas = self::name($table)->where('id',$data['uid'])->find();
        $datas['center_user'] = $data;
        if($file !='' && !is_array($file)){
            return  $datas[$file];
        }else{
            return  $datas;
        }

    }

    public  static function get_star($userid,$uids=''){
        if(empty($uids)){
            $data =  self::name(self::$table1)->where('oid',2)->field('id,uid')->select();
        }else{
            $data =  self::name(self::$table1)->wherein('id',$uids)->where('oid',2)->field('id,uid')->select();
        }


        if($data ==null){ return '';}
        foreach($data as $key=>$value){
            $pic = self::name('app_member')->where('id',$value['uid'])->field('pic,coach_nickname')->find();
            if(!$pic['pic']){
                $pic['pic']="http://jiaolian.wisecareer.org/public/img/user-image.png";
            }

            if(!$pic['coach_nickname']){
                $pic['coach_nickname']="匿名";
            }
            $data[$key]['myInfo'] =$pic;

            $data[$key]['jifen'] = AppJf::get_jf($value['id']);
            $data[$key]['evalu_num']= AppUserEvaluate::where('uid',$value['id'])->count();
            $data[$key]['atten_num']=self::name('app_user_like')->where(['uid'=>$value['id'],'attention'=>1])->count();
            $data[$key]['recom_num']=self::name('app_user_like')->where(['uid'=>$value['id'],'recommend'=>1])->count();
            //现场教学
            $data[$key]['coachenter']=self::name('experience_coachenter')->where('uid',$value['id'])->count();
            $data[$key]['coach_like']=self::name('app_user_coach_like')->where('U_uid',$value['id'])->count();
            $data[$key]['has_coach_like']=self::name('app_user_coach_like')->where(['U_uid'=>$value['id'],'uid'=>$userid])->count();


            $atten = AppUserEvaluate::has_user_attenrecom($value['id'],$userid);
            if($atten){
                $data[$key]['attention'] = $atten['attention'];
                // $data['recommend'] = $atten['recommend'];
            }else{
                $data[$key]['attention'] = 0;
                // $data['recommend'] =0;
            }
            $label = self::Label($value['id']);
            if($label){
                $data[$key]['label']  =array_column($label,'tpname');
            }else{
                $data[$key]['label']  = ' ' ;
            }

            $zhiye = Anli_search::get_user_search($value['id']);
            $data[$key]['zhiye'] = $zhiye?$zhiye:'暂无';
            $grades_new = AppJf::grades($value['id']);  //获取用户最新级别
            //级别
            $data[$key]['grade']  = grade($grades_new);
        }
        $datas = array_sort($data,'jifen','desc');
        $datas = array_slice($datas, 0, 3);

        return $datas;
    }

    public static function coach_info($uids)
    {
        $data = self::name(self::$table1)->where('oid',2)->whereIn('id',$uids)->field('id,uid')->select();
        foreach($data as $key=>$value){
            $pic = self::name('app_member')->where('id',$value['uid'])->field('pic,coach_nickname')->find();
            if(!$pic['pic']){
                $pic['pic']="http://jiaolian.wisecareer.org/public/img/user-image.png";
            }
            if(!$pic['coach_nickname']){
                $pic['coach_nickname']="匿名";
            }

            $data[$key]['pic']  = $pic['pic'];
            $data[$key]['coach_nickname']  = $pic['coach_nickname'];

            //$data[$key]['zhiye']=implode(',', $this->select('app_user_jingli','zhiye',['uid'=>$value['id']]));
            $grades_new = AppJf::grades($value['id']);  //获取用户最新级别
            //级别
            $data[$key]['grade']  = grade($grades_new);
            $label = self::Label($value['id']);
            if($label){
                $data[$key]['label']  =  array_column($label,'tpname')  ;
            }else{
                $data[$key]['label']  = ' ' ;
            }

        }

        return $data;
    }

    /**
     * @param $uids
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function dynamcoachlist($uids)
    {
        $data = self::name(self::$table1)->where('oid',2)->whereIn('id',$uids)->field('id,uid')->select();
        if($data ==null){
            return '';
        }
        foreach($data as $key=>$value){
            $pic = self::name('app_member')->where('id',$value['uid'])->field('pic,user_name,coach_nickname')->find();
            if(!$pic['pic']){
                $pic['pic']="http://jiaolian.wisecareer.org/public/img/user-image.png";
            }
            if(!$pic['coach_nickname']){
                $pic['coach_nickname']="匿名";
            }

            $data[$key]['pic']  = $pic['pic'];
            $data[$key]['user_name']  = $pic['user_name'];
            $data[$key]['coach_nickname']  = $pic['coach_nickname'];
        }

        return $data;
    }

    /**
     * 教练列表
     * @param $uids
     * @param $userid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function coach_list($uids, $userid){
        if(!$uids){
            return false;
        }

        $data =self::name('center_user')->where('oid',2)->whereIn('id',$uids)->field('id,uid')->select();
        foreach($data as $key=>$value){
            $pic =self::name('app_member')->where('id',$value['uid'])->field('pic,coach_nickname')->find();
            if(!$pic['pic']){
                $pic['pic']="http://jiaolian.wisecareer.org/public/img/user-image.png";
            }

            if(!$pic['coach_nickname']){
                $pic['coach_nickname']="匿名";
            }
            $data[$key]['myInfo'] =$pic;

            $data[$key]['jifen'] = AppJf::get_jf($value['id']);
            //$data[$key]['evalu_num']=$this->count($this->table1,['uid'=>$value['id']]);
            $data[$key]['atten_num'] =self::name('app_user_like')->where('uid',$value['id'])->where('attention',1)->count();
            $data[$key]['has'] = self::name('app_user_like')->where(['uid'=>$value['id'],'userid'=>$userid,'attention'=>1])->find()==null?0:1;
            //$data[$key]['recom_num']=$this->count('app_user_like',['AND'=>['uid'=>$value['id'],'recommend'=>1]]);
            //现场教学
            //$data[$key]['coachenter']=$this->count("experience_coachenter",['AND'=>['uid'=>$value['id']]]);
            //$data[$key]['coach_like']=$this->count("app_user_coach_like",['AND'=>['U_uid'=>$value['id']]]);
            //$data[$key]['has_coach_like']=$this->has("app_user_coach_like",['AND'=>['U_uid'=>$value['id'],'uid'=>$uid]]);
//            $zhiyes = self::name('app_user_jingli')->where('uid',$value['id'])->column('zhiye');
//            if($zhiyes !=null){
//                $data[$key]['zhiye']= implode(',',$zhiyes);
//            }

            $zhiye = Anli_search::get_user_search($value['id']);
            $data[$key]['zhiye'] = $zhiye?$zhiye:'暂无';

            $atten = AppUserEvaluate::has_user_attenrecom($value['id'],$userid);
            if($atten){
                $data[$key]['attention'] = $atten['attention'];
               // $data['recommend'] = $atten['recommend'];
            }else{
                $data[$key]['attention'] = 0;
               // $data['recommend'] =0;
            }


            $grades_new = AppJf::grades($value['id']);  //获取用户最新级别
            //级别
            $data[$key]['grade']  = grade($grades_new);
            $label = self::Label($value['id']);
            if($label){
                $data[$key]['label']  =  array_column($label,'tpname')  ;
            }else{
                $data[$key]['label']  = ' ' ;
            }
        }
        $datas = array_sort($data,'jifen','desc');
        $datas = array_slice($datas, 0, 3);
        return $datas;
    }

    //关注我的教练
    //
    public static function acten_me($userid)
    {
        return self::name('app_user_recommend')->where(['userid'=>$userid,'attention'=>1])->column('uid');
    }

    //推荐过我
    public static function recom_me($userid)
    {
        return self::name('app_user_recom_evaluate')->where(['userid'=>$userid])->column('uid');
    }
    public static function eval_me($userid)
    {
        return AppUserEvaluate::where(['userid'=>$userid])->column('uid');
    }
    public static function scene_me($userid)
    {
        return self::name('app_user_site_base')->where('userid',$userid)->column('uid');
    }

    /**
     * 按名字搜索
     */
    public static function serarch_name($name){
        $uids = self::name('app_member')->whereLike('user_name',"%{$name}%")->column('id');

        if($uids ==null){
            return null;
        }
        return self::name(self::$table1)->whereIn('uid',$uids)->where("oid",2)->column('id');
    }

    /**
     * 按职业
     */
    public static function serarch_job($job){
        $title= self::name('anli_search')->where('id',$job)->value('titlename');

        $sql ="SELECT uid FROM (SELECT * FROM (SELECT * FROM `kw_app_user_search` ORDER BY id desc ) t GROUP BY uid ) t1 WHERE zhiye ='$title'";

        $data = self::query($sql);
        if(empty($data)){
            return null;
        }
        return array_column($data,'uid');
    }

    /**
     * 按 地区
     */
    public static function serarch_city($city){
        $uids = self::name('app_member')->where('scid',$city)->column('id');
        if($uids ==null){
            return null;
        }
        return self::name(self::$table1)->whereIn('uid',$uids)->where("oid",2)->column('id');
    }




    /**
     * 标签
     * @param $uid
     * @return array|null|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function Label($uid){
        $data1 = self::name(self::$table1)->where('id',$uid)->field('uid,oid')->find();
        if($data1['oid'] == 2){
            $data =self::name('app_tag_user')->where('uid',$data1['uid'])->where('sh',1)->field('id,txid,aid,content')->select()->toArray();
        }else{
            return null;
        }
//elseif($data['oid'] == 1){
//            $data = $this->select('school_versions',["id","txid","aid"],['sid'=>$_SESSION['myInfo']['classid']]);
//        }


        $anlidata = Caches::HangYeClass();
        $lxdata = Caches::LxClass();
        $vtxdata = Caches::ZhuanTiClass();

        $anlitpids = array_column($anlidata,'tpid');
        $anlitpnames= array_column($anlidata,'tpname');
        $anlidatas = array_combine($anlitpids,$anlitpnames);

        $lxids = array_column($lxdata,'id');
        $lxtpnames = array_column($lxdata,'tpname');
        $lxdatas = array_combine($lxids,$lxtpnames);

        $vlxids = array_column($vtxdata,'id');
        $vlxtpnames = array_column($vtxdata,'tpname');
        $vtxdatas = array_combine($vlxids,$vlxtpnames);

        foreach($data as $key=>$value){
            if($value['txid'] == 'hangye'){
                $data[$key]['tpname'] =$anlidatas[$value['aid']];
            }elseif($value['txid'] == 'lx'){
                $data[$key]['tpname'] =$lxdatas[$value['aid']];
            }elseif($value['txid'] == 'zhuanti'){
                $data[$key]['tpname']=$vtxdatas[$value['aid']];
            }
        }
        return $data;

    }


    //用户经历
    public static  function getjingli($where){
        $data =self::name('app_user_jingli')->where($where)->order('datetime','desc')->select();
        foreach($data as $key=>$value){
            $data[$key]['tpname'] = Anli_class::where('tpid',$value['hangye'])->value('tpname');
            $data[$key]['zhin'] = Lx_class::where('id',$value['zhineng'])->value('tpname');
            $data[$key]['zjname'] = self::name('app_zhiji')->where('id',$value['zhiji'])->value('zjname');

        }
        return $data;
    }

    /**
     *所属行业教练
     */
    public static function belong_hang($hangye){
        return self::name('app_user_jingli')->where('hangye',$hangye)->column('uid');
    }

    /**
     *所属只能教练
     */
    public static function belong_lx($lx){
        return self::name('app_user_jingli')->where('zhineng',$lx)->column('uid');
    }


}

