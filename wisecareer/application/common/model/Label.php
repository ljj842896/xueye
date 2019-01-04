<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   标签
 * @package app\common\model
 */
class Label extends Model
{
    
    /**
     * 标签
     * @return mixed
     */
    public static function label(){
        $data['hangye']= Caches::HangYeClass();
        $data['lx']= Caches::LxClass();
        $data['zhuanti']= Caches::ZhuanTiClass();
        return $data;
    }
    //新增计划
    public static function add_plan($uid,$array){
        $time = time();
        $tpid =self::name('user_e_plan')->insertGetId(['uid'=>$uid,'topicnum'=>0,'comp'=>0,'datetime'=>$time]);
        if($tpid){
            foreach ($array as $key => $value) {
                self::name('user_e_plan_zi')->insert(['type'=>$value[0] ,'language'=>$value[2],'num'=>0,'complete'=>0,'tid'=>$value[1],'tpid'=>$tpid,'uid'=>$uid]);
            }
            return true;
        }
        return false;
    }

    //放弃计划
    public static function give_up_plan($id,$uid)
    {
        return self::name('user_e_plan')->where('id',$id)->where('uid',$uid)->data(['comp'=>2])->update();
    }
    /**
     *     修改子计划
     * @param $uid
     * @param $array
     * @return bool
     */
    public static function update_plan_zi($uid, $array){
        $tpid = self::name('user_e_plan')->where('uid',$uid)->where('comp',0)->value('id');
        if($tpid){
            self::name('user_e_plan_zi')->where('tpid',$tpid)->where('uid',$uid)->delete();
            foreach ($array as $key => $value) {
                self::name('user_e_plan_zi')->data(['type'=>$value[0],'num'=>0,'complete'=>0,'language'=>$value[2],'tid'=>$value[1],'tpid'=>$tpid,'uid'=>$uid])->insert();
            }
            return true;
        }
         return false;
    }

    /**判断是否可以修改计划
     * @param $uid
     * @return array|bool|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function has_update_plan($uid)
    {
        $has = self::name('user_e_plan')->where('uid',$uid)->where('comp',0)->find();
        if($has==null){
            return false;
        }
        return $has;
    }

    /**修改计划
     * @param $uid
     * @param $array
     * @return bool
     */
    public static function update_plan($uid, $array){

        // foreach ($array['types'] as $key => $value) {
        // 	$test = explode('_',$key);
        // 	$this->update($this->table2,['num'=>intval($value)],['AND'=>['tpid'=>$array['tpid'],'type'=>$test[0],'tid'=>$test[1]]]);
        // 	$nums = $nums+intval($value);
        // }
        $nums =intval($array['nums']) ;
        $topicnum = $nums*intval($array['every']);
        $data = self::name('user_e_plan')->where('uid',$uid)->where('id',$array['tpid'])->data(['starttime'=>strtotime($array['starttime']),'endtime'=>strtotime($array['endtime']),'setting'=>$array['setting'],'nums'=>$nums,'topicnum'=>$topicnum,'comp'=>1,'every'=>$array['every']])->update();
        if($data){
            return true;
        }else{
            return false;
        }

    }

    /**检查是否存在为完成计划
     * @param $uid
     * @return \think\db\Query
     */
    public static function plan_has_no($uid){
        return self::name('user_e_plan')->where('uid',$uid)->where('comp',0)->find();
    }
    /**检查计划是否存在
     * @param $uid
     * @return \think\db\Query
     */
    public static function plan_has($uid){
        $time = time();
        $data = self::name('user_e_plan')->where('uid',$uid)->where('comp',1)->where(function ($query) use($time) {
            $query->where('endtime', 0)
                ->whereOr('endtime', '>', $time);
        })->find();

        if($data==null) return false;
        return $data;
         //return $this->has($this->table1,['AND'=>['uid'=>$uid,'comp'=>'OR'=>['endtime[>]'=>$this->time,'endtime'=>0]1,]]);
    }

