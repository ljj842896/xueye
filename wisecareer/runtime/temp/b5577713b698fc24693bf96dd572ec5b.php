<?php /*a:2:{s:73:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\xueye\index\index.html";i:1545031422;s:69:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\layout\layout.html";i:1545040634;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>智能学业e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
     <script src="/static/public/js/jquery-1.8.3.min.js"></script>
    <script src="/static/public/js/com.min.js?v=20181217"></script>
    <script src="/static/public/js/base.min.js?v=20181217"></script>
    <script src="/static/lib/layer/mobile/layer.js"></script>
    <link rel="stylesheet" href="/static/lib/layer/mobile/need/layer.css"/>
    <link href="/static/xueye/css/style.css?v=20180906" type="text/css" rel="stylesheet" />
    

<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<link rel="stylesheet" href="/static/xueye/css/yuan.css"/>
	<style>
		html,body{
			height: auto;
		}
		#demo{
			position: relative;
			width: 100%;
		}
		#circle_num>h4{
			color: #fff;
			line-height: 30px;
		}

			#dBody{
				bottom: 130px;
			}
			#dFoot{
				height: 125px;
			}
			#dFoot>#jiaxiang{
				position: fixed;
				bottom: 95px;
				left: 50%;
				margin-left: -30px;
				z-index: 2000;
			}
			#dFoot>table{
				padding: 20px 0
			}
			#dFoot>table#table_1 td{
				line-height: 70px;
			}
			#dFoot>table#table_2 td{
				line-height: 37px;
			}



		.danxuan3>li{
			background: none;
			line-height: 30px;
			height: 30px;
		}
		.danxuan3>li>a.this{
			background: none;
			border: 1px dashed #fff;
			color: #fff;
		}
		.btn_yes{
			background: #fff;
			color: #666;
		}
		#ez {
			width: 25px;
			display: inline-block;
			position: relative;
		}
		#ez>img {
			position: absolute;
			top: 10px;
			left: 3px;
		}

		input[type='button']#close{
			color: #fff;
			background: url("/static/xueye/images/icon/close.png") no-repeat center center;
			width: 48px;
			height: 48px;
			line-height: 48px;
			display: inline-block;
			text-align: center;
			position: fixed;
			top: 80%;
			right: 50%;
			margin-right:-24px ;
			margin-top:-20px ;
			z-index: 4000;
		}
	</style>

</head>
<body>
<!-- head -->

<div id="dHead">
	<div class="head">
		<h1 style="margin: 0">智能学业
			<span id="ez">&nbsp;
				<img src="/static/public/images/E.png" alt="" height="24"/>
			</span>
			站</h1>
	</div>
</div>






<div class="show_hide2" style="position:absolute;top:0;z-index: 2000;height: 100%;overflow: hidden;display: none">
	<table cellpadding="0" cellspacing="0" style="width: 100%;height: 100%;background: rgba(0,0,0,0.8);">
		<tr>
			<td align="bottom"><a href="javascript:close()" style="font-weight: bold;color: #fff;line-height: 40px;width: 80px;display: block;text-align: center">&nbsp;&nbsp;关闭&nbsp;&nbsp;</a></td>
		</tr>
		<tr>
			<td valign="top" align="center"><img src="http://zt.wisecareer.org/static/zhituo/images/fx_content.jpg" alt="" width="100%"  class="fx_enter"/></td>
		</tr>
	</table>
</div>


<div id="dBody2" style="top:55px;bottom: 110px;background: #fff;">
	<div class="mycontent" style="height:100%;background: #fff;margin: 0">
		<div class="cleardiv10"></div>
		<div class = "wrapper" >
			<ul class = "menu" >
				<li>
					<div class="prize" onclick="location.href='/scores/index/'">
						<h3><img src="/static/xueye/images/home.png" alt="" class="img"/></h3>
						<p class="p_title">志愿填报</p>
						<br/>
					</div>
				</li>
				<li>
					<div class="prize" onclick="location.href='/jcyj/index/'">
						<h3><img src="/static/xueye/images/home2.png" alt="" class="img"/></h3>
						<p class="p_title">3+3选科</p>
						<!--<img src="//zxedu.wisecareer.org/images/tanhao.png" alt="" width="25" class="yujing"/>-->
						<br/>
					</div>
				</li>
				<li>
					<div class="prize" onclick="location.href='/dynamic/index'">
						<h3><img src="/static/xueye/images/home3.png" alt="" class="img"/></h3>
						<p class="p_title">学科动态</p>
						<img src="/static/public/images/new.gif" alt="" width="30" class="yujing" style="top:-5px;right: -5px;"/>
						<br/>
					</div>
				</li>
				<li>
					<div class="prize" onclick="location.href='/presentation/index/'">
						<h3><img src="/static/xueye/images/home4.png" alt="" class="img"/></h3>
						<p class="p_title">我的报告</p>
						<!--<img src="//zxedu.wisecareer.org/images/tanhao.png" alt="" width="25" class="yujing"/>-->
						<br/>
					</div>
				</li>
				<img src="/static/xueye/images/yuan2.png" alt="" width="100%"/>
				<img src="/static/xueye/images/yuan_green.png" alt="" width="40%" id="yuan_blue"/>
				<img src="" alt="" style="width:35vw;height: 35vw" class="li_num" id="li_num2" onclick="location.href='/presentation/index/'"/>
				<img src="/static/xueye/images/20170711143700_510.png" alt="" style="width:35vw;height: 35vw" class="li_num" id="li_num1"/>
				<script>
					var height=document.getElementById('li_num2');
					var n=true;
					setInterval(function(){
						if(n==true){
							$('#li_num2').fadeOut(1500);
							$('#li_num1').fadeIn(1500);
							n=false;
						}else{
							$('#li_num1').fadeOut(1500);
							$('#li_num2').fadeIn(1500);
							n=true;
						}
					},3000)
				</script>

			</ul>
			<div class="cleardiv20"></div>
			<div class="cleardiv20"></div>
			<div class="cleardiv20"></div>
		</div>
		</div>
</div>


 
<div id="dFoot" style="background: none;border-top: 5px solid #ebebeb">
	<img src="//xueye.wisecareer.org/images/xueye/jian_xiang.png" alt="" id="jiaxiang"/>
	<table style="background: #fff;color: #666;padding: 30px 0;display: none" width="100%" id="table_1">
		<tr>
			<td align="center" style="border-right: 1px solid #ebebeb;font-size: 16px" onclick="location.href='/aggregate/major_search/'">专业检索</td>
			<td align="center" style="font-size: 16px" onclick="location.href='/aggregate/school_search/'">大学检索</td>
			<td align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" onclick="location.href='/collection/major/'">我的关注</td>
		</tr>
	</table>
	<table style="background: #fff;color: #666;padding: 0" width="100%"  id="table_2">
		<tr>
			<td align="center" style="border-right: 1px solid #ebebeb;font-size: 16px;position: relative" onclick="location.href='/presentation/branch_list/'">学科能力<img src="//www.wisecareer.org/images/new.gif" alt="" width="20" class="yujing" style="top: 3px;right:3px"/></td>
			<td align="center" style="font-size: 16px">&nbsp;</td>
			<td align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" onclick="location.href='/character/guide/'" id="xingge">性格测评<img src="//www.wisecareer.org/images/new.gif" alt="" width="20" class="yujing" style="top: 5px;right:5px"/></td>
		</tr>
		<tr>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" style="border-right: 1px solid #ebebeb;font-size: 16px" onclick="location.href='/aggregate/major_search/'">专业检索</td>
			<td align="center" style="font-size: 16px" onclick="location.href='/aggregate/school_search/'">大学检索</td>
			<td align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" onclick="location.href='/collection/major/'">我的关注</td>
		</tr>
		<tr>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" style="border-right: 1px solid #ebebeb;font-size: 16px" onclick="location.href='/dynamic/chart/'">竞争数据</td>
			<td align="center" style="font-size: 16px" onclick="location.href='/user/index/'">个人中心</td>
			<td align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" onclick="location.href='/message/index/'">消息中心</td>
		</tr>
	</table>
</div>

<div class="show_hide" style="display: none">
	<div class="class_bg"></div>
	<div class="class_content" style="background: none">
		<div style="position: fixed;top: 20%;left: 0;z-index: 3000;width: 100%;text-align: center">
			<img src="/static/xueye/images/hongbao.png" alt="" width="70%" id="hb"/>
		</div>
		<input type="button" value="" onclick="hide();" id="close"/>
	</div>
</div>

<script>
	$(function(){
		isHaveLogin('/index/index');
	}); 
 
</script> 

<script>

var uid = localStorage.CURRENT_USER_ID;
	var userinfo=0;
	$.ajax({ url:WEB_API_URL + "user/userstatus?"  , async: false,data:{token: uid}, success: function(json){
		userinfo=json.data.userinfo;
		var link="location.href='http://fx.wisecareer.org/index/become?type=1&token="+uid+"'";
		$('img.fx_enter').attr("onclick",link);
//		if(json.data.isretail==0){
//			$('div.show_hide2').show();
//		}else{
//			$('div.show_hide2').hide();
//		}
	}})  
	if(userinfo == 0 && uid !=''){
		layer.open({
			content: '请先完善资料'
			,shadeClose: false
			,btn: '我知道了'
			,yes: function(index){
				layer.close(index);
				window.location.href='/user/userinfo/';
			}
		});
	}
function close(){
	$('div.show_hide2').hide();
}


$('#jiaxiang').toggle(function(){
		$(this).attr('src','/static/xueye/images/jia_xiang.png');
		$('#table_2').fadeOut(500,function(){$('#table_1').fadeIn(500)});

	},function(){
		$(this).attr('src','/static/xueye/images/jian_xiang.png');
		$('#table_1').fadeOut(500,function(){$('#table_2').fadeIn(500)});
	});
	$('.danxuan3>li>a').live('click',function(){
		var tag = $(this).attr('tag');

		$('.danxuan3>li>input[type="hidden"]').remove();
		$(this).after('<input name="cityid" type="hidden"  value='+tag+' />' );
 
		$('.danxuan3>li>a').removeClass("this");
		$('.danxuan3>li>img').hide();
		$(this).addClass("this");
		$(this).next().next().show();
	});


	var token = localStorage.CURRENT_USER_ID;
	$.getJSON(WEB_API_URL + "user/xygetuser?", {'token': token}, function (json) {
		if(json.data.pic==''){
			$('#li_num2').attr('src','/static/xueye/images/20170711143700_510.png')
		}else{
			$('#li_num2').attr('src','http://www.wisecareer.org/od/'+json.data.pic)
		}

//		if(json.data.ots==0){
//			$('#xingge').attr('onclick','location.href=\'/character/notice/\'');
//		}else{
//			$('#xingge').attr('onclick','location.href=\'/character/see/\'');
//		}

		if(json.data.hasid==0){
			$('.show_hide').show();
		}else{
			$('.show_hide').hide();
		}
		$('#hb').attr('onclick','location.href=\'/user/collar?id='+json.data.id+'\'')
	})

	
</script>

<script>
	wx.config({
		debug: false,//关闭调试模式
		appId: '<?php echo htmlentities($signPackage["appId"]); ?>',
		timestamp: <?php echo htmlentities($signPackage["timestamp"]); ?>,
		nonceStr: '<?php echo htmlentities($signPackage["nonceStr"]); ?>',
		signature: '<?php echo htmlentities($signPackage["signature"]); ?>',
		/*appId: 'xxxxxx',
		 timestamp: xxxxx,
		 nonceStr: 'xxxxx',
		 signature: 'xxxxx',*/

		jsApiList: [
			// 所有要调用的 API 都要加到这个列表中
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ'
		]
	});
	wx.ready(function () {

		// 2. 分享接口
		// 2.1 监听"分享给朋友"，按钮点击、自定义分享内容及分享结果接口

		wx.onMenuShareAppMessage({
			title: '智能学业e站，您身边的生涯规划专家！',
			desc: '国内唯一生涯大数据动态分析及学业决策智能支持平台',
			link: 'http://xy.wisecareer.org/index/index',
			imgUrl: 'http://xy.wisecareer.org/static/xueye/images/20170711143700_510.png',//自定义图片地址
			trigger: function (res) {
				// alert('用户点击发送给朋友');
			},
			success: function (res) {
				//alert('已分享');
				//   window.location.href = 'http://m.duolehuan.com/';
			},
			cancel: function (res) {
				//alert('已取消');
			},
			fail: function (res) {
				//alert(JSON.stringify(res));
			}
		});

		// 2.2 监听"分享到朋友圈"按钮点击、自定义分享内容及分享结果接口
		wx.onMenuShareTimeline({
			title: '快来学业e站吧！',
			desc: '国内唯一生涯大数据动态分析及学业决策智能支持平台',
			link: 'http://xy.wisecareer.org/index/index',
			imgUrl: 'http://xy.wisecareer.org/static/xueye/images/20170711143700_510.png',//自定义图片地址
			trigger: function (res) {
				// alert('用户点击分享到朋友圈');
			},
			success: function (res) {
				//alert('已分享');
				//window.location.href = '';
			},
			cancel: function (res) {
				//alert('已取消');
			},
			fail: function (res) {
				//alert(JSON.stringify(res));
			}
		});
	});

	function hide(){
		$('.show_hide').hide();
	}

	function show(){
		$('.show_hide').show();
	}
</script>





</body>
</html>