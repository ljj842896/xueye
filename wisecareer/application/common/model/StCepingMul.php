<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class StCepingMul extends Model
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
        return self::name('st_ceping_udname_mul')->where('uid',$uid)->where('cpid',$cpid)->where('daima',0)->count();
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
            self::name('st_ceping_udname_mul')->where('uid',$uid)->where('cpid',$cpid)->where('listorder',$value[0])->where('tid',$value[1])->data($array)->update();
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

        //array_slice 取该数组中的某一段
        $no=80;
        for ($i=0;$i<$no;$i++){
            $it = $i+1;
            $array[$i]['uid'] =$uid;
            $array[$i]['cpid'] =$insert_id;
            $array[$i]['tid'] = $it;
            $array[$i]['listorder'] =$it;
        }

        return self::name('st_ceping_udname_mul')->data($array)->insertAll();

    }

    //调取测评题
    public static function sel_character($uid)
    {
        $cpid = self::where('uid',$uid)->where('ots',0)->value('id');

        $data = self::name('st_ceping_udname_mul')->field('listorder,tid')->where('uid',$uid)->where('cpid',$cpid)->where('daima',0)->order('listorder','asc')->limit(4)->select();
        if($data && gettype($data)=='object'){
            $data = $data->toArray();
        }
        if(!$data){
            return false;
        }
        $zong = 80;
        $ye = ceil($data[0]['listorder']/4);
        foreach ($data as $key => $value) {
            $sql = "SELECT `id`,`tid`,`cid`,`leta`,`tpname`,`daa`,`qingj`,`leta`,`daa`,`letb`,`dab`,`letc`,`dac`,`letd`,`dad`,`lete`,`dae` FROM kw_cp_id where tid = '13' and cid = '".$value['tid']."'  limit 1";
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

//    public static function st_save_sub($uid, $cpid,$subject){
//        return self::where('id',$cpid)->where('uid',$uid)->update(['ots'=>1]);
//    }

    public static function st_save($uid, $cpid)
    {

        $data = self::name("st_ceping_udname_mul")->where('uid',$uid)->where('cpid',$cpid)->field('tid,daima')->select();
        if($data ==null){
            return false;
        }

        $int1 =0;
        $int2 =0;
        $int3 =0;
        $int4 =0;
        $int5 =0;
        $int6 =0;
        $int7 =0;
        $int8 =0;
        $val1 =0;
        $val2 =0;
        $val3 =0;
        $val4 =0;
        $val5 =0;
        $sum = 0;

        $ints = [];

        foreach ($data as $key =>$value){

            if($value['daima']==1){
                $val1++;
            }elseif($value['daima']==2){
                $val2++;
            }elseif($value['daima']==3){
                $val3++;
            }elseif($value['daima']==4){
                $val4++;
            }elseif($value['daima']==5){
                $val5++;
            }

            $sum = $value['daima'] + $sum;
            if($value['tid']%10==0){
                $ints[] = $sum;
                $sum =0;
            }
        }

        foreach ($ints as $k=>$val){
            $ks = $k+1;
            ${"int".$ks} = $val;
        }

        $updatedata = [
            'ots'=>1,
            'datetime'=>time(),
            'int1'=>$int1,
            'int2'=>$int2,
            'int3'=>$int3,
            'int4'=>$int4,
            'int5'=>$int5,
            'int6'=>$int6,
            'int7'=>$int7,
            'int8'=>$int8,
            'val1'=>$val1,
            'val2'=>$val2,
            'val3'=>$val3,
            'val4'=>$val4,
            'val5'=>$val5,
        ];


        return self::where('id',$cpid)->where('uid',$uid)->update($updatedata);

    }



    public static  function user_character($uid,$action = ''){

        // 用户性格测评 表
        $stdata = self::where('ots',1)->where('uid',$uid)->order('id','desc')->find();

        if($stdata==null){
            return false;
        }
        $result =[];
        $result['datetime'] =  date("Y年m月d日",$stdata['datetime']);



        $User_hangye_ph=array(
            0=>array("name"=>$stdata['int1'],"mail"=>"语言智能","mid"=>"124"),
            1=>array("name"=>$stdata['int2'],"mail"=>"数学逻辑智能","mid"=>"125"),
            2=>array("name"=>$stdata['int3'],"mail"=>"视觉空间智能","mid"=>"126"),
            3=>array("name"=>$stdata['int4'],"mail"=>"自然观察智能","mid"=>"127"),
            4=>array("name"=>$stdata['int5'],"mail"=>"音乐智能","mid"=>"128"),
            5=>array("name"=>$stdata['int6'],"mail"=>"肢体运动智能","mid"=>"129"),
            6=>array("name"=>$stdata['int7'],"mail"=>"人际交往智能","mid"=>"130"),
            7=>array("name"=>$stdata['int8'],"mail"=>"内省智能","mid"=>"131")
        );


        $User_hangye_phNum = array_sort($User_hangye_ph,'name');
        $key = 0;
        $User_hangye_ph = array();
        foreach($User_hangye_phNum as $val) {
            $hangyephArray[$key] = $val;
            $key++;
        }
        $result['advantage']  = [$hangyephArray[7]["mail"],$hangyephArray[6]["mail"],$hangyephArray[5]["mail"]];
        $result['avoid'] =[$hangyephArray[0]["mail"],$hangyephArray[1]["mail"]];




            $tablestr = <<<EOT
        <table class="tablestr" border="1" cellspacing="0" cellpadding="0" width="100%">
                <tr>
            <td width="78" valign="top"><p align="center">语言智能</p></td>
            <td width="84" valign="top"><p align="center">数学逻辑智能 </p></td>
            <td width="78" valign="top"><p align="center">视觉空间智能 </p></td>
            <td width="72" valign="top"><p align="center">自然观察智能 </p></td>
            <td width="78" valign="top"><p align="center">音乐智能 </p></td>
            <td width="78" valign="top"><p align="center">肢体运动智能 </p></td>
            <td width="78" valign="top"><p align="center">人际交往智能 </p></td>
            <td width="85" valign="top"><p align="center">内省智能</p></td>
          </tr>
          <tr>
            <td width="78" valign="top"><p align="center">{$stdata['int1']}</p></td>
            <td width="84" valign="top"><p align="center">{$stdata['int2']}</p></td>
            <td width="78" valign="top"><p align="center">{$stdata['int3']}</p></td>
            <td width="72" valign="top"><p align="center">{$stdata['int4']}</p></td>
            <td width="78" valign="top"><p align="center">{$stdata['int5']}</p></td>
            <td width="78" valign="top"><p align="center">{$stdata['int6']}</p></td>
            <td width="78" valign="top"><p align="center">{$stdata['int7']}</p></td>
            <td width="85" valign="top"><p align="center">{$stdata['int8']}</p></td>
          </tr>
        </table>
EOT;

            $result['table'] = $tablestr;
            $result['ifarmurl'] = "http://www.wisecareer.org/Report/report_ds/ad_user_average.php?token=".$_GET['token'];



            $result['nengli1'] = self::name('capacity')->where('mid',$hangyephArray[7]["mid"])->value('nengli');
            $result['nengli2'] = self::name('capacity')->where('mid',$hangyephArray[6]["mid"])->value('nengli');
            $result['nengli3'] = self::name('capacity')->where('mid',$hangyephArray[5]["mid"])->value('nengli');

            $result['xuexi1'] = self::name('capacity')->where('mid',$hangyephArray[7]["mid"])->value('xuexi');
            $result['xuexi2'] = self::name('capacity')->where('mid',$hangyephArray[6]["mid"])->value('xuexi');
            $result['xuexi3'] = self::name('capacity')->where('mid',$hangyephArray[5]["mid"])->value('xuexi');



            $result['gongju1'] = self::name('capacity')->where('mid',$hangyephArray[7]["mid"])->value('gongju');
            $result['gongju2'] = self::name('capacity')->where('mid',$hangyephArray[6]["mid"])->value('gongju');
            $result['gongju3'] = self::name('capacity')->where('mid',$hangyephArray[5]["mid"])->value('gongju');


        return $result;
    }

}

