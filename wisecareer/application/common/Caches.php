<?php
namespace app\common;
use app\common\model\Anli_class;
use app\common\model\Anli_search;
use app\common\model\Area;
use app\common\model\Class_class;
use app\common\model\Lx_class;
use app\common\model\Myuser;
use app\common\model\Shcool;
use app\common\model\Shcool_class_zx;
use app\common\model\V_txclass;
use Db;
use think\exception\DbException;
use think\facade\Cache;

/**
 * Class Myuser   缓存类
 * @package app\common\model
 */
class Caches
{
    /**性格leixing xing
     * @param $uid
     * @return mixed
     */
    public static function Xingall($ref = 0){
        $data = Cache::get('Xing:all');
        if(!$data || $ref==1){
            $data  = Db::name('xing')->column('titlename','daima');
            Cache::set('Xing:all',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }

    /**
     * 选科
     * @param $uid
     * @return mixed
     */
    public static function EduGkClass($ref = 0){
        $data = Cache::get('EduGkClass:all');
        if(!$data || $ref==1){
            $data  = Db::name('edu_gk_class')->column('tpname','tpid');
            Cache::set('EduGkClass:all',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }
    /**school_set
     * @param $uid
     * @return mixed
     */
    public static function SchoolSetId($shcoolid,$ref = 0){
        $data = Cache::get('SchoolSet:Id:'.$shcoolid);
        if(!$data || $ref==1){
            $data  = Db::name('school_set')->where('classid',$shcoolid)->find();
            Cache::set('SchoolSet:Id:'.$shcoolid,$data,CACHE_DATA_TIME);
        }
        return  $data;
    }

    /**兴趣缓存 edu_ld
     * @param $uid
     * @return mixed
     */
    public static function EduLd($ref = 0){
        $data = Cache::get('EduLd:all');
        if(!$data || $ref==1){
            $data  = Db::name('edu_ld')->column('tpname','tpid');
            Cache::set('EduLd:all',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }
    /**科目 缓存 edu_lilun
     * @param $uid
     * @return mixed
     */
    public static function EduLilun($ref = 0){
        $data = Cache::get('EduLilun:all');
        if(!$data || $ref==1){
            $data  = Db::name('edu_lilun')->column('tpname','tpid');
            Cache::set('EduLilun:all',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }
    /**决策树缓存
     * @param $uid
     * @return mixed
     */
    public static function EduWenSrUid($uid,$ref = 0){
        $data = Cache::get('EduWenSrUid:'.$uid.':all');
        if(!$data || $ref==1){
            $data  = Db::name('edu_wen_sr')->where('uid',$uid)->find();
            Cache::set('EduWenSrUid:'.$uid.':all',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }
    /**收藏所有专业
     * @param $uid
     * @return mixed
     */
    public static function EduUserScId($uid,$ref = 0){
        $data = Cache::get('EduUserScId:'.$uid.':Id');
        if(!$data || $ref==1){
            $data  = Db::name('user_edu_sc')->where('uid',$uid)->column('eid');
            Cache::set('EduUserScId:'.$uid.':Id',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }

    /**收藏所有学校ID
     * @param $uid
     * @return mixed
     */
    public static function SchoolUserScId($uid,$ref = 0){
        $data = Cache::get('SchoolUserScId:'.$uid.':Id');
        if(!$data || $ref ==1){
            $data  = Db::name('school_user_sc')->where('uid',$uid)->column('schoolid');
            Cache::set('SchoolUserScId:'.$uid.':Id',$data,CACHE_DATA_TIME);
        }
        return  $data;
    }

    /**
     * 类别 缓存
     * @param $uid
     * @param string $viptype
     * @return int|mixed|string
     */
    public static function ClassClassKeyVal($cids){
        $key = implode($cids);
        $data = Cache::get('ClassClassKeyVal:'.$key.':tpname');
        if(!$data){
            $data = Class_class::scope('cidtpnameid',$cids)->select();
            Cache::set('ClassClassKeyVal:'.$key.':tpname',$data,CACHE_DATA_TIME);
        }

        return $data;
    }
    /**
     * 类别 缓存
     * @param $uid
     * @param string $viptype
     * @return int|mixed|string
     */
    public static function ClassClass($cid =''){
        if($cid ==''){
            $data = Cache::get('ClassClass:all:tpname');
            if(!$data){
                $data = Class_class::scope('AllTpname')->column('tpname','id');
                Cache::set('ClassClass:all:tpname',$data,CACHE_DATA_TIME);
            }
        }else{
            $data = Cache::get('ClassClass:'.$cid.':tpname');
            if(!$data){
                $data = Class_class::scope('CidTpname',$cid)->column('tpname','id');
                Cache::set('ClassClass:'.$cid.':tpname',$data,CACHE_DATA_TIME);
            }
        }
        return $data;
    }
    /**
     * 学夜E  用户身份状态 缓存
     * @param $uid
     * @param string $viptype
     * @return int|mixed|string
     */
    public static function MyuserXyStatus($uid, $viptype='',$ref=0){
        $data = Cache::get('Myuser:'.$uid.':MyuserXyStatus');
        if(!$data || $ref==1){
            $data = Myuser::myuserXyStatus($uid,$viptype);
            Cache::set('Myuser:'.$uid.':MyuserXyStatus',$data,CACHE_STATUS_TIME);
        }
        return $data;
    }

    /**
     * 职拓  用户身份状态 缓存
     * @param $uid
     * @param string $viptype
     * @return int|mixed|string
     */
    public static function MyuserZtStatus($uid, $viptype='',$ref=0){
        $data = Cache::get('Myuser:'.$uid.':MyuserZtStatus');
        if(!$data || $ref==1){
            $data = Myuser::myuserZtStatus($uid,$viptype);
            Cache::set('Myuser:'.$uid.':MyuserZtStatus',$data,CACHE_STATUS_TIME);
        }
        return $data;
    }

    /**
     * 个人短信息缓存缓存
     * @param $uid
     * @return mixed
     */
    public static function MyuserInfoFiled($uid ,$ref=0){
        $data = Cache::get('Myuser:'.$uid.':MyuserInfoFiled');
        if(!$data || $ref==1){
            $data = Myuser::whereid($uid)->field('password,password_od',true)->find();
            Cache::set('Myuser:'.$uid.':MyuserInfoFiled',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 个人信息缓存
     * @param $uid
     * @return mixed
     */
    public static function MyuserInfo($uid,$ref=0){
        $data = Cache::get('Myuser:'.$uid.':MyuserInfo');
        if(!$data || $ref ==1 ){
            $data = Myuser::whereid($uid)->field('password,password_od',true)->find();
            if(empty($data) ){
                return null;
            }
            if(empty($data['schoolname'] )|| $data['schoolname']==0){
                $data['school_name'] ='';
            }else{
                $schooldata=self::SchoolAll();
                if($schooldata){
                    $data['school_name'] = empty($schooldata[$data['schoolname']])?'':$schooldata[$data['schoolname']];
                }else{
                    $data['school_name'] = Shcool::whereid($data['schoolname'])->value('titlename');
                }
            }
            if(empty($data['scid']) || $data['scid']==0){
                $data['sd_name'] ='';
                $data['sc_name']='';
            }else{
                $areadata = self::AreaAll();
                if($areadata){
                    $data['sd_name'] = empty($areadata[$data['sdid']])?'':$areadata[$data['sdid']];
                    $data['sc_name'] = empty($areadata[$data['scid']])?'':$areadata[$data['scid']];
                }else{
                    $data['sc_name'] = Area::where('ar_id',$data['scid'])->value('ar_name');
                    $data['sd_name'] = Area::where('ar_id',$data['sdid'])->value('ar_name');
                }
            }

            if(empty($data['classname'])||!$data['classname']){
                $data['classname_name']='';
                $data['classroom_name']='';
            }else{
                $classdata =self::Shcool_class_zxAll();
                if($classdata){
                    $data['classname_name'] = empty($classdata['class'][$data['classname']])?'':$classdata['class'][$data['classname']];
                    $data['classroom_name'] = empty($classdata['room'][$data['classroom']])?'':$classdata['room'][$data['classroom']];

                }else{
                    $data['classname_name'] =Shcool_class_zx::getClass($data['classname']);
                    $data['classroom_name'] =Shcool_class_zx::getRoom($data['classroom']);
                }
            }

            Cache::set('Myuser:'.$uid.':MyuserInfo',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 年级 班级缓存 key =>value
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function Shcool_class_zxAll(){
        $data = Cache::store('file')->get('Shcool_class_zxAll:Column');
        if(!$data){
            $data['class'] = Shcool_class_zx::getClassAll();
            $data['room']  = Shcool_class_zx::getRoomAll();

            Cache::store('file')->set('Shcool_class_zxAll:Column',$data,CACHE_DATA_TIME*24);
        }
        return $data;
    }
    /**
     * 学校缓存 key =>value
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function SchoolAll(){
        $data = Cache::store('file')->get('SchoolAll:Column');

        if(!$data){
            $data = Shcool::scope('AllTitle')->column('titlename','id');
            Cache::store('file')->set('SchoolAll:Column',$data,CACHE_DATA_TIME*CACHE_DATA_TIME*24);
        }
        return $data;
    }
    /**
     * 地区缓存 key =>value
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function AreaAll(){
        $data = Cache::store('file')->get('AreaAll:Column');
        if(!$data){
            $data = Area::all()->column('ar_name','ar_id');
            Cache::store('file')->set('AreaAll:Column',$data,CACHE_DATA_TIME*CACHE_DATA_TIME*24);
        }
        return $data;
    }


    /**
     * 地区400缓存 key =>value
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function AreaPhonesAll(){
        $data = Cache::store('file')->get('AreaPhonesAll:Column');
        if(!$data){
            $data = Area::all()->column('phones','ar_id');
            Cache::store('file')->set('AreaPhonesAll:Column',$data,CACHE_DATA_TIME*CACHE_DATA_TIME*24);
        }
        return $data;
    }

    /**
     * 地区缓存 order
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function AreaOrder($scid =0){
        $data = Cache::store('file')->get('AreaOrder:all');
        if(!$data){
            $data = Area::getArOrder($scid);
            Cache::store('file')->set('AreaOrder:all',$data,CACHE_DATA_TIME*CACHE_DATA_TIME*24);
        }
        return $data;
    }

    /**
     * edu_class   redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  EudClassAll(){
        $data = Cache::get('EudClassAll:Tpname');
        if(!$data){
            $data = Db::name('edu_class')->column('tpname','tpid');
            Cache::set('EudClassAll:Tpname',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * edu_class id   redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  EudClassTpid($tpid){
        $data = Cache::get('EudClassTpid:All:'.$tpid);
        if(!$data){
            $data = Db::name('edu_class')->where('tpid',$tpid)->find();
            Cache::set('EudClassAll:All:'.$tpid,$data,CACHE_DATA_TIME);
        }
        return $data;
    }
    /**
     * 职业缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  AnliSearch(){
        $data = Cache::get('AnliSearch:AllTitlename');
        if(!$data){
            $data = Anli_search::all()->column('titlename','id');
            Cache::set('AnliSearch:AllTitlename',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 行业所对应的职业 缓存
     * @param $tpid
     * @return array|mixed|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function AnliSearchHangye($tpid){

        $data = Cache::get('AnliSearchHangye:'.$tpid.':AllTitlename');
        if(!$data){
            $data = Anli_search::scope('AnliSearchHangye',$tpid)->select();
            Cache::set('AnliSearchHangye:'.$tpid.':AllTitlename',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 行业所对应的职业 缓存
     * @param $tpid
     * @return array|mixed|\PDOStatement|string|\think\Collection
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function AnliSearchLx($id){

        $data = Cache::get('AnliSearchLx:'.$id.':AllTitlename');
        if(!$data){
            $data = Anli_search::scope('AnliSearchLx',$id)->select();
            Cache::set('AnliSearchLx:'.$id.':AllTitlename',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 家庭环境调研题 缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  vWrite(){
        $data = Cache::get('vWrite:AllLx');
        if(!$data){
            $data = Db::name('v_write')->column('lx','id');
            Cache::set('vWrite:AllLx',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 家庭环境调研题 选项 缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  vTiWrite(){
        $data = Cache::get('vTiWrite:AllLx');
        if(!$data){
            $data = Db::name('v_ti_write')->column('lx','id');
            Cache::set('vTiWrite:AllLx',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 行业 缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  anliClass(){
        $data = Cache::get('anliClass:AllTpname');
        if(!$data){
            $data = Anli_class::whereNotIn('tpid',['5','41'])->field('tpname,tpid')->select()->toArray();
            Cache::set('anliClass:AllTpname',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 只能 全部  全部  缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  LxKeyClass(){
        $data = Cache::get('LxKeyClass:AllTpname');
        if(!$data){
            $data = Lx_class::where('openid',1)->field('tpname,id')->select()->toArray();
            Cache::set('LxKeyClass:AllTpname',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 行业 全部  缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  HangYeClass(){
        $data = Cache::get('HangYeClass:AllTpname');
        if(!$data){
            $data = Anli_class::where('openid',1)->field('tpname,language,tpid,pic')->select()->toArray();
            Cache::set('HangYeClass:AllTpname',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 只能 全部  全部  缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  LxClass(){
        $data = Cache::get('LxClass:AllTpname');
        if(!$data){
            $data = Lx_class::where('openid',1)->field('tpname,language,id,pic')->select()->toArray();
            Cache::set('LxClass:AllTpname',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

    /**
     * 专题全部  缓存  redis
     * @param $uid
     * @param $type
     * @return int|string
     */
    public static function  ZhuanTiClass(){
        $data = Cache::get('ZhuanTiClass:AllTpname');
        if(!$data){
            $data = V_txclass::where('openid',1)->field('tpname,language,id,pic')->select()->toArray();
            Cache::set('ZhuanTiClass:AllTpname',$data,CACHE_DATA_TIME);
        }
        return $data;
    }

}

