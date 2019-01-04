<?php

namespace app\common\model;

use app\common\Caches;
use think\Model;

/**
 * Class Myuser  行业动态
 * @package app\common\model
 */
class HangyeDynamic extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }


    /**
     * Adds a dynamic.
     *
     * @param      array   $array  The array
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function add_dynamic($array){
        $array['datetime']  = $this->time;
        $res = $this->has($this->table,['id'=>$array['id']]);


        if($res){
            return $this->update($this->table,$array,['AND'=>['id'=>$array['id'],'uid'=>$array['uid']]]);
        }else{
            unset($_POST['id']);
            $message = new app_messageModel();
            $apmsg = jife(13,$array['uid']);
            $message->release($array['uid'],13);
            return  $this->insert($this->table,$array);

        }


    }

    /**
     * { function_description }
     *
     * @param      <type>  $uid    The uid
     *
     * @return     <type>  ( description_of_the_return_value )
     */

    public  static  function sel_dynamic($uid){
        return self::where('uid',$uid)->order('datetime','desc')->select();
    }
    /**
     * Gets the dynamic.
     *
     * @param      <type>  $where  The where
     *
     * @return     <type>  The dynamic.
     */
    public static function get_dynamic($where){
        return self::where($where)->find();
    }
    /**
     * { function_description }
     *
     * @param      <type>  $where  The where
     *
     * @return     <type>  ( description_of_the_return_value )
     */

    public  function del_dynamic($where){
        return $this->delete($this->table,['AND'=>$where]);
    }

    /**
     * 行业动态推荐
     */
    public static function Recommend_new($uid,$scid){
        if( !$scid){
            $scid = 0;
        }
        $sql = "SELECT titlename,intro,datetime,source,pic,id FROM kw_subject_news WHERE open = 1   and ( scid =0 or FIND_IN_SET({$scid},scid) ) ORDER BY datetime DESC LIMIT 4 ";

        $data = self::query($sql);

        foreach ($data as $key => $value) {
            $data[$key]['pic'] ="http://pic.wisecareer.org/".$value['pic'];
            $data[$key]['titlename'] = '【'.$value['titlename'].'】';
        }

        return $data;
    }

    /**
     * @param $classid
     * @param $scid
     * @param int $limit
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function sel_hangye_new($hangye, $lx , $scid, $limit=0){
        //$result = self::subject_num($hangye);
        if($hangye){
            $sql = "SELECT source,titlename,intro,datetime,hits,pic,id FROM kw_subject_news WHERE open = 1 and  FIND_IN_SET({$hangye},hyclass) and ( scid =0  or FIND_IN_SET({$scid},scid) )  order by datetime desc ".$limit;
        }elseif($lx){
            $sql = "SELECT source,titlename,intro,datetime,hits,pic,id FROM kw_subject_news WHERE open = 1 and  FIND_IN_SET({$lx},lx) and ( scid =0  or FIND_IN_SET({$scid},scid) )  order by datetime desc ".$limit;
        }else{
            return false;
        }


        $data =self::query($sql);
        foreach ($data as $key => $value) {
            $data[$key]['pic'] = 'http://pic.wisecareer.org/'.$value['pic'];
            $data[$key]['datetime'] = date('Y-m-d' ,$value['datetime']);
            $data[$key]['hits'] = $value['hits']+self::hits_nums($value['datetime']);
            $data[$key]['num'] = ceil($data[$key]['hits']/13) ;
            // self::name('subject_like')->where('tpid',$value['id'])->count()  +
        }
        $result['data']  = $data;
        return $result;
    }

    /**
     * 只能动态
     * @param $lx
     * @param $scid
     * @param int $limit
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function sel_lx_new($lx, $scid, $limit=0){
        //$result = self::subject_num($hangye);
        $sql = "SELECT source,titlename,intro,datetime,hits,pic,id FROM kw_subject_news WHERE open = 1 and  lx ={$lx} and ( scid =0 or FIND_IN_SET({$scid},scid) )  order by datetime desc ".$limit;
        $data =self::query($sql);
        foreach ($data as $key => $value) {
            $data[$key]['pic'] = 'http://pic.wisecareer.org/'.$value['pic'];
            $data[$key]['datetime'] = date('Y-m-d' ,$value['datetime']);
            $data[$key]['hits'] = $value['hits']+self::hits_nums($value['datetime']);
            $data[$key]['num'] = ceil($data[$key]['hits']/13) ;
            // self::name('subject_like')->where('tpid',$value['id'])->count()  +
        }
        $result['data']  = $data;
        return $result;
    }


    private static function hits_nums($time){
        return  ceil((time() - $time)/600);
    }


    /**
     * d行业动态详情
     * @param $id
     * @param $uid
     * @return mixed
     */
    public static function get_hangye_news($id, $uid){
        $data =self::name('subject_news')->whereid($id)->where('open',1)->field('id,titlename,source,cuid,content,hits,pic,datetime')->find();
        if($data == null) return '';
       // $EduLilundata = Caches::EduLilun();

      //  $array = self::where('uid',$uid)->where('del',0)->column('classid');

        $data['pic'] =  'http://pic.wisecareer.org/'.$data['pic'];
        $data['hits'] =self::hits_nums($data['datetime']) +$data['hits'] ;
        $data['datetime'] = date('Y-m-d' ,$data['datetime']);
        $data['num'] = self::name('subject_like')->where('tpid',$data['id'])->count();
        $data['has'] =self::name('subject_like')->where('tpid',$id)->where('uid',$uid)->count();

        //$result = self::subject_num($data['classid']);

        $result['data']  = $data;
        return $result;
    }

    //动态 流量量
    public static function hangye_hitsnum($id){
        return self::name('subject_news')->whereid($id)->inc('hits')->update();
    }

    /**
     * 点赞 动态
     * @param $tpid
     * @param $uid
     * @return int|string
     */
    public static function hangye_like_add($tpid, $uid){
        $has = self::name('subject_like')->where('uid',$uid)->where('tpid',$tpid)->find();
        if($has){
            return self::name('subject_like')->where('uid',$uid)->where('tpid',$tpid)->delete();
        }else{
            $insertdata = ['uid'=>$uid,'tpid'=>$tpid,'datetime'=>time()];
            return self::name('subject_like')->data($insertdata)->insert();

        }
    }
}

