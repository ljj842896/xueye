<?php
define("TOKEN", "xueyeapi");  
define("APPID", "wx602a56ee4a4ac69b"); 
define('KEY','xueyeapi') ;  
define('APPSECRET','105e74a9d1436e5fc5f05bf07c11d0ac') ;  
$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {

    
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }

    //响应消息
    public function responseMsg()
    {
        $postStr = file_get_contents('php://input');//$GLOBALS["HTTP_RAW_POST_DATA"]; 
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType); 
            //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            $this->logger("T ".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //接收事件消息
 private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe": 
                $content = array();
                 $content[] = array("Title"=>"关于智能学业e站", "Description"=>"欢迎关注智能学业e站", "PicUrl"=>"http://pic.wisecareer.org/images/xueye/weixin_about.jpg", "Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=http://xy.wisecareer.org/reg/agreement?id=24&w=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
                include_once('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri=http://xy.wisecareer.org/reg/agreement?id=24&w=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect');
                       
                break;
            case "unsubscribe":
                $content = "您已取消关注【彩虹联教练】，感谢您的关注！";
                break;
            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                break;
            case "CLICK":
   			switch($object->EventKey)
       		 {
				case "userjifen":
                    //$access_token = $this->getAccessToken();
                      //获取用户openid
                    // $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRET."&grant_type=authorization_code";
                    // $array = $this->https_request($url);
                    // $url ="http://jiaolian.wisecareer.org/api/get_jf?openid=".$object->FromUserName."&key=".md5(KEY);
                    // $result =$this->https_request($url);
                   
                     $content[] = array("Title"=>"关于智能学业e站", "Description"=>"欢迎关注智能学业e站", "PicUrl"=>"http://pic.wisecareer.org/images/xueye/weixin_about.jpg", "Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=http://xy.wisecareer.org/reg/agreement?id=24&w=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
                include_once('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri=http://xy.wisecareer.org/reg/agreement?id=24&w=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect');
                       
                   
                   
    				
				break;
				case "Hot":
				$content = array();

                $url ="http://jiaolian.wisecareer.org/api/get_hot?key=".md5(KEY);
                $result =$this->https_request($url);
                if($result['state']==200){
                    $content[] = array( "Title"=>$result['result']['titlename'], "PicUrl"=>$result['result']['pic'],"Description"=>"HOT案例","Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=http://jiaolian.wisecareer.org/index/hot?a=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
                }
				
				break;
				case "CampusSpace":
				$content = array();
				$content[] = array("Title"=>"模拟实习空间", "Description"=>"模拟实习空间", "PicUrl"=>"http://jiaolian.wisecareer.org/public/img/weixin/space.jpg", "Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=http://jiaolian.wisecareer.org/public/person/id/20?a=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
				break;
				case "message":
                $url ="http://jiaolian.wisecareer.org/api/get_message?openid=".$object->FromUserName."&key=".md5(KEY);
                    $result =$this->https_request($url);

                    if($result['state'] == 200){
                        $content = date("Y年m月d日")."\n你共收到 ".$result['result']['nums']." 条消息。其中 \n学员消息：".$result['result']['stu_num']."条 \n系统消息：".$result['result']['sys_num']."条 \n公告：".$result['result']['gok_num']."条 \n\n\n<a href='http://jiaolian.wisecareer.org/message?a=weixin'>想立即查看？ 戳这里  >> </a> \n ";
                       
                    }else{
                       $content ='抱歉！暂未开通';
                    }

			
				break;
                case "AboutCase":
                $content = array();
                $content[] = array("Title"=>"关于案例", "Description"=>"关于案例", "PicUrl"=>"http://jiaolian.wisecareer.org/public/img/weixin/anli.jpg", "Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=http://jiaolian.wisecareer.org/public/person/id/19?a=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
                break;
                case "Cooperation":
                $content = array();
                $content[] = array("Title"=>"合作品牌", "Description"=>"合作品牌", "PicUrl"=>"http://jiaolian.wisecareer.org/public/img/weixin/cooperation.jpg", "Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=http://jiaolian.wisecareer.org/public/person/id/21?a=weixin&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect");
                break;
				default:
				$content = "点击菜单：".$object->EventKey;
				break;
		 }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：".$object->MsgID."，结果：".$object->Status."，粉丝数：".$object->TotalCount."，过滤：".$object->FilterCount."，发送成功：".$object->SentCount."，发送失败：".$object->ErrorCount;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
		
	return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
         require_once('./replyModel.php');

        


            $replyModel =  new replyModel();

            $content = $replyModel->send($keyword);
        if($content){
            $result = $this->transmitText($object, $content);
             return $result;
        }


        //多客服人工回复模式
        if (strstr($keyword, "您好") || strstr($keyword, "你好") || strstr($keyword, "在吗")){
            $result = $this->transmitService($object);
        }
        //自动回复模式
      //   else{
      //       if (strstr($keyword, "？") || strstr($keyword, "？？") || strstr($keyword, "！")){


      //           //$content = "您的问题已收到，请耐心等待小编的回复哦~（温馨提示：小编在线时间9:00-18:00），非工作时间和节假日亲们可致电：4000740520，我们的客服小妹会及时给您解决。您也可以将问题、好的建议，通过回复，告诉我们。O(∩_∩)O谢谢！！";
      //           $content = '';
      //       }else if (strstr($keyword, "红包")){
      //         $content = "暂无红包信息";
      //       }else if (strstr($keyword, "消息") || strstr($keyword, "公告")){ 
      //           $content = "暂无信息";
      //       } else{
      //         // $content = "您的问题已收到，请耐心等待小编的回复哦~（温馨提示：小编在线时间9:00-18:00），非工作时间和节假日亲们可致电：4000740520，我们的客服小妹会及时给您解决。您也可以将问题、好的建议，通过回复，告诉我们。O(∩_∩)O谢谢！！";
      //          $content = '';
			   // // $content = date("Y-m-d H:i:s",time())."\n彩虹联教练";
      //       } 
      //       if(is_array($content)){
      //           if (isset($content[0]['PicUrl'])){
      //               $result = $this->transmitNews($object, $content);
      //           }else if (isset($content['MusicUrl'])){
      //               $result = $this->transmitMusic($object, $content);
      //           }
      //       }else{
      //           $result = $this->transmitText($object, $content);
      //       }
      //   } 
        return $result;
    }
 
    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "您发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "您发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
        <MediaId><![CDATA[%s]]></MediaId>
        </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[image]]></MsgType>
         $item_str
         </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
        <MediaId><![CDATA[%s]]></MediaId>
         </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[voice]]></MsgType>
        $item_str
        </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
         $item_str
        </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
        </item>
         ";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[news]]></MsgType>
         <ArticleCount>%s</ArticleCount>
         <Articles>
         $item_str</Articles>
        </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
        </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        $item_str
        </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
         <ToUserName><![CDATA[%s]]></ToUserName>
         <FromUserName><![CDATA[%s]]></FromUserName>
         <CreateTime>%s</CreateTime>
         <MsgType><![CDATA[transfer_customer_service]]></MsgType>
         </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 10000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }

    public function getAccessToken()
    {
      //获取memcache缓存的access_token
      $access_token = $this->_memcache_get("access_token");
      //如果缓存的access_token失效
      if(!$access_token)
      {  
        //如果失效调用获取接口凭证来获取access_token
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
        $outoptArr = $this->https_request($url);
        if(!isset($outoptArr['errcode']))
        {
          //memcache缓存access_token
          $this->_memcache_set("access_token",$outoptArr['access_token'],7000);
          return $outoptArr['access_token'];
        }
      }  
      return $access_token;
    }




    //初始化memcache
    private function _memcache_init()
    {
      $mmc = new Memcache;
      $ret = $mmc -> connect("127.0.0.1",11211);
      if ($ret == false) 
      {
        return;
      } 
      return $mmc;
    }
  
    //设置memcache
    private function _memcache_set($key,$value,$time=0)
    {
      $mmc = $this->_memcache_init();
      $mmc -> set($key,$value,0,$time);
    }
  
    //获取memcahce
    private function _memcache_get($key)
    {
      $mmc = $this->_memcache_init(); 
      return $mmc -> get($key);  
    }

    private function https_request($url,$data=null)
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
}
?>