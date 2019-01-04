<?php


namespace app\common;

use Db;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Log
{
    private $time ;

    /**
     * Log constructor.
     */
    public  function __construct()
    {
        $this->time= time();
    }
    /**
     * 用户登录日志表
     * @param $uid
     * @param $type
     * @return int|string
     */
    public  function  userlog($uid, $type){
        $inserdata=[
            'uid'=>$uid,
            'datetime'=>$this->time,
            'type'=>$type,
            'clientIP'=>getIp(),
        ];
        Db::name('myuser')->where('id',$uid)->data(['starttime'=>$this->time])->update();
        return Db::name('user_log')->insert($inserdata);
    }

}

