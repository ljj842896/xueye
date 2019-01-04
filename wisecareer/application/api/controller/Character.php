<?php
namespace app\api\controller;

use app\common\Caches;
use app\common\model\StCeping;
use app\common\model\StCepingMul;
use app\index\controller\Action;
use think\Controller;
use think\facade\Cache;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Character extends Action
{
    /**
     * @return mixed
	     性格测评
     */
    /*
         生成 多元 测评
         */
    public function generate_mul()
    {
        $this->validate_status();
        $uid = $this->uid;
        $ots = StCepingMul::where('uid',$uid)->value('ots');

        if($ots == 1 ){
            return $this->echo_json(15015);
        }elseif($ots === 0){
            return $this->echo_json(0);
        }

        $data = StCepingMul::generate_ceping($uid);
        return $this->echo_json(0);
    }


    /**
     *  //调取测评题
     */
    public function transfer_mul()
    {
        $this->validate_status();
        $uid = $this->uid;
        $ots = StCepingMul::where('uid',$uid)->field('ots,datetime')->find();

        if( $ots &&  $ots->ots == 1 ){
            return $this->echo_json(15015,'',date('Y-m-d',$ots['datetime']));
        }elseif($ots === NULL ){
            StCepingMul::generate_ceping($uid);
        }
        $data = StCepingMul::sel_character($uid);
        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            $cpid = StCepingMul::where('uid',$uid)->where('ots',0)->value('id');
            return $this->echo_json(15010,'',$cpid);
        }
    }
    public function handle_mul()
    {

        $this->validate_status();
        $uid = $this->uid;
        if(empty($this->request->param('data'))){
            return $this->echo_json(30004);
        }
        if(empty($this->request->param('cpid') ) ||   !$this->request->param('cpid') ){
            return $this->echo_json(30004);
        }
        if(empty($this->request->param('ye')) ||   !intval($this->request->param('ye') )){
            return $this->echo_json(30004);
        }

        $ye = intval($this->request->param('ye'));
        $cpid = intval($this->request->param('cpid'));
        $tdata =$this->request->param('data');

        $array = [];
        $datas = explode(',',$tdata);
        $datas = array_filter($datas);
        if( count($datas) < 4){
            return $this->echo_json(30004);
        }
        foreach ($datas as $key => $value) {
            $array[$key]  =	explode('-',$value);
        }
        $arrays = array_filter($array);


        $data = StCepingMul::handle_ceping($arrays,$uid,$cpid);
        // 最后一页 完成所有
        if($ye==20){
            if(!StCepingMul::has_ceping_sel($uid,$cpid)){
                $data = StCepingMul::st_save($uid,$cpid);
            }
        }
        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(15010);
        }

    }
    /*
     生成测评
     */
    public function generate()
    {
        $this->validate_status();
        $uid = $this->uid;
        $ots = StCeping::where('uid',$uid)->value('ots');

        if($ots == 1 ){
            return $this->echo_json(15012);
        }elseif($ots === 0){
            return $this->echo_json(0);
        }

        $data = StCeping::generate_ceping($uid);
        return $this->echo_json(0);
    }

    /**
     *  //调取测评题
     */
    public function transfer()
    {
        $this->validate_status();
        $uid = $this->uid;
        $ots = StCeping::where('uid',$uid)->field('ots,datetime')->find();

        if( $ots &&  $ots->ots == 1 ){
            return $this->echo_json(15012,'',date('Y-m-d',$ots['datetime']));
        }elseif($ots === NULL ){
            StCeping::generate_ceping($uid);
        }
        $data = StCeping::sel_character($uid);
        if($data){

            return $this->echo_json(0,'',$data);
        }else{
            $cpid = StCeping::where('uid',$uid)->where('ots',0)->value('id');
            return $this->echo_json(15010,'',$cpid);
        }
    }

    /**处理性格测评
     * @return \think\response\Json
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function handle()
    {
        $this->validate_status();
        $uid = $this->uid;
        if(empty($this->request->param('data'))){
            return $this->echo_json(30004);
        }
        if(empty($this->request->param('cpid') ) ||   !$this->request->param('cpid') ){
            return $this->echo_json(30004);
        }
        if(empty($this->request->param('ye')) ||   !intval($this->request->param('ye') )){
            return $this->echo_json(30004);
        }

        $ye = intval($this->request->param('ye'));
        $cpid = intval($this->request->param('cpid'));
        $tdata =$this->request->param('data');

        $array = [];
        $datas = explode(',',$tdata);
        $datas = array_filter($datas);
        if( count($datas) < 4){
            return $this->echo_json(30004);
        }
        foreach ($datas as $key => $value) {
            $array[$key]  =	explode('-',$value);
        }
        $arrays = array_filter($array);


        $data = StCeping::handle_ceping($arrays,$uid,$cpid);
        // 最后一页 完成所有
//        if($ye==11){
//            if(!StCeping::has_ceping_sel($uid,$cpid)){
//                $data = StCeping::st_save($uid,$cpid);
//            }
//        }
        if($data){
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(15010);
        }

    }


    public function sub_handle()
    {
        $this->validate_status();
        $uid = $this->uid;
        if(empty($this->request->param('subject'))){
            return $this->echo_json(30004);
        }

        if(empty($this->request->param('cpid') ) ||   !$this->request->param('cpid') ){
            return $this->echo_json(30004);
        }
        $cpid = intval($this->request->param('cpid'));
        $subject = intval($this->request->param('subject'));
        $subject = $subject==1?1:2;
        $data = StCeping::st_save_sub($uid,$cpid,$subject);
        if($data){
            if(!StCeping::has_ceping_sel($uid,$cpid)){
                $data = StCeping::st_save($uid,$cpid);
            }
            return $this->echo_json(0,'',$data);
        }else{
            return $this->echo_json(15010);
        }

    }

    /**
     * 性格
     */
    public function character(){
        $action = !empty($_GET['action'])?trim($_GET['action']):'';
        $uid =  $this->uid;
        $chart = StCeping::user_character($uid,$action);
        if(!$chart){
            return $this->echo_json(15011);
        }
        return $this->echo_json(0,'',$chart);

    }

    /**
     * 性格
     */
    public function character_mul(){
        $action = !empty($_GET['action'])?trim($_GET['action']):'';
        $uid =  $this->uid;
        $chart = StCepingMul::user_character($uid,$action);
        if(!$chart){
            return $this->echo_json(15011);
        }
        return $this->echo_json(0,'',$chart);

    }

}
