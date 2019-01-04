<?php

namespace app\common\model;

use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Area extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }


    /**
     * @param int $cid
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getArOrder($scid=0){
        return self::where('ar_order',$scid)->order('ar_sx','asc')->field('ar_id,ar_name')->select();
    }
}

