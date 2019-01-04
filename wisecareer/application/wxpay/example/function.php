
<?php


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