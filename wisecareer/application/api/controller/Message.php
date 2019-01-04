<?php
namespace app\api\controller;

use app\index\controller\Action;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Message extends Action
{
    /**
     * @return mixed
	 消息中心
     */ 
	
	 //消息中心
	public function index()
		{
			return $this->fetch();
		}
 


}
