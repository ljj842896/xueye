<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;
/**
 * Class 我的数据
 * @package app\common\model
 */

class Cares extends Model
{
    //省多少人
    private static function user_nums($scid =''){
        if($scid){
            return Myuser::where('scid',$scid)->count();
        }else{
            return Myuser::where('scid','neq','')->count();
        }
    }

    /**比例
     * @param $users
     * @param string $scid
     * @return float
     */
    public static function proportion($users, $scid =''){
        $nums =self::user_nums($scid);
        $users  = count($users);
        return @round(($users/$nums)*100,2);
    }

    /**学科优势相同的人
     * @param $uid
     * @param string $scid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function subject_advantage_users($uid, $scid=''){
        $EduWenSrUid  = Caches::EduWenSrUid($uid);
        if(empty($EduWenSrUid)){
            return false;
        }
        $list21 = $EduWenSrUid['list21'];
        if($scid){
            $users = self::name('edu_wen_sr')->alias('t1')->leftJoin('myuser t2','t1.uid = t2.id')->where('t2.scid',$scid)->where('t1.list21',$list21)->column('t1.uid');
        }else{
            $users = self::name('edu_wen_sr')->alias('t1')->leftJoin('myuser t2','t1.uid = t2.id')->where('t2.scid','neq','')->where('t1.list21',$list21)->column('t1.uid');
        }
        return $users;
    }

    /**的选科组合相同的人
     * @param $uid
     * @param string $scid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function subject_combination_users($uid, $scid=''){

        $myinfo =   Caches::MyuserInfoFiled($uid);
        $subject = $myinfo['subject'];
        if($scid){
            $users = self::name('myuser')->where('subject',$subject)->where('scid',$scid)->column('id');
        }else{
            $users = self::name('myuser')->where('subject',$subject)->where('scid','neq','')->column('id');
        }
        return $users;
    }

    /**学你关注同样专业的
     * @param $uid
     * @param string $scid
     * @return array|int|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function action_major_users($uid, $scid=''){
        $eid  = !empty($_GET['eid'])?$_GET['eid']:0;
        if(!$eid){
            return 0;
        }
        if($scid){
            $users = self::name('user_edu_sc')->alias('t1')->leftJoin('myuser t2','t1.uid = t2.id')->where('t1.eid',$eid)->where('t2.scid',$scid)->column('t2.id');
        }else{
            $users = self::name('user_edu_sc')->alias('t1')->leftJoin('myuser t2','t1.uid = t2.id')->where('t1.eid',$eid)->where('t2.scid','neq','')->column('t2.id');
        }
        return $users;
    }

    /**学你性格类型相同的人
     * @param $uid
     * @param string $scid
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function character_type_users($uid, $scid=''){
        $data = self::name("st_ceping")->where('uid',$uid)->where('intE','>',0)->where('intI','>',0)->field('val1,val2,val3,val4')->order('datetime','desc')->find();
        if(!$data){
            return false;
        }
        if($scid){
            $users = self::name('st_ceping')->alias('t1')->leftJoin('myuser t2','t1.uid = t2.id')->where('val1',$data['val1'])->where("val2",$data['val2'])->where("val3",$data['val3'])->where("val4",$data['val4'])->where('t2.scid',$scid)->column('t2.id');
        }else{
            $users = self::name('st_ceping')->alias('t1')->leftJoin('myuser t2','t1.uid = t2.id')->where('val1',$data['val1'])->where("val2",$data['val2'])->where("val3",$data['val3'])->where("val4",$data['val4'])->where('t2.scid','neq','')->column('t2.id');
        }
        return $users;
    }

    /**学科订阅分布
     * @param $users
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function subject_subscription($users){
        $users = implode(',',$users);
        $sql = "SELECT count(id) as num,l.tpname FROM `kw_subject_attention` a left join kw_edu_lilun l  on a.classid = l.tpid  WHERE uid in ({$users}) GROUP BY classid";
        $data = self::name($sql);
        $num = '' ;
        $tpname = '' ;
        foreach ($data as $key => $value) {
            $num .= $value['num'].',';
            $tpname .= '"'.$value['tpname'].'",';
        }
        $num = rtrim($num,',');
        $tpname  = rtrim($tpname,',');
        $result['num']  = $num;
        $result['tpname']  = $tpname;
        return $result;
    }


    /**行业偏向分布
     * @param $users
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function industry_bias($users){
        $users = implode(',',$users);
        $sql1 = "SELECT  b.alclass FROM kw_myuser m RIGHT JOIN kw_user_edu_sc a ON m.id=a.uid RIGHT JOIN kw_edu_info b ON a.eid= b.id where  m.id in ({$users}) and  alclass not like '%行业通用%' ";
        $data = self::query($sql1);
        $strtrades = '';
        foreach ($data as $key => $value) {
            $strtrades .= $value['alclass']."、";
        }

        $pieces = array_filter(explode("、",$strtrades));
        $strhaoma= array_unique($pieces);
        $datas = array();
        $tpnubser = 0;
        $tongyongs =self::Edu_trades_sum("行业通用");
        //总收藏数
        foreach ($strhaoma as $v=>$a)
        {
            $ab1 =self::Edu_trades_sum($a);
            $tpnubser = $ab1+$tpnubser;
        }

        $tpnameidarr = '';
        $tpnameber= '';
        foreach ($strhaoma as $v=>$a)
        {
            $tpnameidarr .= "'".$a."'".",";
            $ab1 =number_format((self::Edu_trades_sum($a)+$tongyongs)/($tpnubser+$tongyongs),3)*100;
            $tpnameber .= $ab1.",";
        }

        $datas['tpname'] = rtrim($tpnameidarr,","); //行业名称
        $datas['tpids'] = rtrim($tpnameber,","); //行业名称
        //
        return $datas;
    }


    /**
     * @param string $arrtyprr
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function Edu_trades_sum($arrtyprr=''){
        $wheres = "";

        if($arrtyprr!=""){
            $wheres .= "  b.alclass like '%".$arrtyprr."%'";
        }
        $sql1 = "SELECT count(m.id) as edusum  FROM kw_myuser m RIGHT JOIN kw_user_edu_sc a ON m.id=a.uid RIGHT JOIN kw_edu_info b ON a.eid= b.id where  ".$wheres." ";
        $data =self::query($sql1);
        return $data[0]['edusum'];
    }

    /**人气专业
     * @param $users
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function get_professional($users){
        $users= implode(',',$users);
        $lx = array(
            array('id' => 1,'name' => '人文科学'),
            array('id' => 2,'name' => '社会科学'),
            array('id' => 3,'name' => '理论科学'),
            array('id' => 4,'name' => '工程技术科学'));
        foreach ($lx as $key => $value) {
            $sql = "SELECT count(*) as sum  , b.titlename, a.eid FROM kw_myuser m RIGHT JOIN kw_user_edu_sc a ON m.id=a.uid RIGHT JOIN kw_edu_info b ON a.eid= b.id  where  m.id in ({$users}) and b.lx={$value['id']} GROUP BY eid order BY sum desc LIMIT 3";
            $res = self::query($sql);
            if($res){
                $lx[$key]['data'] =$res;
            }else{
                unset($lx[$key]);
            }
        }

        return $lx;
    }
    /**四大专业类型中的分布
     * @param $users
     * @param string $type
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function Edu_edu_TpidSum($users, $type=''){
        $wheres = '';

        if($type){
            $wheres .=" and  b.lx = {$type}" ;
        }
        $sql1 = "SELECT count(*) as sum ,  b.lx, m.id ,m.viptype, b.lilun FROM kw_myuser m RIGHT JOIN kw_user_edu_sc a ON m.id=a.uid RIGHT JOIN kw_edu_info b ON a.eid= b.id where m.id in({$users}) ".$wheres." " ;
        $data = self::query($sql1);
        return $data[0]['sum'];

    }

    /**学科
     * @param $users
     * @return string
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function Edu_edu_Tpid($users){
        $users = implode(',',$users);
        $data = array(
            1=>array('id' => 1,'name' => '人文科学'),
            2=>array('id' => 2,'name' => '社会科学'),
            3=>array('id' => 3,'name' => '理论科学'),
            4=>array('id' => 4,'name' => '工程技术科学')
        );

        $strtrades = '';
        $sumtype = self::Edu_edu_TpidSum($users); //总收藏数
        $datas  = '';
        foreach ($data as $key => $value) {
            $datas .= " ['{$value['name']}', ".@round((self::Edu_edu_TpidSum($users,$value['id'])/$sumtype)*100,2)."],";
        }
        $datas = rtrim($datas,",") ;
        return $datas;
    }


    //偏科
    public static function partial_section($users){
        $users = implode(',',$users);
        $sqlnums = " SELECT count(*) as num  FROM kw_myuser m  LEFT  JOIN kw_edu_gk_class b ON m.subject = b.tpid  LEFT  JOIN kw_class_class c ON b.classid = c.id  where   m.id in ({$users})  and c.id !=''   ";
        $datanums = self::query($sqlnums);

        $sql23="SELECT count(*) as num ,c.tpname FROM kw_myuser m  LEFT  JOIN kw_edu_gk_class b ON m.subject = b.tpid  LEFT  JOIN kw_class_class c ON b.classid = c.id  where  m.id in ({$users})  and c.id =23 ";
        $data1 = self::query($sql23);

        $sql24="SELECT count(*) as num ,c.tpname FROM kw_myuser m  LEFT  JOIN kw_edu_gk_class b ON m.subject = b.tpid  LEFT  JOIN kw_class_class c ON b.classid = c.id  where  m.id in ({$users})  and c.id =24 ";
        $data2 = self::query($sql24);

        $sql25="SELECT count(*) as num ,c.tpname FROM kw_myuser m  LEFT  JOIN kw_edu_gk_class b ON m.subject = b.tpid  LEFT  JOIN kw_class_class c ON b.classid = c.id  where  m.id in ({$users})  and c.id =25 ";
        $data3 = self::query($sql25);

        $sql26="SELECT count(*) as num ,c.tpname FROM kw_myuser m  LEFT  JOIN kw_edu_gk_class b ON m.subject = b.tpid  LEFT  JOIN kw_class_class c ON b.classid = c.id  where  m.id in ({$users})  and c.id =26 ";
        $data4 = self::query($sql26);

        $data1['num'] =@round(($data1[0]['num']/$datanums[0]['num'])*100,2);
        $data2['num'] =@round(($data2[0]['num']/$datanums[0]['num'])*100,2);
        $data3['num'] =@round(($data3[0]['num']/$datanums[0]['num'])*100,2);
        $data4['num'] =@round(($data4[0]['num']/$datanums[0]['num'])*100,2);

        $datas = " ['{$data1[0]['tpname']}',{$data1['num']}],";
        $datas .= " ['{$data2[0]['tpname']}',{$data2['num']}],";
        $datas .= " ['{$data3[0]['tpname']}',{$data3['num']}],";
        $datas .= " ['{$data4[0]['tpname']}',{$data4['num']}],";

        $datas = rtrim($datas,',');
        return $datas;

    }


    //选科组合
    public static function edu_gk_class($usres){
        $users = implode(',',$usres);

        $sql1 = "SELECT count(id) as num, `subject`  FROM `kw_myuser` where id in ({$users})  and subject>0  GROUP BY subject";
        $data = self::query($sql1);
        $strTpname  ='';
        $strsubject  ='';
        $datas = array();
        $EduGkClass = Caches::EduGkClass();
        foreach ($data as $key => $value) {
            $datas['tpname'] = $strTpname .= "'".str_replace("#","+",$EduGkClass[$value['subject']])."',";
            $datas['subject'] =  $strsubject .= $value['num'].",";
        }
        return $datas;
    }

    //优势学科分布
    public static function discipline_distribution($usres){
        $usres = implode(',',$usres);
        $sql = "SELECT count(id) as num,l.tpname FROM kw_edu_wen_sr s left join kw_edu_lilun l on l.tpid = s.list21 where uid in({$usres})  and list21 != '' GROUP BY list21";

        $data = self::query($sql);
        $num = '' ;
        $tpname = '';

        foreach ($data as $key => $value) {
            $num  .= $value['num'].',';
            $tpname  .= '"'.$value['tpname'].'",';
        }

        $result['num']  = rtrim($num,',');
        $result['tpname'] = rtrim($tpname,',');

        return $result;
    }

}



