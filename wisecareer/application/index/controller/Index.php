<?php
namespace app\index\controller;
use Db;
use think\facade\Cookie;
use think\facade\Session;
class Index
{
    public function index()
    {

        return $this->fetch();
	}
}
