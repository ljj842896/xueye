<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class AppChat extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    private static $table1 ='app_chat_like';


    /**增加
     * @param $array
     * @return int|string
     */
    public static function add_chat($array){
        return self::insert($array);
    }

    public static function sel_chat($uid){
        return self::where('uid',$uid)->select();
    }

    public static function del_chat($array){
        return self::where($array)->delete();
    }

    public static function get_chat($uid){
        return self::where('uid',$uid)->order('id','desc')->value('content');
    }

}

