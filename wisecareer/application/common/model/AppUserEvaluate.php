<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class AppUserEvaluate extends Model
{
    //案例详情
    public static function add($array){
        $array['datetime']= time();
        return self::insertGetId($array);
    }
    //评分
    public static function pingdefen($array){
        $array['datetime']= time();
        return self::name('ty_user_defen')->insertGetId($array);
    }
    //搜索
    public static function search($id){
        return self::where('taid',$id)->find();
    }

    //案例评价数
    public static function evaluatenum($class,$where){

        if($class['oid'] ==1){
            $schoolname = $class['schoolname'];
            $sql = "SELECT count(a.id) as num  FROM  kw_app_user_evaluate  a LEFT JOIN kw_myuser m  on m.id = a.userid where   m.schoolname = '$schoolname' " .$where;
            $data = self::query($sql);
            return $data['num'];
        }elseif($class['oid'] ==2){
            return self::where($where)->count();
        }
    }
    /**学员 案例 评价情况
     * @param $userid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function user_evaluate($userid){
        $data = AppUserEvaluate::alias('el')->leftJoin('anli','el.aid = anli.id')
            ->where('el.userid',$userid)->field('el.content,el.recom,el.retime,el.datetime,el.uid,anli.titlename')->select();
        foreach($data as $key=> $value){
            $result = [];
            $user_name = self::name('center_user')->alias('cu')->leftJoin('app_member am','cu.uid = am.id')
                ->where('cu.id',$value['uid'])->value('am.user_name');
            $data[$key]['user_name']  = $user_name;
        }
        return $data;
    }

    //学员点赞
    public static function user_recom($uid){
        $data['atten_nun']= self::name('app_user_like')->where(['uid'=>$uid,'attention'=>1])->count();
        $data['recom_num']= self::name('app_user_like')->where(['uid'=>$uid,'recommend'=>1])->count();
        return $data;
    }


    //学院点赞 关注教练
    public static function add_attenrecom($array)
    {
        $time = time();
        $has = self::name('app_user_like')->where(['uid'=>$array['uid'],'userid'=>$array['userid']])->field('attention,recommend')->find();
        if($has != null){
            if(!empty($array['attention'])){
                $where['attention'] =$has['attention'] ? 0: 1;
                $where['attention_time'] = $time;
            }elseif(!empty($array['recommend'])){
                $where['recommend'] =$has['recommend'] ? 0: 1;
                $where['recommend_time'] = $time;
            }
            $data = self::name('app_user_like')->where(['uid'=>$array['uid'],'userid'=>$array['userid']])->update($where);
        }else{
            if(!empty($array['attention'])){
                $array['attention_time'] = $time;
            }elseif(!empty($array['recommend'])){
                $array['recommend_time'] = $time;
            }
            $data = self::name('app_user_like')->insert($array);
        }
        return $data;
    }


    /**学员关注教练列表
     * @param $userid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_user_like_atten($userid)
    {
        $data = self::name('app_user_like')->where(['attention'=>1,'userid'=>$userid])->column('uid');
        if($data == null){
            return false;
        }


        $result =[];
        foreach ($data as $key => $value) {
            $s  = Coach::coach_info($value);
            $result[$key]['pic']=  $s[0]['pic'];
            $result[$key]['user_name']=   $s[0]['coach_nickname'] ;
            $result[$key]['phpto']= self::name('app_user_pic')->where('uid',$value)->count();
            $result[$key]['hangye']= self::name('hangye_dynamic')->where('uid',$value)->count();
            $num1 = self::name('anli_copy')->where(['uid'=>$value,'state'=>3])->count();
            $num2 = self::name('v_ticlass_copy')->where(['uid'=>$value,'state'=>3])->count();
            $result[$key]['anli']= $num1+$num2;
            $result[$key]['id']=  $value;
        }
        return $result;

    }

    /**是否点赞 关注
     * @param $uid
     * @param $userid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function has_user_attenrecom($uid, $userid)
    {
        return self::name('app_user_like')->where(['uid'=>$uid,'userid'=>$userid])->field('attention,recommend')->find();
    }

}