    //
    //// 获取未完成计划标签
    public static function get_plan_label_comp($uid){
        $labeldata = self::name('user_e_plan')->where('uid',$uid)->where('comp',0)->field('id,endtime')->order('datetime','desc')->find();

        $array= self::name('user_e_plan_zi')->where('tpid',$labeldata['id'])->field('type,tid')->select();
        $data = self::label_tpname($array);
        $result['tpid'] = $labeldata['id'];
        $result['label'] = $data;
        return $result;
    }
    // 获取计划标签
    public static function get_plan_label($uid){
        //获取 未完成、完成的计划
        $labeldata=self::name('user_e_plan')->where('uid',$uid)->where('comp','neq',2)->order('datetime','desc')->field('id,endtime')->find();

        if(!$labeldata){
            return false;
        }

        $time = time();
        //   过期
        $result['expire']  = 0;
        if($time > $labeldata['endtime']){
            $result['endtime'] = '';
        }else{
            $result['endtime'] = date('Y-m-d', $labeldata['endtime']);
        }

        $result['label']='';

        if($labeldata['endtime'] <= $time){
            $result['expire']  = 1;
            return $result;
        }
        //获取通道
        $array = self::name('user_e_plan_zi')->where('tpid',$labeldata['id'])->field('language,type,tid')->select();
        $data =self::label_tpname($array);
        $result['tpid'] = $labeldata['id'];


        $result['label'] = $data;

        return $result;
    }


