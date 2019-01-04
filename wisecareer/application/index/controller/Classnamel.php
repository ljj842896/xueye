<?php
namespace app\index\controller;
use app\common\Caches;
use app\common\Code;
use app\common\model\Area;
use app\common\model\Class_class;
use app\common\model\Shcool;
use app\index\controller\Basics;
use think\Db;
use think\facade\Cache;
use think\Request;
// 不需要登录的  公共 方法
class Classnamel extends Basics{


    /**
     * 班级年级
     * @return \think\response\Json
     */
    public function  roomClass( ){

        if(empty($_GET['tpid']) || intval($_GET['tpid']) ==0){
            exit;
        }

        $tpid = intval($_GET['tpid']);
        $banji =  Db::name('school_classroom')
            ->alias('a')
            ->join('shcool_class_zx zx','a.tpname = zx.class_id')
            ->where('a.tporder',$tpid)->where('zx.class_type',2)
            ->field('a.tpid,zx.class_id,zx.class_name')
            ->order('tpsx','asc')->select();


        return $this->echo_json(0,'',$banji);
    }
    /**
     * 行业搜索
     * @return object
     */
    public function industry ()
    {

        $result = Caches::anliClass();
        return $this->echo_json(0,'',$result);
    }

    /**
     * 行业搜索
     * @return object
     */
    public function industry2 ()
    {


        $result['list1'] = Caches::anliClass();
        $result['list2'] = Caches::LxKeyClass();
        return $this->echo_json(0,'',$result);
    }

    /**
     * 职业搜索
     */
    public function getoccupation()
    {
        if(empty($_GET['tpid']) && empty($_GET['id'])){
            exit;
        }
        $id = intval(input('get.id'));
        $tpid = intval(input('get.tpid'));
        $zhiyes ='';
        if($tpid){
            $zhiyes = Caches::AnliSearchHangye($tpid);
        }elseif($id){
            $zhiyes = Caches::AnliSearchLx($id);
        }
        return $this->echo_json(0,'',$zhiyes);
    }
    /**城市
     * @return \think\response\Json
     */
    public function city(){

        if(!empty($this->request->param('scid')) ){
            $scid = intval($this->request->param('scid'));
            $result = Caches::AreaOrder($scid);
        }else{
            $result = Caches::AreaOrder();
        }
        return $this->echo_json(0,'',$result);
    }
    // 中国省份
    public function  areaClass(){
        $result = Caches::AreaOrder();
        return $this->echo_json(0,'',$result);
    }

    /**
     *    //类型
     */
    public function calsstype()
    {
        if(empty($this->request->param('scid'))){
           return $this->echo_json(30004);;
        }
        if(!empty($this->request->param('scid'))){
            $cids = explode(',',$this->request->param('scid'));
            $cids = array_filter($cids);
            $data=  Caches::ClassClassKeyVal($cids);
            $result =[];
            foreach ($data as $key => $value) {
                $result[$value['cid']][] = $value;
            }
        }
        return $this->echo_json(0,'',$result);
    }


    // 专业分布类别
    public function  zyclass(){

        $result = Db::name('edu_class')->where('tporder',0)->field('tpname,tpid')->select();
        $data = array();
        foreach($result as $key=>$value) {
            $temp['tpname'] = $value['tpname'];
            $datas = Db::name('edu_class')->where('tporder',$value['tpid'])->field('tpname,tpid')->select();
            $temp['class_name'] = $datas;
            array_push($data, $temp);
        }
        return $this->echo_json(0,'',$data);
    }

    // 专业层级
    public function  cjclass(){
        $data= Db::name('class_class')->where('cid',11)->field('tpname,id')->select();
        return $this->echo_json(0,'',$data);
    }
    // 专业类型
    public function  lxclass(){
        $data= Db::name('class_class')->where('cid',6)->field('tpname,id')->select();
        return $this->echo_json(0,'',$data);
    }
    // 专业分级
    public function  fjclass(){
        $data= Db::name('class_class')->where('cid',12)->field('tpname,id')->select();
        return $this->echo_json(0,'',$data);
    }

    // 学科优势
    public function  lilunclass(){
        $data= Db::name('edu_lilun')->wherenotin('tpid',[24,30,6,31,32,7,3,28])->field('tpname,tpid')->select();
        return $this->echo_json(0,'',$data);
    }

    // 学科优势
    public function  lilunclassall(){
        $data= Db::name('edu_lilun')->wherenotin('tpid',[24,30,6,31,32,7,3,28])->field('tpname,tpid')->select();
        return $this->echo_json(0,'',$data);
    }


    // 大学类型
    public function  schoollx(){
        $data= Db::name('shcool_class')->wherenotin('tpid',[3,9])->where('tporder',0)->field('tpname,tpid')->select();
        return $this->echo_json(0,'',$data);
    }

    // 年纪
    public function  nameClass(){
        $data =  Db::name('shcool_class_zx')->where('class_type',1)->select();
        return $this->echo_json(0,'',$data);
    }
    // 专业
    public function  zhuanye(){
        $tpid = !empty($_GET['tpid'])?$_GET['tpid']:0;
        $sql ="SELECT zy.id,titlename FROM `kw_shcool_eduzy`  zy LEFT JOIN kw_edu_info  ifo on zy.zid =ifo.id  WHERE tpid ={$tpid}";
        $data = Db::query($sql);
        return $this->echo_json(0,'',$data);
    }
    //职业名称
    public function occupation(){
        if(empty($_GET['id'])|| !intval($_GET['id']) ){
            return  $this->echo_json('错误',300);
        }
        $id = intval($_GET['id']);
        $result =Db::name('anli_search')->whereid($id)->field('hangye,pic,content,titlename')->find();

        if(!empty($result)){

            $hangyeids =	explode(',',$result['hangye']);
            $data['tpnames']=	 Db::name('anli_class')->wherein('tpid',$hangyeids)->field('tpname,tpid')->select();
            unset($result['hangye']);

            $dataname = Db::name('anli_search_z')->alias('t1')->leftJoin('edu_info t2','t1.zid = t2.id')->field('t2.titlename')->wherein('t1.soid',$id)->where('t2.open',1)->select();
            $data['zynames']=$dataname;

            $result = array_merge($data,$result);
        }

        return $this->echo_json(0,'',$result);
    }


    public function testphp(){
//        set_time_limit(0);
//
//        $cursor = Db::name('myuser')->cursor();
//
//        $num = 0;
//        foreach($cursor as $myuserinfo){
//
//            if(!empty($myuserinfo['user_name']) && !empty($myuserinfo['sex']) && !empty($myuserinfo['birthday']) && !empty($myuserinfo['schoolname']) && !empty($myuserinfo['classname']) && !empty($myuserinfo['classroom'])){
//
//
//                $num++;
//            }
//
//        }


    }

}