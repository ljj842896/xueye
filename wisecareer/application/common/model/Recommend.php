<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Recommend extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * @param $uid
     * @return array|int
     */
    public static function recomm_hangye($uid){
        $data =  self::name("user_edu_sc")->where('uid',$uid)->column('eid');
        if(empty($data)){
            return 0;
        }
        $like = "行业通用";
        $likenum = self::name('edu_info')->wherein('id',$data)->whereLike('alclass',$like)->count();
        $datanum = count($data);

        $data2 = self::name('edu_info')->whereNotLike('alclass',$like)->where('alclass','neq','')->wherein('id',$data)->group('alclass')->column('alclass');

        if(!$data2){
            return 0;
        }
        $result=[];
        foreach ($data2 as $key => $value) {
            $result= array_merge($result,explode('、',$value));
        }

        $strhaoma = array_unique($result);
        $num = 0;
        foreach ($strhaoma as $key => $value) {
            $strlike = $value;
            $ab1 = $likenum+ self::name('edu_info')->whereLike('alclass',$strlike)->wherein('id',$data)->count();
            $num += $ab1;
        }

        foreach ($strhaoma as $key => $value) {
            $strlike = $value;
            $abrs[$key]['tpname'] = $value;
            $abrs[$key]['per'] =number_format(( self::name('edu_info')->whereLike('alclass',$strlike)->wherein('id',$data)->count() +$likenum)/$num,3)*100;

        }
        $res = array_sort($abrs,'per','desc');
        $retus = array_slice($res, 0, 3);

        return $retus;

    }

    /**
     * @param $uid
     * @return array|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function recomm_lilun($uid){
        //$data = $this->select("user_edu_sc"."(t1)",["[>]"edu_info"(t2)" => ["t1.eid" => "id"]],'t2.lilun',['AND'=>['t1.uid'=>$uid,'t2.lilun[!]'=>'']]);
        $data =self::name('user_edu_sc')
            ->alias('t1')
            ->leftJoin('edu_info t2',' t1.eid  = t2.id')
            ->where('t1.uid',$uid)
            ->where('t2.lilun','neq','')
            ->column('t2.lilun');

        if(empty($data)){
            return 0;
        }
        $result=[];
        foreach ($data as $key => $value) {
            $result= array_merge($result,explode('、',$value));
        }

        $retus =array_count_values($result);
        arsort($retus);
        $data2 = array_slice($retus, 0, 3);

        $lilus = array(2,4,5,8,9,10,15,17,27);
        $edu_lilundata  = Caches::EduLilun();
        foreach($data2 as $key=>$val){
            $res['num'] = $val;
            $res['tpname'] = $key;
            $res['classid']= empty($edu_lilundata[$key])?'':$edu_lilundata[$key];
            if(in_array($res['classid'],$lilus)){

                $res['has']= self::name('subject_attention')->alias('t1')->leftJoin('edu_lilun t2','t1.classid = t2.tpid')->where('t1.uid',$uid)->where('t2.tpname',$key)->where('stop_time','>',time())->count();

            }else{
                $res['has']=3;
            }

            $results[] =$res ;
        }


        return $results;

        exit;
    }

    public static function recomm_lilun_subject($uid){
        //$data = $this->select("user_edu_sc"."(t1)",["[>]"edu_info"(t2)" => ["t1.eid" => "id"]],'t2.lilun',['AND'=>['t1.uid'=>$uid,'t2.lilun[!]'=>'']]);
        $data =self::name('user_edu_sc')
            ->alias('t1')
            ->leftJoin('edu_info t2',' t1.eid  = t2.id')
            ->where('t1.uid',$uid)
            ->where('t2.lilun','neq','')
            ->column('t2.lilun');

        if(empty($data)){
            return 0;
        }
        $result=[];
        foreach ($data as $key => $value) {
            $result= array_merge($result,explode('、',$value));
        }

        $subjects = ['思想政治','地理','物理','生物','化学','历史'];
        $retus =array_count_values($result);


        $retus1 = array_keys($retus);
        $data2 = array_intersect($subjects,$retus1);
        if(count($data2)<3){
            return false;
        }
        $data2 = array_flip($data2);
        $datas = array_intersect_key($retus,$data2);
        arsort($datas);
        $rest = array_slice($datas,0,3);
        $lilus = array(2,4,5,8,9,10,15,17,27);
        $lilus = ['思想政治'=>27,'地理'=>17,'物理'=>9,'生物'=>15,'化学'=>10,'历史'=>8];
        $edu_lilundata  = Caches::EduLilun();
        foreach($rest as $key=>$val){
            $res['tpname'] = $key;
            $res['tpid'] = $lilus[$key];
            $results[] =$res ;
        }

        return $results;

        exit;
    }

    /**
     * //推荐高校
     * @param $uid
     * @param $schoolid
     * @param $limit
     * @return int
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function recomm_school($uid, $schoolid, $limit){

        $data = self::name('user_edu_sc')->where('uid',$uid)->column('eid');
        if(empty($data)){
            return 0;
        }
        $diquid = Shcool::get_district($schoolid);
        $zid=implode(',',$data);

        $sql = "SELECT   c.pic,c.titlename,a.sid,b.rank,count(*) as num   FROM kw_shcool  c LEFT JOIN kw_shcool_eduzy b on c.id = b.sid LEFT JOIN kw_shcool_eduzy_info a on b.id = a.eduid where a.zid in ($zid) and a.cityid = $diquid and rank !=99  GROUP BY a.sid  order by num desc ,b.rank asc".$limit;
        $sqlnum = "SELECT   c.pic,c.titlename,a.sid,b.rank,count(*) as num   FROM kw_shcool  c LEFT JOIN kw_shcool_eduzy b on c.id = b.sid LEFT JOIN kw_shcool_eduzy_info a on b.id = a.eduid where a.zid in ($zid) and a.cityid = $diquid and rank !=99  GROUP BY a.sid  order by num desc ,b.rank asc";


        $result['list'] = self::query($sql);
        $result['num'] = count( self::query($sqlnum));
        return $result;
    }
}


