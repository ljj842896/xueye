<?php
namespace app\api\controller;

use app\common\model\Evaluate;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Evalu extends Action
{
    public function  memberEvaluate(){
        if(empty(input('type')) || !is_numeric(input('type'))){
            return $this->echo_json(30004);
        }
        $type = intval(input('type'));
        $uid = $this->uid;
        $data =Evaluate::get_user_evaluate($uid,$type);
        return $this->echo_json(0,'',$data);
    }
    public function evaluateDetail(){
        if(empty( input('taid')) || !is_numeric( input('taid'))){
            return  $this->echo_json(30004);
        }
        $tyuser_id  =  input('taid');
        $uid = $this->uid;
        $data =Evaluate::user_evaluate_detail($uid,$tyuser_id);
        return $this->echo_json(0,'',$data);
    }

    public function evalrecom()
    {
        if(empty(input('evid')) || !is_numeric(input('evid'))){
            return $this->echo_json(30004);
        }
        $id = input('evid');
        $uid =$this->uid;
        $data= Evaluate::recom($id,$uid);
        if($data){
            return $this->echo_json(140008,'',$data);
        }else{
            return $this->echo_json(140009);
        }
    }
}
