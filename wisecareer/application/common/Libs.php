<?php

namespace app\common;

use Db;
use think\facade\Cache;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Libs
{
    private $time ;
    /**
     * Log constructor.
     */
    public  function __construct()
    {
        $this->time= time();
    }

    /** 分割 字串 取数组
     * @param $tagid
     * @param $table
     * @param $id
     * @param $tpname
     * @param $division
     * @return string
     */
    public static function checkleiArr($str, $table, $wherefield, $field, $division){
        if(!empty($str)){
            $key = 'checkleiArr'.md5($str.$table.$wherefield.$field.$division);
            $data = Cache::get($key);

            if(!$data){
                $tagdis = explode($division, $str);
                $data  = Db::name($table)->whereIn($wherefield,$tagdis)->column($field);
                Cache::set($key,$data,CACHE_DATA_TIME);
            }
            return $data;
        }else{
            return '';
        }

    }

}

