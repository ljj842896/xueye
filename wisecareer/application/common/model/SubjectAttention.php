<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class SubjectAttention extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * 列表
     * @param $uid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function get_lilun($uid){
        $liluns = [2,4,5,8,9,10,15,17,27,28];

        $data = self::name('edu_lilun')->whereIn('tpid',$liluns)->field('tpid,tpname,pic,description')->select();
        foreach ($data as $key => $value) {
            $data[$key]['pic'] ="http://pic.wisecareer.org/".$value['pic'];
            $has = self::where('uid',$uid)->where('del',0)->where('classid',$value['tpid'])->find();
            if($has){
                unset($data[$key]);
            }else{
                $data[$key]['num'] = self::where('classid',$value['tpid'])->count();
                if(empty($value['description'])){
                    $data[$key]['description'] = '';
                }
            }
        }

        return $data;
    }

    /**
     * 学员订阅 学科动态
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function add_subject($uid,$classid){

        $data=  self::where('uid',$uid)->where('del',0)->where('classid',$classid)->find();

        if(!empty($data)){
            $updata = ['del'=>1,'stop_time'=>time()];
            return self::where('uid',$uid)->where('stop_time',0)->where('classid',$classid)->data($updata)->update();
        }else{
            $insertdata  = ['del'=>0,'uid'=>$uid,'classid'=>$classid,'datetime'=>time()];
            return self::insert($insertdata);
        }

    }

    /**
     * @param $classid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function subject_num($classid){
        $sql ="SELECT count(a.id) as num , l.tpname FROM `kw_subject_attention` a LEFT JOIN kw_edu_lilun l on a.classid = l.tpid WHERE a.classid  = {$classid} ";
        return self::query($sql);
    }
    /**
     * 学科动态 内容
     *
     * @param      <type>  $classid  The classid
     *
     * @return     <type>  The subject news.
     */

    public static function sel_subject_new($classid,$scid,$limit=0){
        $result = self::subject_num($classid);

        $sql = "SELECT source,titlename,intro,datetime,hits,pic,id FROM kw_subject_news WHERE open = 1 and  classid ={$classid} and ( scid =0 or FIND_IN_SET({$scid},scid) )  order by datetime desc ".$limit;
        $data =self::query($sql);

        foreach ($data as $key => $value) {
            $data[$key]['pic'] = 'http://pic.wisecareer.org/'.$value['pic'];
            $data[$key]['datetime'] = date('Y-m-d' ,$value['datetime']);
            $data[$key]['hits'] = $value['hits']+self::hits_nums($value['datetime']);
            $data[$key]['num'] = self::name('subject_like')->where('tpid',$value['id'])->count()  + ceil($data[$key]['hits']/13) ;
        }
        $result['data']  = $data;
        return $result;
    }
    /**
     * 推荐 动态
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function Recommend_subject($uid,$scid){

        if( !$scid){
            $scid = 0;
        }
        $sql = "SELECT titlename,intro,datetime,source,pic,id,classid FROM kw_subject_news WHERE open = 1 and classid !=0   and ( scid =0 or FIND_IN_SET({$scid},scid) ) ORDER BY datetime DESC LIMIT 4 ";

        $data = self::query($sql);

        $edulilundata = Caches::EduLilun();
        foreach ($data as $key => $value) {
            $data[$key]['pic'] ="http://pic.wisecareer.org/".$value['pic'];
            $data[$key]['titlename'] = empty($edulilundata[$value['classid']])? $value['titlename'] :'【'.$edulilundata[$value['classid']].'】'.$value['titlename'];
        }

        return $data;
    }



    /**
     * 学员订阅学科动态 数
     *
     * @param      <type>  $uid    The uid
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static  function getsubject($uid){
        $sql ="SELECT  l.tpname,a.stop_time,l.tpid,a.stop_time FROM `kw_subject_attention` a LEFT JOIN kw_edu_lilun l on a.classid = l.tpid WHERE a.uid = {$uid} and del = 0   GROUP BY a.classid";

        $data = self::query($sql);
        $EduLilunDataPic = self::name('edu_lilun')->column('pic','tpid');
        foreach ($data as $key => $value) {
            $data[$key]['num'] = self::where('classid',$value['tpid'])->count() ;
            $data[$key]['pic'] ="http://pic.wisecareer.org/".$EduLilunDataPic[$value['tpid']];
        }
        return  $data;
    }

    /**
     * 学科动态 内容
     *
     * @param      <type>  $classid  The classid
     *
     * @return     <type>  The subject news.
     */

    public static function get_subject_news($id,$uid){
        $data =self::name('subject_news')->whereid($id)->where('open',1)->field('id,classid,titlename,source,cuid,content,hits,pic,datetime')->find();

        if($data ==null ) return '';
        $EduLilundata = Caches::EduLilun();
        $array = self::where('uid',$uid)->where('del',0)->column('classid');

//        if(!in_array($data['classid'],$array)){
//            $tpname = empty($EduLilundata[$data['classid']])?'':$EduLilundata[$data['classid']];
//            $result['is'] =1;
//            $result['classid'] =$data['classid'];
//            $result['tpname'] =$tpname;
//            return $result;
//        }

        $data['pic'] =  'http://pic.wisecareer.org/'.$data['pic'];
        $data['hits'] =self::hits_nums($data['datetime']) +$data['hits'] ;
        $data['datetime'] = date('Y-m-d' ,$data['datetime']);
        $data['num'] = self::name('subject_like')->where('tpid',$data['id'])->count();
        $data['has'] =self::name('subject_like')->where('tpid',$id)->where('uid',$uid)->count();

        $result = self::subject_num($data['classid']);

        $result['data']  = $data;
        return $result;
    }

    //动态 流量量
    public static function subject_hitsnum($id){
        return self::name('subject_news')->whereid($id)->inc('hits')->update();
    }

    private static function hits_nums($time){

        return  ceil((time() - $time)/600);

    }


    /**
     * 点赞 动态
     * @param $tpid
     * @param $uid
     * @return int|string
     */
    public static function subject_like_add($tpid, $uid){
        $has = self::name('subject_like')->where('uid',$uid)->where('tpid',$tpid)->find();
        if($has){
            return self::name('subject_like')->where('uid',$uid)->where('tpid',$tpid)->delete();
        }else{
            $insertdata = ['uid'=>$uid,'tpid'=>$tpid,'datetime'=>time()];
            return self::name('subject_like')->data($insertdata)->insert();

        }
    }
}

