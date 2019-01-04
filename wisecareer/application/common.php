<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------


function datediff($date1){
    $Date_1=date("Y-m-d");
    $Date_2=$date1;
    $d1=strtotime($Date_1);
    $d2=strtotime($Date_2);
    $Days=round(($d1-$d2)/3600/24);
    return $Days;
}


function start_time(){
    $t = time();
    return  mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
}

function end_time(){
    $t = time();
    return mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
}

function getJson($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}
// 应用公共文件
/**
 * 获取 用户 ip
 * @return array|false|string
 */
function getIp() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                $ip = getenv("REMOTE_ADDR");
            else
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    $ip = $_SERVER['REMOTE_ADDR'];
                else
                    $ip = "unknown";
    return ($ip);
}

function utf8_filter($data)

{

    $str = "";

    for($n = 0; $n < strlen($data);)

    {

        $s = substr($data, $n, 1);

        $v = ord($s);

        if($v >= 127)

        {

            ++$n;

            $cnt = 0;

            $tmp = $v;

            while($tmp & 0x80)

            {

                $tmp = $tmp << 1;

                ++$cnt;

            }

            $x = 0;

            while($x < $cnt && $n < strlen($data))

            {

                $s = substr($data, $n, 1);

                if((ord($s) & 0xC0) == 0x80)

                {

                    ++$n;

                    ++$x;

                }else{

                    break;

                }

            }

            if($x + 1 == $cnt)

            {

                $str  = $str . substr($data, $n - $cnt, $cnt);

            }else{

                while($n < strlen($data))

                {

                    $s = substr($data, $n, 1);

                    if(ord($s) & 0x80)

                    {

                        ++$n;

                    }else{

                        break;

                    }

                }

            }

        }else{

            $str = $str. $s;

            ++$n;

        }

    }

    return  $str;

}

/**数组排序
 * @param $arr
 * @param $keys
 * @param string $type
 * @return array
 */
