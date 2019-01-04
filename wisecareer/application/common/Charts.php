<?php
namespace app\common;
use Db;
use think\facade\Cache;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Charts
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
    public  function electiveCombination($schoolid){
        $cityid = Db::name('shcool')->whereid($schoolid)->value('gaokaoclass');
        $cityid = $cityid==82?82:26;
        $data = Db::name('shcool_eduzy_info')->where('cityid',$cityid)->where('sid',$schoolid)->field('lilun,count(id) as num')->group('lilun')->select();
        $num = array_column($data,'num');
        $nums =  array_sum($num);
        $res  =[];
        foreach ($data as $value){
                $res[$value['lilun']]=round((($value['num']/$nums)*100),1);
        }

        return $res;
        //SELECT COUNT( * ) AS dxabus ,a.lilun FROM  a  where a.cityid = 26 and a.sid = 3 and a.lilun !=''  GROUP BY  `lilun` ORDER BY  `lilun` DESC
    }

}

