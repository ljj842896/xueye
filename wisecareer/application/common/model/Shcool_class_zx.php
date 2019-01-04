<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Shcool_class_zx extends Model
{
    /**
     *
     */
    protected static function init()
    {
    }
    /**
     * 获取年级
     * @param $class_id
     * @return mixed
     */
    public static function getClass($class_id){
        return self::where('class_id',$class_id)->where('class_type',1)->value('class_name');
    }
    /**
     * 获取班级
     * @param $class_id
     * @return mixed
     */
    public static function getRoom($class_id){
        return self::where('class_id',$class_id)->where('class_type',2)->value('class_name');
    }

    /**
     * 年级
     * @param $query
     * @return mixed
     */
    public static function getClassAll()
    {
        return self::where('class_type',1)->column('class_name','class_id');

    }

    /**
     * 班级
     * @param $query
     * @return mixed
     */
    public static function getRoomAll()
    {
        return self::where('class_type',2)->column('class_name','class_id');
    }

}