function array_sort($arr, $keys, $type='asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**多字段排序实
 * @param $arr
 * @param $keys
 * @param string $type
 * @return array
 */
function sortByCols($list,$field)
{
    $sort_arr = array();
    $sort_rule = '';
    foreach ($field as $sort_field => $sort_way) {
        foreach ($list as $key => $val) {
            $sort_arr[$sort_field][$key] = $val[$sort_field];
        }
        $sort_rule .= '$sort_arr["' . $sort_field . '"],' . $sort_way . ',';
    }
    if (empty( $sort_arr ) || empty( $sort_rule )) {
        return $list;
    }
    eval( 'array_multisort(' . $sort_rule . ' $list);' );//array_multisort($sort_arr['parent'], 4, $sort_arr['value'], 3, $list);
    return $list;
}
/**
 * 随机字符串
 * @param $leng
 * @return null|string
 */
function getRand($leng){
    //$a = '0123456789abcdefghijklmnopqrstuvwxyz'; //加小写字母都可以
    $a = '0123456789'; //加小写字母都可以
    $str =null;
    for($i=0;$i<$leng;$i++){
        $str .= $a[rand(0, strlen($a)-1)];
    }
    return $str;
}

/**
 * 手机验证
 * @param $subject
 * @return bool
 */
function Phone($subject) {
    $pattern='/^(0|86|17951)?(13[0-9]|15[012356789]|1[789][0-9]|14[57])[0-9]{8}$/';
    return PublicMethod($pattern, $subject);
}

/**
 * email 验证
 * @param $subject
 * @return bool
 */
function Email($subject) {

    $pattern='/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
    return PublicMethod($pattern, $subject);
}

/**
 * 身份证验证
 * @param $subject
 */
function ID($subject)
{
    $pattern = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
}

/**
 * 验证基类
 * @param $pattern
 * @param $subject
 * @return bool
 */
function PublicMethod($pattern, $subject){
    if(preg_match($pattern, $subject)){
        return true;
    }
    return false;
}

/**检查维度
 * @param $num
 * @return string
 */
function cheweidu($num){
    switch($num){
        case "E";
            return "外倾";
            break;
        case "I";
            return "内倾";
            break;
        case "S";
            return "实感";
            break;
        case "N";
            return "直觉";
            break;
        case "F";
            return "感性";
            break;
        case "T";
            return "理性";
            break;
        case "P";
            return "弹性";
            break;
        case "J";
            return "系统";
            break;
    }
}

 /*****情景*****/

function chengduweidu($num,$user){
    $conn = DBManager::getConnection();
    $sql = "SELECT * FROM `kw_cp_id` WHERE `id` in (4,3,11,7)";

    $result = $conn->query($sql);
    $name = '';
    while($obj = $result->fetch_assoc())
    {
        if($obj['qingj']!=""){
            $name .= $obj['qingj']."、";
        }
    }
    return $name;
}

/******性格***/	 /****维度****/
 function xingge($num1,$num2,$num3,$num4,$xg1,$xg2,$xg3,$xg4 ){

     if($xg1==1){
         $px1 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg1==2){
         $px1 = "px = '2' or px = '3'";
     }elseif($xg1==3){
         $px1 = "px = '3'";
     }

     if($xg2==1){
         $px2 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg2==2){
         $px2 = "px = '2' or px = '3'";
     }elseif($xg2==3){
         $px2 = "px = '3'";
     }

     if($xg3==1){
         $px3 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg3==2){
         $px3 = "px = '2' or px = '3'";
     }elseif($xg3==3){
         $px3 = "px = '3'";
     }

     if($xg4==1){
         $px4 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg4==2){
         $px4 = "px = '2' or px = '3'";
     }elseif($xg4==3){
         $px4 = "px = '3'";
     }

     $daname = " and ((da= '$num1' and (".$px1.")) or (da= '$num2' and (".$px2.")) or (da= '$num3' and (".$px3.")) or (da= '$num4'  and (".$px4.")) or (da = '".$num1."".$num2."".$num3."' or da = '".$num2."".$num3."".$num4."' or da = '".$num1."".$num3."".$num4."' or da = '".$num1."".$num2."".$num4."' or da = '".$num1."".$num2."' or da = '".$num1."".$num3."' or da = '".$num1."".$num4."' or da = '".$num2."".$num3."' or da = '".$num2."".$num4."' or da = '".$num3."".$num4."'))";

     $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '1' and mid ='1'".$daname;

     $name = '';
     $result = \think\Db::query($sql);

     foreach($result as $key =>$value)
     {
         $daima = strlen($value['da']);
         if($daima==1){
             $dm = $value['da'];
         }
         $name .= "<img src='http://zxedu.wisecareer.org/images/cp_dx_1.gif' />".$value['content']."<br/>";
     }
     return $name;
 }

	/***忙点**/

	 function mangdian($num1,$num2,$num3,$num4,$xg1,$xg2,$xg3,$xg4){


         /*$sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '1' and mid = 1  and (da = '$num1' or da = '$num2' or da = '$num3' or da = '$num4') ";
         echo $sql;
         exit;
         */

         if($xg1==1){
             $px1 = "px = '1' or px = '2' or px = '3'";
         }elseif($xg1==2){
             $px1 = "px = '2' or px = '3'";
         }elseif($xg1==3){
             $px1 = "px = '3'";
         }

         if($xg2==1){
             $px2 = "px = '1' or px = '2' or px = '3'";
         }elseif($xg2==2){
             $px2 = "px = '2' or px = '3'";
         }elseif($xg2==3){
             $px2 = "px = '3'";
         }

         if($xg3==1){
             $px3 = "px = '1' or px = '2' or px = '3'";
         }elseif($xg3==2){
             $px3 = "px = '2' or px = '3'";
         }elseif($xg3==3){
             $px3 = "px = '3'";
         }

         if($xg4==1){
             $px4 = "px = '1' or px = '2' or px = '3'";
         }elseif($xg4==2){
             $px4 = "px = '2' or px = '3'";
         }elseif($xg4==3){
             $px4 = "px = '3'";
         }


         $daname = " and ((da= '$num1' and (".$px1.")) or (da= '$num2' and (".$px2.")) or (da= '$num3' and (".$px3.")) or (da= '$num4'  and (".$px4.")) or (da = '".$num1."".$num2."".$num3."' or da = '".$num2."".$num3."".$num4."' or da = '".$num1."".$num3."".$num4."' or da = '".$num1."".$num2."".$num4."' or da = '".$num1."".$num2."' or da = '".$num1."".$num3."' or da = '".$num1."".$num4."' or da = '".$num2."".$num3."' or da = '".$num2."".$num4."' or da = '".$num3."".$num4."'))";


         $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '1' and mid ='2'".$daname;


// $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '1' and mid ='2' and (da like '%$num1%' or da like '%$num2%' or da like '%$num3%' or da like '%$num4%')";
         $name = '';
         $result = \think\Db::query($sql);


         foreach($result as $key =>$value)
         {

             $daima = strlen($value['da']);

             if($daima==1){
                 $dm = $value['da'];
             }


             $name .= "<img src='http://zxedu.wisecareer.org/images/cp_dx_1.gif' />".$value['content']."<br/>";

         }
         return $name;
     }

/***第二性格**/

	function miaos($num2){

        $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '2' and da='$num2'";
        $result = \think\Db::query($sql);
        foreach($result as $key =>$value)
        {
            $name= "".$value['content']."<br />";
        }
        return $name;
    }



	/******优势****/
 function youshi($num1,$num2,$num3,$num4,$xg1,$xg2,$xg3,$xg4){



     if($xg1==1){
         $px1 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg1==2){
         $px1 = "px = '2' or px = '3'";
     }elseif($xg1==3){
         $px1 = "px = '3'";
     }

     if($xg2==1){
         $px2 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg2==2){
         $px2 = "px = '2' or px = '3'";
     }elseif($xg2==3){
         $px2 = "px = '3'";
     }

     if($xg3==1){
         $px3 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg3==2){
         $px3 = "px = '2' or px = '3'";
     }elseif($xg3==3){
         $px3 = "px = '3'";
     }

     if($xg4==1){
         $px4 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg4==2){
         $px4 = "px = '2' or px = '3'";
     }elseif($xg4==3){
         $px4 = "px = '3'";
     }


     $daname = " and ((da= '$num1' and (".$px1.")) or (da= '$num2' and (".$px2.")) or (da= '$num3' and (".$px3.")) or (da= '$num4'  and (".$px4.")) or (da = '".$num1."".$num2."".$num3."' or da = '".$num2."".$num3."".$num4."' or da = '".$num1."".$num3."".$num4."' or da = '".$num1."".$num2."".$num4."' or da = '".$num1."".$num2."' or da = '".$num1."".$num3."' or da = '".$num1."".$num4."' or da = '".$num2."".$num3."' or da = '".$num2."".$num4."' or da = '".$num3."".$num4."'))";


     $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '3' and mid ='1'".$daname;



// $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '3' and mid ='1' and (da like '%$num1%' or da like '%$num2%' or da like '%$num3%' or da like '%$num4%')";
     $name = '';
     $result = \think\Db::query($sql);
     foreach($result as $key =>$value)
     {

         $daima = strlen($value['da']);

         if($daima==1){
             $dm = $value['da'];
         }


         $name .= "<img src='http://zxedu.wisecareer.org/images/cp_dx_1.gif' />".$value['content']."<br/>";

     }
     return $name;
 }



/******职业偏向****/
function eduzhiye($num1,$num2,$num3,$num4,$subject){


    $daname = $num1.$num2.$num3.$num4;
    if($subject>0){
        $subject = " and subject='$subject'";
    }else{
        $subject = " and subject='2'";
    }

    $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '9' and da = '".$daname."'".$subject;

    $result = \think\Db::query($sql);
    $name='';
    foreach($result as $key =>$value)
    {
        $name .= "<span  style='font-weight:600;'>".$value['tjff']."</span>"."<br />".$value['content']."<br />";
    }
    return $name;
}

/******劣势****/
 function lieshi($num1,$num2,$num3,$num4,$xg1,$xg2,$xg3,$xg4){



     if($xg1==1){
         $px1 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg1==2){
         $px1 = "px = '2' or px = '3'";
     }elseif($xg1==3){
         $px1 = "px = '3'";
     }

     if($xg2==1){
         $px2 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg2==2){
         $px2 = "px = '2' or px = '3'";
     }elseif($xg2==3){
         $px2 = "px = '3'";
     }

     if($xg3==1){
         $px3 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg3==2){
         $px3 = "px = '2' or px = '3'";
     }elseif($xg3==3){
         $px3 = "px = '3'";
     }

     if($xg4==1){
         $px4 = "px = '1' or px = '2' or px = '3'";
     }elseif($xg4==2){
         $px4 = "px = '2' or px = '3'";
     }elseif($xg4==3){
         $px4 = "px = '3'";
     }


     $daname = " and ((da= '$num1' and (".$px1.")) or (da= '$num2' and (".$px2.")) or (da= '$num3' and (".$px3.")) or (da= '$num4'  and (".$px4.")) or (da = '".$num1."".$num2."".$num3."' or da = '".$num2."".$num3."".$num4."' or da = '".$num1."".$num3."".$num4."' or da = '".$num1."".$num2."".$num4."' or da = '".$num1."".$num2."' or da = '".$num1."".$num3."' or da = '".$num1."".$num4."' or da = '".$num2."".$num3."' or da = '".$num2."".$num4."' or da = '".$num3."".$num4."'))";


     $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '3' and mid ='2'".$daname;

     //$sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '3' and mid ='2' and (da like '%$num1%' or da like '%$num2%' or da like '%$num3%' or da like '%$num4%')";

     $result = \think\Db::query($sql);
     foreach($result as $key =>$value)
     {

         $daima = strlen($value['da']);
         $name = '';
         if($daima==1){
             $dm = $value['da'];
         }
         $name .= "<img src='http://zxedu.wisecareer.org/images/cp_dx_1.gif' />".$value['content']."<br/>";

     }
     return $name;
 }

 /******推荐学习方法****/
 function edusjiaoyu($num1,$num2,$num3,$num4,$xg1,$xg2,$xg3,$xg4){


     $daname = " and ((da= '$num1' and px = '$xg1') or (da= '$num2' and px = '$xg2') or (da= '$num3'  and px = '$xg3') or(da= '$num4'  and px = '$xg3') or (da = '".$num1."".$num2."".$num3."' or da = '".$num2."".$num3."".$num4."' or da = '".$num1."".$num3."".$num4."' or da = '".$num1."".$num2."".$num4."' or da = '".$num1."".$num2."' or da = '".$num1."".$num3."' or da = '".$num1."".$num4."' or da = '".$num2."".$num3."' or da = '".$num2."".$num4."' or da = '".$num3."".$num4."'))";


     $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '4' ".$daname;

     //$sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '4'  and (da like '%$num1%' or da like '%$num2%' or da like '%$num3%' or da like '%$num4%')";

     $name= '';


     $result = \think\Db::query($sql);
     foreach($result as $key =>$value)
     {
         $name .= "<span  style='font-weight:600;'>".$value['tjff']."</span>"."<br />".$value['content']."<br />";
     }
     return $name;
 }


 /***激励方式 **/

	function jiling($model,$num2){

        $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '5' and da='$num2'";
        $result = $model->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($result as $key =>$value)
        {
            $name= "<img src='http://zxedu.wisecareer.org/images/cp_dx_1.gif' />".$value['content']."<br />";
        }
        return $name;
    }

 /***教学风格偏好  **/

	function jiaoxue($model,$num2){

        $sql = "SELECT * FROM `kw_xg_a1` WHERE aid = '6' and da='$num2'";
        $result = $model->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($result as $key =>$value)
        {
            $name= "<img src='http://zxedu.wisecareer.org/images/cp_dx_1.gif' />".$value['content']."<br />";
        }
        return $name;
    }


/**
 * 用户资料是否完善
 * @param $myinfo
 * @return int
 */
function userinfo_is($myinfo){
        if( !empty($myinfo['user_name']) && !empty($myinfo['sex']) && !empty($myinfo['birthday']) && !empty($myinfo['schoolname']) && !empty($myinfo['classname']) && !empty($myinfo['classroom'])){
            return 1;
        }else{
            return 0;
        }
    }

function formatting($v){
    $v['datetime'] =  date('Y-m-d H:i:s',$v['datetime']);
    $v['degreestr'] =  $v['degree'] ==5 ? '已离岗':'' ;

    return $v;
}

//等级
function grade($num,$width=0){



    $man = "<img src='http://jiaolian.wisecareer.org/public/img/images/star_all.png' class='img_grade' />";
    $ban = "<img src='http://jiaolian.wisecareer.org/public/img/images/star_half.png' class='img_grade' />";



    if($num == 1){
        $str = $man;
    }elseif($num == 1.5){
        $str = $man.$ban;
    }elseif($num == 2){
        $str = $man.$man;
    }elseif($num == 2.5){
        $str = $man.$man.$ban;
    }elseif($num == 3){
        $str = $man.$man.man;
    }elseif($num == 3.5){
        $str = $man.$man.$man.$ban;
    }elseif($num == 4){
        $str = $man.$man.$man.$man;
    }elseif($num == 4.5){
        $str = $man.$man.$man.$man.$ban;
    }elseif($num == 5){
        $str = $man.$man.$man.$man.$man;
    }
    return $str;
}
function userImg($myuser){

    if($myuser['pic'] !=''){
        $data['pic'] = $myuser['pic'];
    }else{
        $data['pic'] = 'http://jiaolian.wisecareer.org/public/img/user-image.png';
    }
    if($myuser['sex'] == 1){
        $data['img'] ='http://jiaolian.wisecareer.org/public/img/icon/man.png';
    }elseif($myuser['sex'] ==2){
        $data['img'] =	"http://jiaolian.wisecareer.org/public/img/icon/woman.png";
    }


    return $data;

}

//生成二维码
function scerweima1($url='',$datetime,$id,$type='2'){
	require_once '/data/wisecareer/thinkphp/library/vendor/phpqrcode/phpqrcode.php';
	
	if($type==1){
		$types = "xy";
		}else{
		$types = "zt";	
			}
	$money = $types."_"."qrcode_".$id."_".$datetime;
	$value = $url;					//二维码内容  
	$errorCorrectionLevel = 'H';	//容错级别  
	$matrixPointSize = 8;			//生成图片大小  
	//生成二维码图片 
	 
	// $filename = 'qrcode/'.$moneys."/".$money.'.png';
	 $filename  = '/data/wisecareer/public/uploads/qrcode/'.$money.'.png';
	 $filename_pic = 'uploads/qrcode/'.$money.'.png';
 
 QRcode::png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);  
	
	if($type==1){ 
		$logo = '/data/wisecareer/public/uploads/qrcode/xy_logo.png'; 	//准备好的学业logo图片 
	 }else{ 
		$logo = '/data/wisecareer/public/uploads/qrcode/zt_logo.png'; 	//准备好的学业logo图片 
	  }
			

	$QR = $filename;			//已经生成的原始二维码图  
 
 if (file_exists($logo)) {   
		$QR = imagecreatefromstring(file_get_contents($QR));   		//目标图象连接资源。
		$logo = imagecreatefromstring(file_get_contents($logo));   	//源图象连接资源。
		$QR_width = imagesx($QR);			//二维码图片宽度   
		$QR_height = imagesy($QR);			//二维码图片高度   
		$logo_width = imagesx($logo);		//logo图片宽度   
		$logo_height = imagesy($logo);		//logo图片高度   
		$logo_qr_width = $QR_width / 5;   	//组合之后logo的宽度(占二维码的1/5)
		$scale = $logo_width/$logo_qr_width;   	//logo的宽度缩放比(本身宽度/组合后的宽度)
		$logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
		$from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
		
		//重新组合图片并调整大小
		/*
		 *	imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
		 */
		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height); 
	}   
  
	//输出图片  
	imagepng($QR, $filename);  
	imagedestroy($QR);
	imagedestroy($logo);
	 return $filename_pic;   
}
 
