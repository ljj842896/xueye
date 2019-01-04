<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class AddtoRead extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }


    public function add_read($array){

        $has = $this->has($this->table,['AND'=>['id'=>$array['id'],'uid'=>$array['uid']]]);
        if($has){


            return $this->update($this->table,$array,['AND'=>['id'=>$array['id'],'uid'=>$array['uid']]]);
        }else{

            $message = new app_messageModel();
            $apmsg = jife(22,$array['uid']);
            $message->release($array['uid'],22);
            return $this->insert($this->table,$array);
        }

    }

    /**
     * @param $uid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function sel_read($uid){
        return self::where('uid',$uid)->order('datetime','desc')->select();
    }

    public function get_read($id){

        return $this->get($this->table,'*',['id'=>$id]);
    }


    public function del_read($where){
        $file = $this->get($this->table,'pic',['AND'=>$where]);
        @unlink ('.'.$file);
        return $this->delete($this->table,['AND'=>$where]);
    }
}

