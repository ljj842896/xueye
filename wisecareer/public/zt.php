<?php
header("Content-type: text/html; charset=utf-8");


$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx1189697f6d6a1212&secret=114a17fbd2249a2d096f2b31c5316813";

//d2bf459953d999a121b897a6ae4b267e";

$result =https_request($url);


     
define("ACCESS_TOKEN", $result['access_token']);


function https_request($url,$data=null)
    {
      //1、初始化curl
      $ch = curl_init();
  
      //2、设置传输选项
      curl_setopt($ch, CURLOPT_URL, $url);//请求的url地址
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//将请求的结果以文件流的形式返回
        
      if(!empty($data))
      {
        curl_setopt($ch,CURLOPT_POST,1);//请求POST方式
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//POST提交的内容
      }
  
      //3、执行请求并处理结果
      $outopt = curl_exec($ch);
  
      //把json数据转化成数组
      $outoptArr = json_decode($outopt,TRUE);
  
      //4、关闭curl
      curl_close($ch);
  
      //如果返回的结果$outopt是json数据，则需要判断一下
      if(is_array($outoptArr))
      {  
        return $outoptArr;
      }
      else
      {
        return $outopt;
      }      
    }




//创建菜单
function createMenu($data){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".ACCESS_TOKEN);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$tmpInfo = curl_exec($ch);
if (curl_errno($ch)) {
  return curl_error($ch);
}

curl_close($ch);
return $tmpInfo;

}

//获取菜单
function getMenu(){
return file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".ACCESS_TOKEN);
}

//删除菜单
function deleteMenu(){
return file_get_contents("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".ACCESS_TOKEN);
}
 //http://zt.wisecareer.org/scene/baogao?w=weixin&back=1

$data = '{
    "button": [
        {
            "name": "职业体验",
            "sub_button": [
				        {
                    "type": "view",
                    "name": "行业动态",
                    "url": "http://zt.wisecareer.org/hangye/index?w=weixin&back=1"
                },
                {
                    "type": "view",
                    "name": "体验计划",
                    "url": "http://zt.wisecareer.org/experience/plan?weixin&back=1"
                },
                {
                    "type": "view",
                    "name": "案例体验",
                    "url": "http://zt.wisecareer.org/experience/index/#&slider1=3&back=1"
                }
               
            ]
        },
        {
            "name": "现场实习",
            "sub_button": [
            	{
                    "type": "view",
                    "name": "实习申请",
                    "url": "http://zt.wisecareer.org/invitation/index?w=weixin&back=1"
                },
                {
                    "type": "view",
                    "name": "实习报告",
                    "url": "http://zt.wisecareer.org/presentation/index?w=weixin&back=1"
                },
                {
                    "type": "view",
                    "name": "现场实习",
                    "url": "http://zt.wisecareer.org/scene/input?w=weixin&back=1"
                }
			
            ]
        },
        {
            "name": "我的e站",
            "sub_button": [
                {
                    "type": "view",
                    "name": "首页",
                    "url": "http://zt.wisecareer.org?w=weixin&back=1"
                },{
                    "type": "view",
                    "name": "注册／登录",
                    "url": "http://zt.wisecareer.org/login/index?w=weixin&back=1"
                },
                
                {
                    "type": "view",
                    "name": "我的报告",
                    "url": "http://zt.wisecareer.org/presentation/index?w=weixin&back=1"
                },
                {
                    "type": "view",
                    "name": "我的教练",
                    "url": "http://zt.wisecareer.org/jiaolian/index?w=wiexin&back=1"
                },
                {
                    "type": "view",
                    "name": "个人中心",
                    "url": "http://zt.wisecareer.org/user/index?w=weixin&back=1"
                }
            ]
        }
    ]
}'; 

echo createMenu($data);
//echo getMenu();
//echo deleteMenu();
//
//职拓体验： 案例体验，现场实习，首页
//我的关注： 我的教练， 朋友圈， 