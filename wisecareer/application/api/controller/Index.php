<?php
namespace app\api\controller;

use app\index\controller\Action;
use think\Controller;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Index extends Action
{
    /**
     * @return mixed
     */
    public function index()
    {

        $this->assign('userpic','xx');
        return $this->fetch('index/index');
    }


}
