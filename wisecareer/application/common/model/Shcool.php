<?php

namespace app\common\model;

use app\common\Caches;
use app\common\Libs;
use Db;
use think\db\exception\BindParamException;
use think\exception\DbException;
use think\exception\PDOException;
use think\facade\Cache;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Shcool extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * 所以学校名字
     * @param $query
     */
    public function scopeAll($query)
    {
        $query->field('id,titlename,cooperation');
    }

    /**
     * 所以学校名字
     * @param $query
     */
    public function scopeAllTitle($query)
    {
        $query->field('id,titlename');
    }

    /**
     * 所以 大学
     * @param $query
     */
    public function scoped($query)
    {
        $query->where('lx','15')->whereOr('lx','32')->whereOr('lx','44')->field('id,titlename,cooperation');
    }
    /**
     * 所以中学
     * @param $query
     */
    public function scopez($query)
    {
        $query->where('lx','3')->whereOr('lx','9')->field('id,titlename,cooperation');
    }
    /**
     *  根据学校名字调出 学校数据
     * @param $query
     */
    public function scopeTitlename($query,$titlename){
        $query->where('titlename',$titlename)->field('id,titlename,lx,scid,sdid,cooperation');
    }


    /**
     * 验证学校验证码对么
     * @param $array
     * @return array|null|\PDOStatement|string|Model
     */
    public static function shcoolVerify($array){
       return self::name('school_set')->where ($array)->find ();
    }


    /**
     * 	// 大学检索
     * @param $uid
     * @param $where
     * @param $limit
     * @return mixed
     * @throws DbException
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\PDOException
     */
    public  function search_school_where($uid, $where, $limit){

        $sqlzong = "SELECT  count(id) as num  FROM kw_shcool t left join kw_shcool_class t8 on t.lx = t8.tpid  where id > 0  and lx!='3' and lx !='9' ".$where;

        $sql = "SELECT t.id,t.pic,t.titlename,t.scid,t.sdid,t.tagid,t8.tpname FROM kw_shcool t left join kw_shcool_class t8 on t.lx = t8.tpid  where id > 0  and lx!='3' and lx !='9' ".$where. $limit;
        $uidsqlkey = "SearchSchool".md5($sql);
        $sqlkey =  "SearchSchoolnum".md5($sqlzong) ;
        $data=Cache::get($uidsqlkey);
        $zonnum=Cache::get($sqlkey);
        if(!$data){
            $data = $this->query($sql);  Cache::set($uidsqlkey,$data,CACHE_DATA_TIME);
        }
        if(!$zonnum){
            $zonnum = $this->query($sqlzong)[0]['num']; Cache::set($sqlkey,$zonnum,CACHE_DATA_TIME);
        }

        $areadata = Caches::AreaAll();
        $tagiddata = Caches::ClassClass(11);
        $school_scdata = Caches::SchoolUserScId($uid);


        foreach($data as $key => $value){
            $value['pic']  ||  $data[$key]['pic'] = "/images/xueye/zanwu.png";
            $data[$key]['scid']=empty($areadata[$value['scid']])?'':$areadata[$value['scid']];
            $data[$key]['sdid']=empty($areadata[$value['sdid']])?'':$areadata[$value['sdid']];

            if($value['tagid']==''){
                $data[$key]['tagids']=[];
            }else{
                $tagid= explode(',',$value['tagid']);
                foreach ($tagid as $k =>$v){
                    $data[$key]['tagids'][] = empty($tagiddata[$v])?'':$tagiddata[$v];
                }
            }


            $data[$key]['has'] = in_array($value['id'],$school_scdata)?1:0;
        }
        $res['num'] = $zonnum;
        $res['list'] = $data;
        return $res;

    }

    /**
     * 学校院系结构
     * @param $schoolid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function school_structure($schoolid){
        $data = self::name('shcool_prdclass')->where('tporder',0)->where('sid',$schoolid)->field('tpid,tpname')->order('tpsx','asc')->select();
        foreach($data as $key=>$value){
            if($value['tpname']=='无'){
                $data[$key]['tpname']='开设专业';
            }
            $data2 = self::name('shcool_prdclass')
                ->where('tporder',$value['tpid'])
                ->where('sid',$schoolid)
                ->order('tpsx','asc')
                ->column('tpid');
            $array = [];
            foreach($data2 as $val){
                $data3=self::name('shcool_eduzy')->alias('t1')
                    ->leftJoin('edu_info t2','t1.zid = t2.id')
                    ->where('t1.tpid',$val)
                    ->field('t1.zid,t1.rank,t1.id,t1.tagid,t1.content,t2.titlename')
                    ->select();
                if(!empty($data3)){
                    $array = array_merge($data3->toArray(),$array);
                }
            }
            $data[$key]['departments'] =$array;
        }
        return $data;
    }

    /**
     * 学校风采
     * @param $id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function get_school_pic($id){
        $pic = self::name('shcool_pic')->where('classid',$id)->column('pic');
        return $pic?$pic:"/images/zanwu.png";
    }
    //获取高考考区
    public static  function get_district($schoolid){
        if($schoolid==0 || $schoolid ==999999){
            return 26;
        }
        $gkclssid = Cache::get('SchoolGaokaoclass'.$schoolid);
        if($gkclssid ===false){
            $gkclssid = self::whereid($schoolid)->value('gaokaoclass');
            Cache::set('SchoolGaokaoclass'.$schoolid,$gkclssid,CACHE_DATA_TIME);
        }
        $shcoolgkclass = [3,26,82];
        if(in_array($gkclssid,$shcoolgkclass)){
            $gaokao = $gkclssid;
        }else{
            $gaokao = 3;
        }
        return $gaokao;
    }
    /**
     * 开设院校
     * @param $limit
     * @param $majorid
     * @param $schoolid
     * @param $scid
     * @param $where
     * @param $lilun
     * @return mixed
     * @throws DbException
     */
    public static function get_professional_colleges( $majorid, $schoolid,$tagid, $limit='',$scid='',$where='',$lilun=''){
        $type =self::name('edu_info')->whereid($majorid)->value('type');

        if($type == 1){
            $cityid = 0;
        }else{
            $cityid = $schoolid? self::get_district($schoolid):0;
        }
        $data =self::get_school_num($limit,$majorid,$cityid,$tagid,$where,$scid,$lilun);

        if($limit==''){
            return count($data);
        }
        $AreaAlldata = Caches::AreaAll();
        foreach($data as $key=>$value){
//            $data[$key]['lx']=$this->get($this->table8,'tpname',['tpid'=>$value['lx']]);
            $data[$key]['college'] =self::name('shcool_prdclass')->alias('t1')->leftJoin('shcool_prdclass t2','t1.tporder = t2.tpid')->where('t1.tpid',$value['tpid'])->value('t2.tpname');
            $data[$key]['sc'] = empty($AreaAlldata[$value['scid']])?'':$AreaAlldata[$value['scid']];
            $data[$key]['sd'] = empty($AreaAlldata[$value['sdid']])?'':$AreaAlldata[$value['sdid']];
            if(!empty($value['content'])){
                $data[$key]['lilun'] = $value['lilun'].'('.$value['content'].')';
            }
            $data[$key]['tagid'] =Libs::checkleiArr($value['tagid'],'class_class','id','tpname',',');
            $data[$key]['tagids'] =Libs::checkleiArr($value['tagids'],'class_class','id','tpname',',');

        }
        return $data;
    }


    /**
     * @param $zids
     * @param $schoolid
     * @return mixed
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function majorlei_rank($zids, $schoolid){
        $rankdata = self::name('edu_info')->wherein('id',$zids)->field('rankid,ranktitle')->find();
        $ranktitle = $rankdata['ranktitle'];
        $rankid = $rankdata['rankid'];
        $data =self::name('shcool_eduzy')
            ->wherein('zid',$zids)
            ->where('rank','neq',99)
            ->field('id,rank,zid,sid')
            ->group('sid')
            ->order('rank','asc')
            ->select();

        $data =$data ->toArray();
        $schoolids = array_column($data,'sid');

        $schooldata = self::wherein('id',$schoolids)->field('id,titlename,pic')->select()->toArray();
        $schoolidkey = array_column($schooldata,'id');
        $schooldatas = array_combine($schoolidkey,$schooldata);

        foreach ($data as $key => $value) {

            $data[$key]['shcoolname']= $schooldatas[$value['sid']]['titlename'];
            $pic =$schooldatas[$value['sid']]['pic'];
            $data[$key]['pic'] =$pic?$pic:"/images/xueye/zanwu.png";
            # code...
        }

        $result['ranktitle'] = $ranktitle;
        $result['rankid'] = $rankid;
        $result['list'] = $data;

        return $result;
    }

    /**    //专业类
     * @return array|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static  function majorlei_ranklist(){
        $data= self::name('edu_info')->where('open',1)->where('ranktitle','neq','')->group('ranktitle')->order('id','asc')->field('id,ranktitle,rankid')->select();

        foreach ($data as $key => $value) {
            $data2 =self::name('edu_info')->where('open',1)->where('ranktitle',$value['ranktitle'])->order('id','asc')->column('id');
            $data[$key]['pai'] =$data2 ;
        }
        return $data;
    }


     /** 获取开设院校数目
     * @param $limit
     * @param $majorid
     * @param $cityid
     * @param string $where
     * @param string $scid
     * @param string $lilun
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public  static  function get_school_num($limit, $majorid, $cityid, $tagid='',$where='', $scid =" and c.scid != '' ", $lilun = ''){
        //a.id, a.sid,a.tpid,c.scid,c.sdid,c.lx,c.tagid,b.tagid as tagids ,b.rank,a.lilun,b.content,a.zid,a.cityid

        if($cityid){
            $cityidwhere = " and a.cityid = ".$cityid;
        }else{
            $cityidwhere = '';
        }
        if($tagid !=''){
            $tagidar  = explode(',',$tagid);
            $tagidarwhere =" and ( ";
            foreach ( $tagidar as $key =>$v){
                $tagidarwhere .= " FIND_IN_SET({$v},b.tagid) or";
            }
            $tagidarwhere = rtrim($tagidarwhere,'or');
            $tagidarwhere .=") ";
        }else{
            $tagidarwhere='';
        }
        if($lilun){
            $lilunwhere = "   and  a.lilun = '".$lilun."'";
        }else{
            $lilunwhere =  '   ';
        }
        $sql = "SELECT  c.titlename,a.id,c.pic, a.sid,a.tpid,c.scid,c.sdid,c.lx,c.tagid,b.tagid as tagids ,b.rank,a.lilun,a.content,a.zid,a.cityid FROM kw_shcool c LEFT JOIN kw_shcool_eduzy b on c.id = b.sid LEFT JOIN kw_shcool_eduzy_info a on b.id = a.eduid where a.zid = ".$majorid.$tagidarwhere.$lilunwhere.$scid.$cityidwhere." order by b.rank asc ".$limit;


        $data = self::query($sql);
        return $data;
    }

    /**专业检索
     * @param $uid
     * @param $schoolid
     * @param $where
     * @param $wheretag
     * @param $limit
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function search_major( $schoolid, $where,$tagid, $wheretag, $limit=''){

        $gaokaodiquid = self::get_district($schoolid);

        if($limit==''){
            if(!empty($_GET['type2'])){
                if($wheretag){
                    $zonsql = " SELECT count(*) as num from ( SELECT  count(i.id)  FROM kw_edu_info i left join kw_shcool_eduzy zy  on i.id = zy.zid   WHERE    i.open = '1' ".$where."   GROUP BY i.id  ) a";
                }else{
                    $zonsql = " SELECT count(*) as num from ( SELECT  count(i.id)  FROM kw_edu_info i   WHERE    i.open = '1' ".$where."   GROUP BY i.id  ) a";
                }
            }else{
                $zonsql = " SELECT  count(i.id) as num  FROM kw_edu_info i   WHERE    i.open = '1' ".$where;
            }
            $data1 =self::query($zonsql)  ;
            if($data1){
                return $data1[0]['num'];
            }
            return 0;
        }

        if(!empty($_GET['type2'])){
            if($wheretag){
                $sql = "SELECT i.id,i.titlename,i.lilun,i.alclass,i.type,i.hb FROM kw_edu_info i left join kw_shcool_eduzy zy  on i.id = zy.zid   WHERE    i.open = '1' ".$where."   GROUP BY i.id    order by i.benke desc , i.yjs desc , i.fuxiu desc ".$limit;
            }else{
                $sql = "SELECT i.id,i.titlename,i.lilun,i.alclass,i.type,i.hb FROM kw_edu_info i  WHERE   i.open = '1' ".$where."   GROUP BY i.id    order by i.benke desc , i.yjs desc , i.fuxiu desc ".$limit;
            }

        }else{
            $sql = "SELECT i.id,i.titlename,i.lilun,i.alclass,i.type,i.hb FROM kw_edu_info i    WHERE   i.open = '1' ".$where."   order by i.benke desc , i.yjs desc , i.fuxiu desc ".$limit;
        }



        $data =self::query($sql);

        foreach ($data as $key => $value) {
            $data[$key]['colleges']= count(self::get_school_num('',$value['id'],0,$tagid));
//            if($data[$key]['colleges']==0){
//                unset($data[$key]);
//                continue;
//            }
            $closer = self::majorCloser($value,$value['id']);
            $data[$key]['closer']= $closer? count($closer):0;

        }
        return $data;
    }

    /**
     * 推荐专业
     * @param $schoolid
     * @param $where
     * @param $tagid
     * @param $wheretag
     * @param string $limit
     * @return int|mixed
     * @throws BindParamException
     * @throws PDOException
     */
    public static function search_major_recom($schoolid, $where, $tagid, $wheretag, $limit=''){


        $gaokaodiquid = self::get_district($schoolid);

        if($limit==''){
            if(!empty($_GET['type2'])){
                $zonsql = " SELECT count(*) as num from ( SELECT  count(i.id)  FROM kw_edu_info i left join kw_shcool_eduzy zy  on i.id = zy.zid LEFT JOIN kw_shcool_eduzy_info zyin on zy.id =zyin.eduid     WHERE  zyin.cityid ={$gaokaodiquid} and   i.open = '1' ".$where."   GROUP BY i.id  ) a";
            }else{
                $zonsql = " SELECT  count(i.id) as num  FROM kw_edu_info i   WHERE    i.open = '1' ".$where;
            }
            $data1 =self::query($zonsql)  ;
            if($data1){
                return $data1[0]['num'];
            }
            return 0;
        }

        if(!empty($_GET['type2'])){
            $sql = "SELECT i.id,i.titlename,i.lilun,i.alclass,i.type,i.hb FROM kw_edu_info i left join kw_shcool_eduzy zy  on i.id = zy.zid LEFT JOIN kw_shcool_eduzy_info zyin on zy.id =zyin.eduid     WHERE   zyin.cityid ={$gaokaodiquid} and  i.open = '1' ".$where."   GROUP BY i.id    order by i.benke desc , i.yjs desc , i.fuxiu desc ".$limit;

        }else{
            $sql = "SELECT i.id,i.titlename,i.lilun,i.alclass,i.type,i.hb FROM kw_edu_info i    WHERE   i.open = '1' ".$where."   order by i.benke desc , i.yjs desc , i.fuxiu desc ".$limit;
        }



        $data =self::query($sql);

        foreach ($data as $key => $value) {
            if($value['type'] ==1){
                $data[$key]['colleges']= count(self::get_school_num('',$value['id'],0,$tagid));
            }else{
                $data[$key]['colleges']= count(self::get_school_num('',$value['id'],$gaokaodiquid,$tagid));
            }

            if($data[$key]['colleges']==0){
                unset($data[$key]);
                continue;
            }
            $closer = self::majorCloser($value,$value['id']);
            $data[$key]['closer']= $closer? count($closer):0;

        }
        return $data;
    }

    /**    // 3+3 开设院校
     * @param $uid
     * @param $schoolid
     * @param $where
     * @param $limit
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function  elective( $schoolid, $where, $limit=''){
        $cityid = self::get_district($schoolid);

        if($limit==''){
            $sql2 = " SELECT count(*) as num FROM (SELECT count(*) as num FROM kw_shcool_eduzy_info a LEFT JOIN kw_edu_info  c on a.zid = c.id where c.open = '1' and  a.cityid = {$cityid} ".$where."  GROUP BY  zid,a.lilun ) a ";
            $data2 =  self::query($sql2);
            if($data2){
                return $data2[0]['num'];
            }
            return 0;
        }
        $sql = "SELECT count(*) as num, c.id, c.titlename , a.lilun FROM kw_shcool_eduzy_info a LEFT JOIN kw_edu_info c on a.zid = c.id where c.open = '1' and  a.cityid = {$cityid}  ".$where."  GROUP BY  zid,lilun ".$limit;


        $data = self::query($sql);

        foreach ($data as $key => $value) {
            $data[$key]['colleges'] =count(self::get_school_num('',$value['id'],$cityid,'',$where,'',$value['lilun']));
        }
        return $data;
    }
    /**
     * 获取专业详情
     * @param $id
     * @param $status
     * @return bool
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function majorDetails($id, $status){
            $data= self::name('edu_info')->whereid($id)->where('open',1)->find();
            if(!$data){
                return false;
            }
            $eduClassdata = Caches::EudClassAll();
            $data['cidname'] = empty($eduClassdata[$data->cid]) ?'':$eduClassdata[$data->cid];
            $data['didname'] = empty($eduClassdata[$data->did]) ?'':$eduClassdata[$data->did];
            // 相关zhiye
            $data['profession']='';
            if($status ==1 ||  $status ==2) {
                $data['profession'] = self::name( 'anli_search_z' )->alias( 't1' )->leftJoin( 'anli_search t2', 't1.soid = t2.id' )->where( 't1.zid', $id )->field( 't2.id,t2.titlename' )->select();
            }
            $data['closer']= self::majorCloser($data,$id);

            return $data;
    }


    /**
     * 专业发展报告
     * @param $id
     * @return bool
     */
    public static function major_report($id,$rankid)
    {
        if(!empty($id)){
            $data= self::name('edu_info')->whereid($id)->where('open',1)->find();
        }else{
            $data= self::name('edu_info')->where('rankid',$rankid)->where('open',1)->find();
        }
        if(!$data){
            return false;
        }
        $res = self::name('edu_info')->where(self::raw("FIND_IN_SET({$data['did']},did)"))->where('open',1)->column('id');
        $result['edus']  = implode('_',$res);
        $eduClassdata = Caches::EudClassAll();
        $result['cidname'] = empty($eduClassdata[$data->cid]) ?'':$eduClassdata[$data->cid];
        $data2 = Caches::EudClassTpid($data['did']);
        $result['lilun'] = $data['lilun'];
        $result['da'] = $data['da'];
        $result['type'] = $data['type'];
        $result['content'] = $data['content'];
        $result['alclass'] = empty($data['alclass'])?'无':$data['alclass'];
        $result['didname'] = $data2['tpname'];
        $result['difficulty'] = $data2['difficulty'];
        $result['job'] = $data2['job'];
        $result['overview'] = $data2['overview'];
        $result['coach'] = $data2['coach'];
        $result['occupation'] = $data2['occupation'];
        $result['capacity'] = $data2['capacity'];
        if(empty($data2['relevance'])){
            $result['relevance']='';
        }else{
            $relevance = array_filter(explode(',',$data2['relevance']));
            $result['relevance'] =self::name('edu_class')->whereIn('tpid',$relevance)->column('tpname');
        }

//        $data3 = self::name('subject_news')->whereIn('classid',$subjects)->where('open',1)->field('intro,id,pic,datetime,hits,source,titlename')->order('datetime','desc')->limit(3)->select();

        $sql="SELECT intro,id,pic,datetime,hits,source,titlename FROM kw_subject_news  where open =1 and   FIND_IN_SET(".$data['did'].",educlass) order by datetime desc limit 3";
        $data3 = self::query($sql);
        foreach ($data3 as $key => $value) {
            $data3[$key]['hits'] = $value['hits']+self::hits_nums($value['datetime']);
            $data3[$key]['datetime'] = date('Y-m-d',$value['datetime']);
            $data3[$key]['num'] = self::name('subject_like')->where('tpid',$value['id'])->count() + ceil($data3[$key]['hits']/13) ;
        }
        $result['subject_new'] = $data3;

        return $result;
    }

    private static function hits_nums($time){
        return  ceil((time() - $time)/600);

    }

    //相近专业
    private static  function majorCloser($data,$id){
        if(!empty($data['hb'])){
            $hbs = explode(',',$data['hb']);
            if(count($hbs) ==1 && $data['hb'] ==$id){
                return '';
            }
            $data2= self::name('edu_info')->where('id','neq',$id)->whereIn('id',$hbs)->where('open',1)->field('id,titlename')->select();

            return $data2;
        }
        return '';
    }
    /**添加收藏学校
     * @param $uid
     * @param $schoolid
     * @return int|string
     */
    public static function add_school_sc($uid, $schoolid){
        $data = self::name('school_user_sc')->where('schoolid',$schoolid)->where('uid',$uid)->value('id');
        if($data){
            $res = self::name('school_user_sc')->whereid($data)->delete();
            if($res){
                $insertdata =['type'=>1,'uid'=>$uid,'pid'=>$schoolid,'datetime'=>time()];
                self::name('del_sc_log')->insert($insertdata);
            }
            if($res){
                Caches::SchoolUserScId($uid,1);
            }
            return $res ;
        }else{
            $insertdata =['uid'=>$uid,'schoolid'=>$schoolid,'datetime'=>time()];
            $res= self::name('school_user_sc')->insert($insertdata);

            if($res){
                Caches::SchoolUserScId($uid,1);
            }
            return $res;
        }

    }

    /**添加收藏专业
     * @param $uid
     * @param $eid
     * @return int|string
     */
    public static  function add_user_edu_sc($uid, $eid){

        $eduscdata = Caches::EduUserScId($uid,1);
        if(in_array($eid,$eduscdata)){

            $type = self::name('user_edu_sc')->where('eid',$eid)->where('uid',$uid)->delete();
            if($type){
                $insertdata = ['type'=>2,'uid'=>$uid,'pid'=>$eid,'datetime'=>time()];
                self::name('del_sc_log')->insert($insertdata);
            }
            if($type){
                Caches::EduUserScId($uid,1);
            }
            return $type ;
        }else{
            if(count($eduscdata)>24){
                return 'duo';
            }
            $data =self::name('edu_info')->whereid($eid)->field('lx,cid,did')->find();
            $insertdata  = ['uid'=>$uid,'cid'=>$data['cid'],'did'=>$data['did'],'tbid'=>$data['lx'],'eid'=>$eid,'datetime'=>time()];
            $res = self::name('user_edu_sc')->insert($insertdata);
            if($res){
                Caches::EduUserScId($uid,1);
            }
            return $res;

        }

    }

    /**
     *     //收藏专业 llist
     * @param $uid
     * @param $schoolid
     * @param $cids
     * @return $this
     */
    public static  function collection($uid, $schoolid, $cids){

        $cityid = self::get_district($schoolid);
        $result =self::name('user_edu_sc')
            ->alias('t1')
            ->leftJoin('edu_info t2','t1.eid = t2.id')
            ->where('t1.uid',$uid)->whereIn('t2.cid',$cids)
            ->field('t1.eid,t1.datetime,t2.titlename,t2.type,t2.lilun')
            ->order('t1.datetime','desc')->select();

        foreach ($result as $key => $value) {
            $result[$key]['datetime'] =empty($value['datetime'])? '':date("Y-m-d",$value['datetime']);
            $res_policy= Cache::get('Navigationa:policy:uid:'.$uid.':eid'.$value['eid']);
            if(!$res_policy){
                $res_policy = Navigationa::policy( $uid, $value['eid'] );
                Cache::set('Navigationa:policy:uid:'.$uid.':eid'.$value['eid'],$res_policy,CACHE_DATA_TIME);
            }
            $result[$key]['policy'] =$res_policy['policy'];
            $result[$key]['tages'] = $res_policy['tages'];
        }
        return $result;

    }


    /** //收藏学校
     * @param $uid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function get_school_sc($uid){
        $areadata  = Caches::AreaAll();
        $data = self::name('school_user_sc')
            ->alias('t1')
            ->leftJoin('shcool t2','t1.schoolid = t2.id')
            ->where('t1.uid',$uid)
            ->field('t2.id,t2.pic,t2.tagid,t2.scid,t2.sdid,t2.titlename')
            ->order('t1.datetime','desc')
            ->select();
        if(!$data){
            return null;
        }
        foreach ($data as $key => $value) {
            $value['pic']  ||  $data[$key]['pic'] = "/images/xueye/zanwu.png";
            $data[$key]['scname']=empty( $areadata[$value['scid']])?'': $areadata[$value['scid']];
            $data[$key]['sdname']= empty( $areadata[$value['sdid']])?'': $areadata[$value['sdid']];
            $data[$key]['tagid']=Libs::checkleiArr($value['tagid'],'class_class','id','tpname',',');
        }
        return $data;
    }

    //三加三
    public static function aggre_gate($schoolid,$eid){
        $cityid= self::get_district($schoolid);

        $sql = "SELECT COUNT( a.id ) AS targe ,  a.lilun FROM kw_shcool_eduzy_info a LEFT JOIN kw_edu_info b on a.zid = b.id where a.cityid = {$cityid} and a.zid = {$eid}   GROUP BY  `lilun` ORDER BY  `lilun` DESC";

        $result = self::query($sql);

        $num = self::name('shcool_eduzy_info')->where('zid',$eid)->where('cityid',$cityid)->count();
        foreach ($result as $key => $value) {
            $result[$key]['targe'] = round(($value['targe']/$num)*100,2);
        }
        $data['titlename'] = self::name('edu_info')->whereid($eid)->value('titlename');
        $Areadata = Caches::AreaAll();
        $data['addr'] =$Areadata[$cityid];
        $data['ation'] = $result;
        return $data;

    }


}

