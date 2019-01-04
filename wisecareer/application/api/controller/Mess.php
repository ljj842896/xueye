<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\Charts;
use app\common\model\UserMessage;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Mess extends Action
{

    public function userMessage()
    {
        $type = empty($_GET['type'])?0:intval($_GET['type']);
        $cli = empty(input('get.cli'))?'xy':'zt';


        $limit = $this->search_page('array');
        $uid = $this->uid;
        $userinfo = Caches::MyuserInfoFiled($uid);

        $scid =$userinfo['scid'];
        $sdid =$userinfo['sdid'];
        $closslx =$userinfo['closslx'];
        if($closslx <=9 ){
            $array['lx'] = array(0,1);
        }else{
            $array['lx'] = array(0,2);
        }
        $array['scid'] = array(0,$scid);
        $array['sdid'] = array(0,$sdid);
        if($cli=='xy'){
            $typein=[0,1,2,3,5,9];
        }elseif($cli =='zt'){
            $typein=[0,4,5,6,7,8];
        }

        $data = UserMessage::get_message($uid,$type,$limit,$array,$typein);

        if(!count($data)){
            $data ='';
        }
        return $this->echo_json(0,'',$data);
    }
}
