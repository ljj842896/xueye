<?php
namespace app\index\controller;
use app\common\Caches;
use app\common\model\Myuser;
use app\common\model\User_write;
use app\common\Smtp;
use Db;
use think\facade\Cache;


class Publics extends Action{

    /**
     *  家庭数据采集
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function wirteHomeList()
    {
        $num= 4;
        $page  = intval($num * (intval(input('page'))?input('page')-1:0));
        $pagestr= "{$page},4";
        $myinfo = session('myuserinfo');
        $uid = $myinfo['id'];
        $User_write = new User_write();

        $data = $User_write->wirteHomeList($pagestr,$uid);
        $page2 = $page+4;
        $pagestr2 =  "{$page2},4";
        $data1 = $User_write->wirteHomeList($pagestr2,$uid);
        $result['has'] = 0;
        if($data1){
            $result['has'] = 1;
        }
        if($data){
            $result['list'] = $data;
            return $this->echo_json(0,'',$result);
        }else{
            return $this->echo_json(60001);
        }

	}

    /**
     * 家庭数据采集
     * @param $array
     * @param $uid
     * @return bool
     */
    public function userWirtehome()
    {
//        $validate= new Validate([
//            'writehome'=>'require|token',
//        ]);
//        if(!$validate->check($this->request->param())){
//            return $this->echo_json(30003,$validate->getError());
//        }

        $uid =session('myuserinfo')['id'];
        $writehome =explode('&',urldecode($this->request->param('writehome'))) ;
        $result=[];
        foreach ($writehome as  $value) {
            $test=explode('=',$value);
            if(empty($result[$test[0]])){
                $result[$test[0]]=$test[1];
            }else{
                $result[$test[0]] .= ','.$test[1];
            }

        }

        $User_write = new User_write();

        $data =$User_write ->userWirtehome($result,$uid);
        if($data){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(60002);
        }

    }

    /**
     * ajax 上传图片
     */
    public function update_pic(){

        if(empty($_POST['pic'])){
            return $this->echo_json(50011);
        }
        $uid  = $this->uid;
        $myinfo = Caches::MyuserInfoFiled($uid);
        $panth= UPLOADS.'userpic/';
        $newfilename = date('YmdHis')."_".rand(100,999);
        $base64_image_content = $_POST['pic'];
        //保存base64字符串为图片
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $new_file = $panth.$newfilename.".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                $pic['pic']= "userpic/".$newfilename.".{$type}";

                $result1 = Myuser::whereid($uid)->data($pic)->update();
                if($result1){
                    Caches::MyuserInfoFiled($uid,1);
                    Caches::MyuserInfo($uid,1);
                    return $this->echo_json(0);
                } else {
                    return $this->echo_json(50010);
                }
            }

        }

        return $this->echo_json(50010);
    }


    public function send_email(){
        $uid  = $this->uid;
        $toemail = input('mail');

        if(!Email($toemail)){
             return $this->echo_json(20008);
        }
        $smtp = new Smtp();
        $name='static7';
        $subject='彩虹联教育现场实习报告';
        $url = "http://lab.wisecareer.org/?m=p&uid=".$uid;
        $Myinfo = Caches::MyuserInfoFiled($uid);
        $name = $Myinfo['user_name'];
        $content = $smtp->get_content($name,$url);
        $r = $smtp->send_mail($toemail,$name,$subject,$content);

        if($r===true){
            return $this->echo_json(0);
        }else{
            return $this->echo_json(20009);
        }

        exit;

    }

}
