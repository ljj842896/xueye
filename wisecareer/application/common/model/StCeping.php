<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class StCeping extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * 是否有未完成的 测评题
     * @param $uid
     * @param $cpid
     * @return \think\db\Query
     */
    public static function has_ceping_sel($uid, $cpid)
    {
        return self::name('st_ceping_udname')->where('uid',$uid)->where('cpid',$cpid)->where('daima',0)->count();
    }

    /**处理测评题
     * @param $data
     * @param $uid
     * @param $cpid
     * @return bool
     */
    public static function handle_ceping($data, $uid, $cpid)
    {
        if(! self::whereid($cpid)->where('uid',$uid)->where('ots',0)->find() ){
            return false;
        }
        foreach ($data as $key => $value) {
            if(count($value)<4){
                continue;
            }
            $array = [];
            $array['tnid'] = $value[2];
            $array['daima'] = $value[3];
            self::name('st_ceping_udname')->where('uid',$uid)->where('cpid',$cpid)->where('listorder',$value[0])->where('tid',$value[1])->data($array)->update();
        }
        return true;

    }

    /**
     * 生成测评
     * @param $uid
     * @return bool|int|string
     */
    public static function generate_ceping($uid)
    {
        $has = self::where('uid',$uid)->find();
        if($has){
            return false;
        }
        $insertdata=['uid'=>$uid,'datetime'=>time()];
        $insert_id  = self::insertGetId($insertdata);
        if(!$insert_id){
            return false;
        }
        $numbers = range (1,44);
        //shuffle 将数组顺序随即打乱
        shuffle ($numbers);
        //array_slice 取该数组中的某一段
        $no=44;
        $result = array_slice($numbers,0,$no);
        for ($i=0;$i<$no;$i++){
            $it = $i+1;
            $array[$i]['uid'] =$uid;
            $array[$i]['cpid'] =$insert_id;
            $array[$i]['tid'] =$result[$i];
            $array[$i]['listorder'] =$it;
        }


        return self::name('st_ceping_udname')->data($array)->insertAll();

    }

    //调取测评题
    public static function sel_character($uid)
    {
        $cpid = StCeping::where('uid',$uid)->where('ots',0)->value('id');

        $data = self::name('st_ceping_udname')->field('listorder,tid')->where('uid',$uid)->where('cpid',$cpid)->where('daima',0)->order('listorder','asc')->limit(4)->select();
        if($data && gettype($data)=='object'){
            $data = $data->toArray();
        }
        if(!$data){
            return false;
        }
        $zong = 44;
        $ye = ceil($data[0]['listorder']/4);
        foreach ($data as $key => $value) {
            $sql = "SELECT `id`,`tid`,`cid`,`leta`,`tpname`,`daa`,`letb`,`dab`,`qingj` FROM kw_cp_id where tid = '1' and cid = '".$value['tid']."'  order by  rand() limit 1";
            $datas = self::query($sql);
            $data[$key]['list'] = self::query($sql)[0];
        }

        $result['data'] = $data;
        $result['zong'] = $zong;
        $result['ye'] = $ye;
        $result['cpid'] = $cpid;
        return $result;
        # code..,.
    }

    public static function st_save_sub($uid, $cpid,$subject){
        return self::where('id',$cpid)->where('uid',$uid)->update(['subject'=>$subject]);
    }

    /**
     * 处理完成
     * @param $uid
     * @param $cpid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function st_save($uid, $cpid)
    {
        $sql ="SELECT  count(id) as num ,daima FROM kw_st_ceping_udname where  uid = {$uid} and cpid ={$cpid} group by daima  order by tid asc ";
        $data = self::query($sql);
        $gkkeys=array_column($data, 'daima');
        $gkvalues=array_column($data, 'num');
        $datalist  =array_combine($gkkeys, $gkvalues);
        if( empty($datalist['E'])   ){
            $val1 = "I";
        }elseif( empty($datalist['I'] ) ){
            $val1 = "E";
        }elseif(  $datalist['E']  > $datalist['I'] ) {
            $val1 = "E";
        }else{
            $val1 = "I";
        }

        if(empty($datalist['S'])   ){
            $val2 = "N";
        }elseif(empty($datalist['N'])  ){
            $val2 = "S";
        }elseif( $datalist['S'] > $datalist['N']){
            $val2 = "S";
        }else{
            $val2 = "N";
        }

        if(empty($datalist['F'])   ){
            $val3 = "T";
        }elseif(empty($datalist['T'])  ){
            $val3 = "F";
        }elseif($datalist['F'] > $datalist['T']){
            $val3 = "F";
        }else{
            $val3 = "T";
        }

        if(empty($datalist['J'])   ){
            $val4 = "P";
        }elseif(empty($datalist['P'])  ){
            $val4 = "J";
        }elseif($datalist['P'] > $datalist['J']){
            $val4 = "P";
        }else{
            $val4 = "J";
        }



        $intE = empty($datalist['E'])?0:$datalist['E'];
        $intI = empty($datalist['I'])?0:$datalist['I'];
        $intS = empty($datalist['S'])?0:$datalist['S'];
        $intN = empty($datalist['N'])?0:$datalist['N'];
        $intF = empty($datalist['F'])?0:$datalist['F'];
        $intP = empty($datalist['P'])?0:$datalist['P'];
        $intT = empty($datalist['T'])?0:$datalist['T'];
        $intJ = empty($datalist['J'])?0:$datalist['J'];

        $res = self::execute("UPDATE kw_st_ceping SET `intE`={$intE},`intI`={$intI},`intS`={$intS},`intN`={$intN},`intF`={$intF},`intT`={$intT},`intP`={$intP},`intJ`={$intJ},`val1`='$val1',`val2`='$val2',`val3`='$val3',`val4`='$val4',`ots`='1' WHERE id='".$cpid."' and uid = '".$uid."'");

        return $res;
    }


    public static function  user_character($uid,$action = ''){
        //是否有性格报告
        $stdata = self::where('uid',$uid)->where('ots',1)->order('datetime','desc')->find();
        if(!$stdata){
            return false; //  不存在性格报告
        }

        $valuser = $stdata['val1'].$stdata['val2'].$stdata['val3'].$stdata['val4'];
        //类型时间
        $xingdata = Caches::Xingall();
        $arrs['xing'] = $xingdata[$valuser] .'('.$valuser.')';
        $arrs['id'] =$stdata['id'];
        $arrs['datetime'] = date("Y年m月d日",$stdata['datetime']);
        /*偏向性*/
        $intE = $stdata['intE'];
        $intI = $stdata['intI'];
        $intS = $stdata['intS'];
        $intN = $stdata['intN'];
        $intF = $stdata['intF'];
        $intT = $stdata['intT'];
        $intP = $stdata['intP'];
        $intJ = $stdata['intJ'];

        if($intE==5 or $intE==6){
            $weidu1 = "均衡";
            $wdid1 = '3';
        }elseif($intE>8 or $intE<3){
            $weidu1 = "明显偏向";
            $wdid1 = '1';
        }elseif($intE<9 and $intE>2){
            $weidu1 = "偏向";
            $wdid1 = '2';
        }

        if($intS==5 or $intS==6){
            $weidu2 = "均衡";
            $wdid2 = '3';
        }elseif($intS>8 or $intS<3){
            $weidu2 = "明显偏向";
            $wdid2 = '1';
        }elseif($intS<9 and $intS>2){
            $weidu2 = "偏向";
            $wdid2 = '2';
        }
        if($intF==5 or $intF==6){
            $weidu3 = "均衡";
            $wdid3 = '3';
        }elseif($intF>8 or $intF<3){
            $weidu3 = "明显偏向";
            $wdid3 = '1';
        }elseif($intF<9 and $intF>2){
            $weidu3 = "偏向";
            $wdid3 = '2';
        }

        if($intP==5 or $intP==6){
            $weidu4 = "均衡";
            $wdid4 = '3';
        }elseif($intP>8 or $intP<3){
            $weidu4 = "明显偏向";
            $wdid4 = '1';
        }elseif($intP<9 and $intP>2){
            $weidu4 = "偏向";
            $wdid4 = '2';
        }

        $arrs['latitude'][] =$weidu1.'('.cheweidu($stdata['val1']).')';
        $arrs['latitude'][] =$weidu2.'('.cheweidu($stdata['val2']).')';

        $arrs['latitude'][] = $weidu3.'('.cheweidu($stdata['val3']).')';
        $arrs['latitude'][] =$weidu4.'('.cheweidu($stdata['val4']).')';

        if($action =="xgcp"){


            $xingename = xingge($stdata['val1'],$stdata['val2'],$stdata['val3'],$stdata['val4'],$wdid1,$wdid2,$wdid3,$wdid4);
            $vowels = array("<p>", "</p>");
            $onlyconsonants = str_replace($vowels,"",$xingename);
            $mangdianname = mangdian($stdata['val1'],$stdata['val2'],$stdata['val3'],$stdata['val4'],$wdid1,$wdid2,$wdid3,$wdid4);

            $vowelsis = array("<p>", "</p>");
            $onmangdian = str_replace($vowelsis,"",$mangdianname);

            $arrs ['onlyconsonants'] = $onlyconsonants;
            $arrs ['onmangdian'] = $onmangdian;

        }elseif($action=="cpfz"){

            $miaoval2 = miaos($stdata['val2']);
            $miaoval3 = miaos($stdata['val3']);

            $arrs ['miaoval2'] = $miaoval2;
            $arrs ['miaoval3'] = $miaoval3;


        }elseif($action=="cpedu"){
            $youshiname = youshi($stdata['val1'],$stdata['val2'],$stdata['val3'],$stdata['val4'],$wdid1,$wdid2,$wdid3,$wdid4);
            $youshibr = array("<p>", "</p>");
            $onyoushi = str_replace($youshibr,"",$youshiname);

            $lieshiname = lieshi($stdata['val1'],$stdata['val2'],$stdata['val3'],$stdata['val4'],$wdid1,$wdid2,$wdid3,$wdid4);

            $lieshibr = array("<p>", "</p>");
            $onlieshi = str_replace($lieshibr,"",$lieshiname);



            $edusjiaoyu1234=  edusjiaoyu($stdata['val1'],$stdata['val2'],$stdata['val3'],$stdata['val4'],$wdid1,$wdid2,$wdid3,$wdid4);

            $arrs ['onyoushi'] = $onyoushi;
            $arrs ['onlieshi'] = $onlieshi;


            $arrs['edusjiaoyu1234'] = $edusjiaoyu1234;
        }elseif($action=='cpzypx'){
            $arrs['youshiname']  = eduzhiye($stdata['val1'],$stdata['val2'],$stdata['val3'],$stdata['val4'],$stdata['subject']);
        }
        return $arrs;
    }

}

