<?php

namespace app\common\model;

use app\common\Caches;
use app\common\Libs;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Scores extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }




    /**省控线
     * @param $where
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function sel_enrollProvince($where){
        $data= self::name('enrollprovince')
            ->alias('t1')
            ->leftJoin('class_class t2','t1.batch = t2.id ')
            ->where($where)
            ->field('t1.province_score,t1.batch,t1.year')
            ->order('t1.year','desc')
            ->order('t2.tbid','asc')
            ->select();

        $ClassClassdata = Caches::ClassClass();
        foreach ($data as $key => $value) {
            $data[$key]['yearname']  = $ClassClassdata[$value['year']] ;
            $data[$key]['batchname']  = $ClassClassdata[$value['batch']] ;
        }

        return $data;
    }


    /**大学分数线
     * @param $where
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function sel_enrollgrade($where){

        $data = self::name('enrollgrade')
            ->alias('t1')
            ->leftJoin('class_class t2','t1.batch = t2.id')
            ->where($where)
            ->field('t1.number,t1.batch,t1.year,t1.top_score,t1.average,t1.lowest,t1.subject')
            ->order('t1.subject','asc')
            ->order('t1.year','desc')
            ->order('t2.tbid','asc')
            ->select();

        $ClassClassdata = Caches::ClassClass();
        foreach ($data as $key => $value) {
            $data[$key]['yearname']  = $ClassClassdata[$value['year']] ;
            $data[$key]['batchname']  = $ClassClassdata[$value['batch']] ;
        }

        return $data;
    }


    /**只分选大学
     * @param $where
     * @param $score
     * @return mixed
     */
    public static function knowledge_points($where, $score)
    {
        $data1=self::knowledge_ative($where,$score);
        $data2=self::knowledge_safe($where,$score);
        $data3=self::knowledge_sprint($where,$score);
        $result['ative']=$data1;;
        $result['safe']=$data2;;
        $result['sprint']=$data3;
        return $result;

    }

    /**
     * @param $where
     * @param $score
     * @param $action
     * @param $uid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function knowledge_points_list($where, $score, $action, $uid)
    {
        if($action == 'ative'){
            $data=self::knowledge_ative($where,$score);
        }elseif($action=='safe'){
            $data=self::knowledge_safe($where,$score);
        }elseif($action =='sprint'){
            $data=self::knowledge_sprint($where,$score);
        }else{
            return false;
        }

        $result=[];

        $SchoolUserScIddata = Caches::SchoolUserScId($uid);
        foreach ($data['list'] as $key => $value) {
            $res=[];

           // $datas=$this->select($this->table1.'(t1)',['[>]class_class(t2)'=>['t1.year'=>'id']],['t1.top_score','t2.tpname(year)','t1.lowest','t1.average'] ,['AND'=>['t1.schoolid'=>$value['schoolid'],'t1.subject'=>$where['subject'],'t1.scid'=>$where['scid'],'t1.batch'=>$value['batch']],'ORDER'=>['year'=>'DESC']]);

            $datas =self::name('enrollgrade')
                ->alias('t1')
                ->leftJoin('class_class t2','t1.year = t2.id')
                ->field('t1.top_score,t2.tpname year,t1.lowest,t1.average')
                ->where('t1.schoolid',$value['schoolid'])
                ->where('t1.subject',$where['subject'])
                ->where('t1.scid',$where['scid'])
                ->where('t1.batch',$value['batch'])
                ->order('year','desc')
                ->select();
            $result[$key]['list']=$datas;
            $areadata = Caches::AreaAll();
            $calssclassdata = Caches::ClassClass();
            $res = Shcool::whereid($value['schoolid'])->field('titlename,tagid,scid,sdid,pic,id')->find();
            $res['scidname']=empty($areadata[$res['scid']])?'':$areadata[$res['scid']];
            $res['sdidname']= empty($areadata[$res['sdid']])?'':$areadata[$res['sdid']];
            $res['tagid_str'] = Libs::checkleiArr($res['tagid'],'class_class','id','tpname',',');
            $res['schas'] =  in_array($value['schoolid'],$SchoolUserScIddata)?1:0;
            $res['batch'] = empty( $calssclassdata[$value['batch']])?'': $calssclassdata[$value['batch']];
            $result[$key]['shcoolfile'] = $res;
        }

        return $result;

    }

    /*


    我的分数 >= 历年最高分平均值   为“可保底”  80% - 100%

    历年最高分平均值 > 我的分数  > 平均分（历年平均值）      为“较稳妥” -- 所有   50%-80%

    3、最低分（历年平均值）  >= 我的分数   为“可冲刺” -- 最多10所   30%-50%
    */

    /** 保底
     * @param $where
     * @param $score
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function knowledge_ative($where, $score)
    {
        $avgscore = $score;
//        $data=  $this->select($this->table4.'(t1)',['[>]class_class(t2)'=>['t1.batch'=>'id']],['t1.schoolid','t1.batch'],['AND'=>['AND'=>$where,'top_score[<=]'=>$avgscore,'top_score[!]'=>0,'average[!]'=>0,'batch[!]'=>41],'ORDER'=>['t2.tbid'=>'ASC','top_score'=>'DESC'],'LIMIT'=>20]);

        $data = self::name('enrollgrade_refresh')
            ->alias('t1')
            ->leftJoin('class_class t2','t1.batch = t1.id')
            ->where($where)
            ->where('top_score','<=',$avgscore)
            ->where('top_score','neq',0)
            ->where('t1.batch','neq',41)
            ->field('t1.schoolid,t1.batch')
            ->order('t2.tbid','asc')
            ->order('top_score','desc')
            ->limit(20)->select();



        $num = count($data);
        $result['num'] =$num;
        $result['list'] =$data;
        return $result;
    }

    /**稳妥
     * @param $where
     * @param $score
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function knowledge_safe($where, $score)
    {
        $scoreavg = $score;
//        $data=  $this->select($this->table4.'(t1)',['[>]class_class(t2)'=>['t1.batch'=>'id']],['schoolid','batch'],['AND'=>['AND'=>$where,'average[<=]'=>$scoreavg,'batch[!]'=>41,'top_score[!]'=>0,'average[!]'=>0,'OR'=>['top_score[>=]'=>$scoreavg,'top_score'=>0]],'ORDER'=>['t2.tbid'=>'ASC','average'=>'DESC']]);
        $data = self::name('enrollgrade_refresh')
            ->alias('t1')
            ->leftJoin('class_class t2','t1.batch = t1.id')
            ->where($where)
            ->where('lowest','<=',$scoreavg)
            ->where('top_score','neq',0)
            ->where('average','neq',0)
            ->where('t1.batch','neq',41)
            ->where(function ($query) use($scoreavg){
                $query->where('top_score', ['>=', $scoreavg], ['=', 0], 'or');
            })
            ->field('schoolid,batch')
            ->order('t2.tbid','asc')
            ->order('average','desc')
            ->limit(20)->select();;




        $num = count($data);
        $result['num'] =$num;
        $result['list'] =$data;
        return $result;
    }


    /**可冲刺
     * @param $where
     * @param $score
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function knowledge_sprint($where, $score)
    {
        $scoreavg = $score + 10 + 0 ;
//,'OR'=>['lowest[<=]'=>$scoreavg,'lowest'=>0]
//        $data=  $this->select($this->table4.'(t1)',['[>]class_class(t2)'=>['t1.batch'=>'id']],['schoolid','batch'],['AND'=>['AND'=>$where,'OR'=>['average[>=]'=>$scoreavg,'AND'=>['average'=>0,'lowest[>=]'=>$scoreavg]],'batch[!]'=>41],'ORDER'=>['t2.tbid'=>'ASC','lowest'=>'ASC'],'LIMIT'=>20]);

        $data = self::name('enrollgrade_refresh')
            ->alias('t1')
            ->leftJoin('class_class t2','t1.batch = t1.id')
            ->where($where)
            ->where('t1.batch','neq',41)
            ->where(function ($query) use($scoreavg,$score){
                $query->where([['lowest', '<=', $scoreavg],['lowest','>=',$score]]);
            })
            ->field('schoolid,batch')
            ->order('t2.tbid','asc')
            ->order('lowest','ASC')
            ->limit(20)->select();

        $num = count($data);
        $result['num'] =$num;
        $result['list'] =$data;
        return $result;
    }

    /**知位选大学
     * @param $where
     * @param $rank
     * @return bool|mixed
     * @throws \think\exception\DbException
     */
    public static function knowledge_rank($where, $rank)
    {
//SELECT `year` FROM `kw_enrollRanking` WHERE scid = 360 GROUP BY `year`
        $score = self::get_score($where,$rank);
        if(!$score){
            return false;
        }
        $result = self::knowledge_points($where,$score);

        return $result;

    }


    /**
     * @param $where
     * @param $rank
     * @return bool|float
     * @throws \think\exception\DbException
     */
    private  static  function get_score($where, $rank)
    {
        $years = self::name('enrollranking')->where('scid',$where['scid'])->group('year')->column('year');
        if(!$years){
            return false;
        }
        //SELECT  *  FROM `kw_enrollRanking` WHERE scid = 360 and `year` = 46 and `subject` =2 and  ranking <=456  ORDER BY ranking   desc  LIMIT 1
        $sum = 0;
        $num = 0;


        foreach ($years as $value) {
            $sum += self::name('enrollranking')->where('scid',$where['scid'])->where('year',$value)->where('subject',$where['subject'])->where('ranking','<=',$rank)->order('ranking','desc')->value('score');

            $num++;
        }


        $score = round(($sum/$num),1);
        return $score;
    }


    /**
     * @param $where
     * @param $rank
     * @param $action
     * @param $uid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function knowledge_rank_list($where, $rank, $action, $uid){
        $score = self::get_score($where,$rank);

        if(!$score){
            return false;
        }
        if($action == 'ative'){
            $data=self::knowledge_ative($where,$score);
        }elseif($action=='safe'){
            $data=self::knowledge_safe($where,$score);
        }elseif($action =='sprint'){
            $data=self::knowledge_sprint($where,$score);
        }else{
            return false;
        }

        $result=[];
        $SchoolUserScIddata = Caches::SchoolUserScId($uid);
        foreach ($data['list'] as $key => $value) {
            $res=[];
//            $datas=$this->select($this->table1.'(t1)',['[>]class_class(t2)'=>['t1.year'=>'id']],['t1.top_score','t2.tpname(year)','t1.lowest','t1.average'] ,['AND'=>['t1.schoolid'=>$value['schoolid'],'t1.subject'=>$where['subject'],'t1.scid'=>$where['scid'],'t1.batch'=>$value['batch']],'ORDER'=>['year'=>'DESC']]);
            $datas =self::name('enrollgrade')
                ->alias('t1')
                ->leftJoin('class_class t2','t1.year = t2.id')
                ->where('t1.schoolid',$value['schoolid'])
                ->where('t1.subject',$where['subject'])
                ->where('t1.scid',$where['scid'])
                ->where('t1.batch',$value['batch'])
                ->order('year','desc')
                ->field('t1.top_score,t2.tpname year,t1.lowest,t1.average')
                ->select();

            $result[$key]['list']=$datas;
            $res=  Shcool::whereid($value['schoolid'])->field('titlename,scid,sdid,pic,id')->find();
            $areadata = Caches::AreaAll();
            $calssclassdata = Caches::ClassClass();
            $res['scidname']=empty($areadata[$res['scid']])?'':$areadata[$res['scid']];
            $res['sdidname']= empty($areadata[$res['sdid']])?'':$areadata[$res['sdid']];
            $res['schas'] =    in_array($value['schoolid'],$SchoolUserScIddata)?1:0;
            $res['batch'] = empty( $calssclassdata[$value['batch']])?'': $calssclassdata[$value['batch']];
            $result[$key]['shcoolfile'] = $res;
        }
        return $result;
    }

    /** 年级排名
     * @param $uid
     * @param string $schoolid
     * @param string $classname
     * @param string $classroom
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function score_branch($uid, $schoolid='', $classname='', $classroom =''){

        if(!$schoolid && !$classname && !$classroom ){
            return false;
        }
        $where= [];
        if($schoolid){
            $where['schoolname']=$schoolid;
        }
        if($classname){
            $where['classname']=$classname;
        }
        if($classroom){
            $where['classroom']=$classroom;
        }
        //$ids = $this->select('myuser','id',['AND'=>$where]);
        $ids = self::name('myuser')->where($where)->column('id');
        $userscore = self::name('user_scores_avg')->where('uid',$uid)->field('perc2,perc4,perc5,perc8,perc10,perc15,perc17,perc27,perc9')->find();
        if(empty($userscore)){
            return false;
        }
        $userscore = $userscore->toArray();
        $data = self::name('user_scores_avg')->whereIn('uid',$ids)->field('perc2,perc4,perc5,perc8,perc9,perc10,perc15,perc17,perc27')->select()->toArray();

        $scores2 = array_column($data,'perc2');
        $scores4 = array_column($data,'perc4');
        $scores5 = array_column($data,'perc5');
        $scores8 = array_column($data,'perc8');
        $scores9 = array_column($data,'perc9');
        $scores10 = array_column($data,'perc10');
        $scores15 = array_column($data,'perc15');
        $scores17 = array_column($data,'perc17');
        $scores27 = array_column($data,'perc27');
        rsort($scores2);
        rsort($scores4);
        rsort($scores5);
        rsort($scores8);
        rsort($scores9);
        rsort($scores10);
        rsort($scores15);
        rsort($scores17);
        rsort($scores27);
        $scores2= array_filter($scores2);
        $scoresnum['scores2'] = count($scores2);
        $scores4= array_filter($scores4);
        $scoresnum['scores4'] = count($scores4);
        $scores5= array_filter($scores5);
        $scoresnum['scores5']= count($scores5);
        $scores8= array_filter($scores8);
        $scoresnum['scores8']= count($scores8);
        $scores9= array_filter($scores9);
        $scoresnum['scores9']= count($scores9);
        $scores10= array_filter($scores10);
        $scoresnum['scores10'] = count($scores10);
        $scores15= array_filter($scores15);
        $scoresnum['scores15'] = count($scores15);
        $scores17= array_filter($scores17);
        $scoresnum['scores17'] = count($scores17);
        $scores27= array_filter($scores27);
        $scoresnum['scores27'] = count($scores27);
        $scores['scores2'] = $scores2;
        $scores['scores4'] = $scores4;
        $scores['scores5'] = $scores5;
        $scores['scores8'] = $scores8;
        $scores['scores9'] = $scores9;
        $scores['scores10'] = $scores10;
        $scores['scores15'] = $scores15;
        $scores['scores17'] = $scores17;
        $scores['scores27'] = $scores27;
        $result = [];

        $tpids = array(2,4,5,8,9,10,15,17,27);
        $classdata =self::name('edu_lilun')->whereIn('tpid',$tpids)->field('tpid,tpname')->select();
        foreach ($classdata as $key => $value) {
            $result[$key]['score'] = !empty($userscore['perc'.$value['tpid']]) ? array_search($userscore['perc'.$value['tpid']],$scores['scores'.$value['tpid']] ) +1 :'无';
            $result[$key]['num']= $scoresnum['scores'.$value['tpid']];
            $result[$key]['tpname']= $value['tpname'];
            $result[$key]['tpid']= $value['tpid'];
            $result[$key]['perc']= !empty($userscore['perc'.$value['tpid']]) ? $userscore['perc'.$value['tpid']]: '0' ;
        }

        return $result;


    }

    /**
     * 各科成绩排名
     * @param $uid
     * @param string $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function user_score_branch($uid, $data=''){
        if($data ==''){
            $data = self::name('user_scores_avg')->where('uid',$uid)->field('perc2,perc4,perc5,perc8,perc10,perc15,perc17,perc27,perc9')->find();
            if(empty($data)){
                return false;
            }
            $data = $data->toArray();
        }

        $tpids = array(2,4,5,8,9,10,15,17,27);
        $classdata =self::name('edu_lilun')->whereIn('tpid',$tpids)->field('tpid,tpname')->select();
        foreach ($classdata as $key => $value) {
            $result[$key]['tpname']= $value['tpname'];
            $result[$key]['tpid']= $value['tpid'];
            $result[$key]['perc']= !empty($data['perc'.$value['tpid']]) ? $data['perc'.$value['tpid']]: '0' ;
        }

       return $result;

    }


}


