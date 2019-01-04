<?php

namespace app\common\model;
use app\common\Caches;
use Db;
use think\Cache;
use think\Model;

/**
 * Class Myuser   家庭信息采集
 * @package app\common\model
 */
class User_write extends Model
{

    /**
     *
     */
    protected static function init()
    {

    }



    /**
     * 	//需要采集的家庭数据
     * @param $page
     * @param $uid
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wirteHomeList($page, $uid)
    {

        $data = $this->name('v_write')
            ->where('open',1)
            ->order('px','asc')
            ->field('id,titlename,lx,px')
            ->limit($page)
            ->select();
        if(!$data){
            return false;
        }
        $data = $data->toArray();
        $tids = array_column($data, 'id');
        // 用户采集的数据
        $data1 = $this->name('user_write')
            ->where('uid',$uid)
            ->wherein('tid',$tids)
            ->field('tid,da,type,qita')
            ->select();

        if($data1){
            $data1 = $data1->toArray();
            $usertid = array_column($data1, 'tid');
            $userdatas = array_combine($usertid,$data1);
        }

        $tidata = $this->name('v_ti_write')
            ->wherein('tid',$tids)
            ->order('px','asc')
            ->field('id,tid,titlename,da,lx')
            ->select();
        $tidata = $tidata->toArray();
        $keys = [];
        foreach ($tidata as $one => $val) {
            $val['qita']='';
            $val['has']=0;
            if(!empty($userdatas ) ){
                if( !empty($userdatas[$val['tid']]) && $userdatas[$val['tid']]['type'] != 3 && in_array($val['id'],explode(',', $userdatas[$val['tid']]['da'])) ){
                    $val['has'] = 1;
                    if($val['lx']){
                        $val['qita'] = $userdatas[$val['tid']]['qita'];
                    }
                }
            }

            $keys[$val['tid']][] = $val;
        }

        $AnliSearch_tpname  =  Caches::AnliSearch();

        foreach ($data as $key => $value) {
            if($value['lx'] !=3){
                $data[$key]['ti'] = $keys[$value['id']];

            }else{
                $data[$key]['val'] = '点击选择';
                if(!empty($userdatas ) ){
                    if( !empty($userdatas[$val['tid']]) && $userdatas[$value['id']]['da']){
                        $tpname  = explode(',',$userdatas[$value['id']]['da']);
                        $data[$key]['val'] = empty($AnliSearch_tpname[$tpname[1]])?'':$AnliSearch_tpname[$tpname[1]];//   $this->get('anli_search','titlename',['id'=> $tpname[1] ]);;
                    }

                }
            }

        }
        return $data;
    }

    /**j信息录入
     * @param $array
     * @param $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userWirtehome($array, $uid)
    {
        //家庭环境调研题
        $ids = Caches::vWrite();

        $namedata = [];
        $qitadata =[];
        foreach ($array as $key => $value) {
            $k = preg_replace('/\d+/','',$key);
            if($k =='name'){
                $namedata[$key] = $value;
            }elseif($k =='qita'){
                $qitadata[$key] = $value;
            }
        }

        if(empty($namedata)){
            return false;
        }
        $vTiWrite = Caches::vTiWrite();

        $time = time();
        foreach ($namedata as $key => $value) {
            $insertdata = [];
            $id = str_replace("name","",$key);
            $lx = $ids[$id];
            if($lx ==1 && !empty($qitadata['qita'.$id]) ){
                if( !empty($vTiWrite[$value]) && $vTiWrite[$value] !=1 ){
                    unset($qitadata['qita'.$id]);
                }
            }
            $insertdata['uid']  =$uid;
            $insertdata['datetime']  = $time;
            $insertdata['tid']  =$id;
            if($value){
                $insertdata['da']  =$value;
            }

            $insertdata['type']  = $lx;
            $insertdata['qita']  = empty($qitadata['qita'.$id])?'':$qitadata['qita'.$id] ;

            $has = $this->where('uid',$uid)->where('tid',$id)->find();

            if($has){
                $this->where('uid',$uid)->where('tid',$id)->update($insertdata);
            }else{

                $this->create($insertdata);
            }

        }
        return true;
    }

}

