<?php

namespace app\common\model;

use Db;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Anli_search extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * 所有职业
     * @param $query
     */
    public static function SearchAll()
    {
        return self::all()->column('titlename','id');
    }
    /**
     * 符合行业的 职业
     */
    public function scopeAnliSearchHangye($query,$tpid){
          return  $query ->where( self::raw('FIND_IN_SET('.$tpid.',hangye)'))
            ->field('id,titlename');
    }

    /**
     * 符合行业的 职业
     */
    public function scopeAnliSearchLx($query,$id){
        return  $query ->where( self::raw('FIND_IN_SET('.$id.',lxclass)'))
            ->field('id,titlename');
    }

    /**
     * 用户代言的职业
     * @param $uid
     * @return mixed
     */
    public static function get_user_search($uid){

        return self::name('app_user_search')->where('uid',$uid)->order('id','desc')->value('zhiye');
    }

    /**
     * 职业搜索
     * @param $query
     * @param $tpid
     * @return mixed
     */
    public function scopeAnliSearchHangyeJijie($query, $tpid, $jibie){
        return  $query ->where( self::raw('FIND_IN_SET('.$tpid.',hangye)'))->wherein('jibie',$jibie)
            ->field('id,titlename,jibie');
    }


    /**
     * 职业搜索
     * @param $query
     * @param $tpid
     * @return mixed
     */
    public function scopeAnliSearchLxJijie($query, $tpid, $jibie){
        return  $query ->where( self::raw('FIND_IN_SET('.$tpid.',lxclass)'))->wherein('jibie',$jibie)
            ->field('id,titlename,jibie');
    }
}