    /**进度 百分比
     * @param $uid
     * @return float
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function plan_pro($uid){
        //总次数
        $data =self::name('user_e_plan')->where('uid',$uid)->where('comp','neq',2)->field('id,nums,comp')->order('datetime','desc')->find();

        if($data ==null){ return false;}
        // //已经体验了多少次
        $completes = self::name('user_e_plan_zi')->where('tpid',$data['id'])->sum('complete');
        //百分比
        $result= @round(($completes/$data['nums'])*100,2);
        if( $data['comp'] !=3 && $data['comp'] !=0  &&  $completes >= $data['nums']  ){
            self::name('user_e_plan')->where('id',$data['id'])->data(['comp'=>3])->update();
        }
        return $result;
    }

    //获取计划任务

    public static function get_plan($uid){
        $data = self::name('user_e_plan')->where('uid',$uid)->where('comp',1)->field('comp,nums,datetime,endtime,every,setting,starttime,topicnum,id')->order('datetime','desc')->find();
        if($data ==null) return false;
        $data['starttime']= $data['starttime'] ?date('Y-m-d',$data['starttime']):'';
        $data['endtime']= $data['endtime']?date('Y-m-d',$data['endtime']):'';
        $data['datetime']= $data['datetime']?date('Y-m-d',$data['datetime']):'';
        $array= self::name('user_e_plan_zi')->where('tpid',$data['id'])->field('type,tid,complete,num')->select();
        $completes=0;
        $nums=$data['nums'];
        foreach ($array as $key => $value) {
            $completes += $value['complete'];
            //$nums+=$value['num'];
        }
        $data['pro']= @round(($completes/$nums)*100,2);
        $data1 =self::label_tpname($array);
        $result['tpid'] = $data;
        $result['label'] = $data1;
        return $result;
    }



    // 标签格式化
    public static function label_tpname($array){
        if(!$array){
            return false;
        }
        $anlidata = Caches::HangYeClass();
        $lxdata = Caches::LxClass();
        $vtxdata = Caches::ZhuanTiClass();

        $anlitpids = array_column($anlidata,'tpid');
        $anlidatas = array_combine($anlitpids,$anlidata);

        $lxids = array_column($lxdata,'id');
        $lxdatas = array_combine($lxids,$lxdata);

        $vtxids = array_column($vtxdata,'id');
        $vtxdatas = array_combine($vtxids,$vtxdata);

        foreach ($array as $key => $value) {


            if($value['type'] == 'hangye'){
                $data = $anlidatas[$value['tid']];
            }elseif($value['type'] == 'lx'){
                $data = $lxdatas[$value['tid']];
            }elseif($value['type'] == 'zhuanti'){
                $data = $vtxdatas[$value['tid']];
            }

            $array[$key]['tpname']=$data['tpname'];
            //$array[$key]['language']=$data['language'];
            $array[$key]['pic']='http://www.wisecareer.org/terminals/'.$data['pic'];

        }

        return $array;

    }

    /**判断是否 可以生成题
     * @param $uid
     * @param $array
     * @return bool|null|static
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function plan_generate_has($uid, $array){
        $time = time();
        $data =  self::name('user_e_plan')->where([['uid','=',$uid],['comp','=',1],['starttime','<',$time],['endtime','>',$time]])->field('id,every,nums')->order('id','desc')->find();

        if($data ==null){
            return false;
        }
        $array =self::name('user_e_plan_zi')->where('tpid',$data['id'])->field('complete,num')->select();
        $completes=0;
        //$nums=0;
        foreach ($array as $key => $value) {
            $completes += $value['complete'];
            //$nums+=$value['num'];
        }
        if($completes < $data['nums']){
            return $data;
        }
        return false;
    }

    //通道数
    public static function labelnum($tpid,$uid){
        return self::name('user_e_plan_zi')->where('uid',$uid)->where('tpid',$tpid)->count();
    }

    //完成子计
    public static function plan_zi_comp($tid,$uid){
        $has = self::name('ty_user')->where(['uid'=>$uid,'userty'=>$tid,'huida'=>0])->find();
        if($has!=null){
            return false;
        }
        $data = self::name('user_ty')->where(['uid'=>$uid,'id'=>$tid])->field('hangye,valid,lx,ztid')->find();
        if($data ==null || $data['valid']){
            return false;
        }
        UserTy::where(['uid'=>$uid,'id'=>$tid])->update(['valid'=>1]);
        if($data['hangye']){
            self::name('user_e_plan_zi')->where(['uid'=>$uid,'type'=>'hangye','tid'=>$data['hangye']])-> setInc('complete');
        }elseif($data['lx']){
            self::name('user_e_plan_zi')->where(['uid'=>$uid,'type'=>'lx','tid'=>$data['lx']])-> setInc('complete');
        }elseif($data['ztid']){
            self::name('user_e_plan_zi')->where(['uid'=>$uid,'type'=>'zhuanti','tid'=>$data['ztid']])-> setInc('complete');
        }
        return true;
    }

    //子计划进度
    public static function plan_zi_pro($id,$nums)
    {
        # code...
        $array = self::name('user_e_plan_zi')->where('tpid',$id)->field('complete,num')->select();
        $completes=0;
        foreach ($array as $key => $value) {
            $completes += $value['complete'];
            //$nums+=$value['num'];
        }
        return @round(($completes/$nums)*100,2);
    }


    //计划日志
    public static function plan_log($uid,$page)
    {
        $data  = self::name('user_e_plan')->where([['uid','=',$uid],['comp','neq',0]])->order('datetime','desc')->limit($page,10)->select();
        if($data == null){
            return false;
        }
        $time = time();
        foreach ($data as $key => $value) {
            if($value['comp'] ==1 && $value['endtime'] > $time){
                unset($data[$key]);
                continue;
            }

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

            $data2 = self::name('user_e_plan_zi')->where('tpid',$value['id'])->field('type,tid')->select();
            $tpname = [];
            foreach ($data2 as $key2 => $value2) {
                if($value2['type'] == 'hangye'){
                    $tpname[] =$anlidatas[$value2['tid']];
                }elseif($value2['type'] == 'lx'){
                    $tpname[] =$lxdatas[$value2['tid']];
                }elseif($value2['type'] == 'zhuanti'){
                    $tpname[] =$vtxdatas[$value2['tid']];
                }
            }

            $data[$key]['tpname'] =implode(',', $tpname);
            $data[$key]['starttime'] =date('Y-m-d',$value['starttime']);
            $data[$key]['endtime'] =date('Y-m-d',$value['endtime']);
            if($value['comp'] ==2){
                $data[$key]['comp'] ='放弃';
            }elseif($value['comp'] ==3){
                $data[$key]['comp'] ='完成';
            }elseif($value['comp'] ==1){
                $data[$key]['comp'] ='未完成';
            }

            $data[$key]['pro'] = self::plan_zi_pro($value['id'],$value['nums']);
        }
        return $data;
    }
}

