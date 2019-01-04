<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Class_class extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * all id=>tpname
     * @param $query
     */
    public function scopeAllTpname($query)
    {
        $query->field('id,tpname');
    }

    /**
     * cid  id=>tpname
     * @param $query
     * @param $cid
     */
    public function scopeCidTpname($query, $cid)
    {
        $query->where('cid',$cid);
    }

    /**
     * cid  id=>tpname
     * @param $query
     * @param $cid
     */
    public function scopeCidTpnameId($query, $cid)
    {
        $query->whereIn('cid',$cid)->field('cid,tpname,id')->order('tbid','asc');
    }

}

