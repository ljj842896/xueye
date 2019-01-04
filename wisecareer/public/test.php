<?php
header("Content-type: text/html; charset=utf-8");


$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx602a56ee4a4ac69b&secret=105e74a9d1436e5fc5f05bf07c11d0ac";

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
 

$data = '{
    "button": [
        {
            "name": "3+3选科",
            "sub_button": [
                {
                    "type": "view",
                    "name": "选科组合",
                    "url": "http://xy.wisecareer.org/jcyj/index?back=1"
                },
				      {
                    "type": "view",
                    "name": "目标专业",
                    "url": "http://xy.wisecareer.org/jcyj/recommend?type=2&back=1"
                },
                {
                    "type": "view",
                    "name": "学科能力",
                    "url": "http://xy.wisecareer.org/presentation/branch_list/?back=1"
                },
                {
                    "type": "view",
                    "name": "学科动态",
                    "url": "http://xy.wisecareer.org/dynamic/index?back=1"
                } 
            ]
        },
        {
            "name": "找大学/专业",
            "sub_button": [
            	   {
                    "type": "view",
                    "name": "找大学",
                    "url": "http://xy.wisecareer.org/aggregate/school_search/?back=1"
                },
                  {
                    "type": "view",
                    "name": "专业检索",
                    "url": "http://xy.wisecareer.org/aggregate/major_search/?back=1"
                },
                {
                    "type": "view",
                    "name": "专业排名",
                    "url": "http://xy.wisecareer.org/aggregate/major/?back=1"
                },
				        {
                    "type": "view",
                    "name": "志愿填报",
                    "url": "http://xy.wisecareer.org/scores/index/?back=1"
                }
               
            ]
        },
        {
            "name": "我的E站",
            "sub_button": [
                {
                    "type": "view",
                    "name": "首页",
                    "url": "http://xy.wisecareer.org/?back=1"
                },
                {
                    "type": "view",
                    "name": "注册／登录",
                    "url": "http://xy.wisecareer.org/login/index?back=1"
                },              
                {
                    "type": "view",
                    "name": "我的报告",
                    "url": "http://xy.wisecareer.org/presentation/index/?back=1"
                },
                
                {
                    "type": "view",
                    "name": "竞争数据",
                    "url": "http://xy.wisecareer.org/dynamic/chart/?back=1"
                },
              
                {
                    "type": "view",
                    "name": "个人中心",
                    "url": "http://xy.wisecareer.org/user/index/?back=1"
                }
            ]
        }
    ]
}'; 

echo createMenu($data);
//echo getMenu();
//echo deleteMenu();
//
  // {
  //                   "type": "view",
  //                   "name": "收藏与关注",
  //                    "url": "http://xy.wisecareer.org/collection/major/"
  //               },