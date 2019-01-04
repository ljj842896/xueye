<?php
namespace app\api\controller;

use app\common\Charts;
use app\common\model\Anli_search;
use app\common\model\AppUserEvaluate;
use app\common\model\Coach;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\Exception;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Coachs extends Action
{
    /**
     * 搜教练
     */
    public function search_coach()
    {
        $userid = $this->uid;
        $array = [];
        if(!empty($_GET['actten'])){
            if($arr_actten = Coach::acten_me($userid)){
                $array  = array_merge($array,$arr_actten);
            }
        }
        //推荐过我
        if(!empty($_GET['recom'])){
            if($arr_recom = Coach::recom_me($userid) ){
                $array  = array_merge($array,$arr_recom);
            }
        }
        //评价过我
        if(!empty($_GET['eval'])){
            if($arr_eval = Coach::eval_me($userid) ){
                $array  = array_merge($array,$arr_eval);
            }
        }
        //现场指导过我
        if(!empty($_GET['scene'])){
            if($arr_scen =Coach::scene_me($userid) ){
                $array  = array_merge($array,$arr_scen);
            }
        }
        $arr_name =false;
        //  搜索条件


        if(!empty(input('name'))){
            $name = trim(input('name'));
            $encode = mb_detect_encoding($name, array("ASCII","UTF-8","GB2312","GBK","BIG5"));
            $str_encode = mb_convert_encoding($name,'utf8');

            try{
                $arr_name = Coach::serarch_name($str_encode);
                $arr_name = empty($arr_name)?[]:$arr_name;
            }catch (Exception $e){

            }




        }
        $arraydatas = false;

        if(!empty(input('job'))){
            $job = intval(input('job'));
            $arr_coa = Coach::serarch_job($job);
            $arr_coa = empty($arr_coa)?[]:$arr_coa;
            if($arr_name===false){
                $arraydatas = $arr_coa;
            }else{
                $arraydatas =  array_intersect($arr_name,$arr_coa);
            }


        }
        $coachdata = false;
        if(!empty(input('city'))){
            $city = intval(input('city'));
            $arr_cit = Coach::serarch_city($city);
            $arr_cit = empty($arr_cit)?[]:$arr_cit;
            if($arraydatas !==false){
                $coachdata = array_intersect($arr_cit,$arraydatas);
            }elseif($arr_name !==false){
                $coachdata = array_intersect($arr_cit,$arr_name);
            }else{
                $coachdata = $arr_cit;
            }
        }



        if($coachdata!==false){
            $array  = array_merge($array,$coachdata);
        }elseif($arraydatas!==false){
            $array  = array_merge($array,$arraydatas);
        }elseif($arr_name !==false){
            $array  = array_merge($array,$arr_name);
        }



        $uidslist2=[];
        if(!empty(input('tpid'))){
            $tpid = intval(input('tpid'));
            $ids =  Anli_search::where(Anli_search::raw('FIND_IN_SET('.$tpid.',hangye)'))->column('id');

            if($ids !=null){
                foreach ($ids as $val){
                    $arrs = Coach::serarch_job($val);
                    $arrs = $arrs?$arrs:[];
                    $uidslist2 = array_merge( $uidslist2,$arrs);
                }
                $uidslist2 = array_unique($uidslist2);
            }

        }elseif(!empty(input('id'))){
            $id = intval(input('id'));
            $ids =  Anli_search::where(Anli_search::raw('FIND_IN_SET('.$id.',lxclass)'))->column('id');
            if($ids !=null){
                foreach ($ids as $val){
                    $arrs = Coach::serarch_job($val);
                    $arrs = $arrs?$arrs:[];
                    $uidslist2 = array_merge( $uidslist2,$arrs);
                }
                $uidslist2 = array_unique($uidslist2);
            }
        }



        $uids = array_unique($array);
        $data1 = Coach::coach_list($uids,$userid);
        $data2 = Coach::coach_list($uidslist2,$userid);
        $resutl['list1'] =$data1;
        $resutl['list2'] =$data2;
        return $this->echo_json(0,'',$resutl);
    }
    // 我的教练
    public function mycoach()
    {
        $userid = $this->uid;
        $data =AppUserEvaluate::get_user_like_atten($userid);
        return $this->echo_json(0,'',$data);
    }

}
