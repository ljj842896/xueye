<?php /*a:1:{s:74:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\login\index.html";i:1545031458;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link rel="icon" href="//www.wisecareer.org/images/favicon.ico"  type="image/x-icon">
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/yuan.css"/>
	<link href="//www.wisecareer.org/css/zhituo/login.css" type="text/css" rel="stylesheet" />
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>
    <script src="/static/zhituo/js/com.min.js"></script>
    <script src="/static/zhituo/js/base.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body>

<div id="dHead">
	<div class="head">
		<h1 style="font-family:Arial,'微软雅黑', Helvetica, sans-serif;">用户登录</h1>
	</div>
</div>
<div id="dBody" style="overflow-y:hidden; overflow-x:hidden;table-layout: fixed;word-wrap:break-word;word-break:break-all;">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner" style="background: none">
		<div class="cleardiv10"></div>
 <div id="user_login"> 
		<form method="post" action="/login/login/"  id="frmlogin" name="frmlogin">
			<ul id="login_list">
				<li><input type="text"  placeholder="请输入（学业e站）用户名/手机号" class="zhanghao shuru" id="user" name='user' value=""/><span class="login_tishi" style="top: 8px">请输入学业e站账号！</span></li>
				<li><input type="password" placeholder="请输入密码" class="mima shuru" id="passwd" name='passwd'  value="" /><span class="login_tishi">请输入密码！</span></li>
			</ul>
		<input type="hidden" id="openid" name="openid"  value="">
			<input type="button" value="登录" class="tijiao"  onClick="doMemberdologin('/index/index');" style="margin: 5px auto"/>
			<h4><span><a href="/login/duanxin">短信验证登录</a></span>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<span><a href="/login/password">忘记密码</a></span>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<span><a href="/reg/index">免费注册</a></span></h4><br/>
			<div class="cleardiv20"></div>
			<div class="cleardiv20"></div>
			<div class="cleardiv20"></div>

		</form>
 </div>
		</div>
	</div>
</div>

<script>
function tourist() {
	$.post( "/user/logout")
	localStorage.CURRENT_USER_ID = ""; 
	localStorage.CURRENT_USER_TypeLX = "";  
	location.href = "/index/index/"
 }
 
	
	$(".zhanghao").focus(function(){
		$(this).next().hide();
	})
	$(".zhanghao").blur(function(){
		var zhanghao= $(".zhanghao").val();
		if(zhanghao==""){
			$(this).next().html('请输入账号！');
			$(this).next().show();
			return;
		}
		if(zhanghao.length<6){
			$(this).next().html('账号必须多于5位！');
			$(this).next().show();
			return;
		}
	});
	$(".mima").focus(function(){
		$(this).next().hide();
	})
	$(".mima").blur(function(){
		var mima= $(".mima").val();
		if(mima==""){
			$(this).next().html('请输入密码！');
			$(this).next().show();
			return;
		}
		if(mima.length<6){
			$(this).next().html('密码必须多于5位！');
			$(this).next().show();
			return;
		}
	})
	function sendMessage(){
		var partten = /^\d{10,13}$/;
		if (!document.getElementById("zhanghao").value) {
			layer.open({
				content: '账号不能为空！'
				,btn: '我知道了'
			});
			return;
		}
		if (!document.getElementById("mima").value) {
			layer.open({
				content: '密码不能为空！'
				,btn: '我知道了'
			});
			return;
		} 
	}

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
		title: '跨行业职业体验及现场实习平台',
		desc: '近30个行业、岗位、专题的职业（案例）体验、现场实习、多元随机评价、精准定位“职业教练”...... 积累经验值，换得未来好工作。',
		link: 'http://zt.wisecareer.org/login/index',
		imgUrl: 'http://www.wisecareer.org/images/zhituo.jpg',//自定义图片地址
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
		title: '跨行业职业体验及现场实习平台',
		desc: '近30个行业、岗位、专题的职业（案例）体验、现场实习、多元随机评价、精准定位“职业教练”...... 积累经验值，换得未来好工作。',
		link: 'http://zt.wisecareer.org/login/index',
		imgUrl: 'http://www.wisecareer.org/images/zhituo.jpg',//自定义图片地址
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
</script>
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1273552666'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/z_stat.php%3Fid%3D1273552666'type='text/javascript'%3E%3C/script%3E"));</script></body>
</html>