<?php
namespace app\api\controller;

use app\common\Charts;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Chart extends Action
{
    /**
     * @return mixed
     */
    public function electiveCombination ()
    {
        if(empty($_GET['schoolid'])){
            return $this->echo_json(80001);
        }

        $schoolid = intval($_GET['schoolid']);
        $CharModel = new Charts();
        $data = Cache::get('Chart:'.$schoolid.'electiveCombination');
        if(!$data){
            $data = $CharModel->electiveCombination($schoolid);
            Cache::set('Chart:'.$schoolid.'electiveCombination',$data,CACHE_DATA_TIME);
        }
        return $this->echo_json(0,'',$data);
    }


}
