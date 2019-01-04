<?php /*a:1:{s:76:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\login\duanxin.html";i:1545031458;}*/ ?>
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
	<style>
		.btn_yes{
			margin: 10px auto;
		}

		#ez>img {
			position: relative;
			top: 2px;
			left: -1px;
		}
	</style>
</head>
<body>


<div id="dHead">
	<div class="head">
		<h1 style="margin: 0">短信登录</h1>
		<a href="javascript:history.go(-1)" class="return"><i class="return-right"></i></a>
	</div>
</div>

<div id="dBody" style="overflow-y:hidden; overflow-x:hidden;table-layout: fixed;word-wrap:break-word;word-break:break-all;">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner" style="background: none">
			<div class="cleardiv20"></div>
 <div id="user_login">
		<form method="post" action="/login/dologin/"  id="frmlogin" name="frmlogin">
			<ul id="login_list">
				<li><input type="text"  placeholder="请输入用户名/手机号" class="zhanghao shuru" id="phone" name='user' value=""/><span class="login_tishi">请输入账号！</span></li>
				<li>
					<table width="100%">
						<tr>
							<td width="50%" valign="top"><input type="number"  id="yz"  name="yz" placeholder="请输入验证码" class="yanzheng shuru" style="padding-left: 20%;width: 75%"/></td>
							<td width="1%"></td>
							<td width="49%" valign="top"><input type="button" value="获取验证码" id="btnSendCode" class="tijiao" onclick="sendMessage()" /></td>
						</tr>
					</table>
				</li>
				<input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
			</ul>
		<input type="hidden" id="openid" name="openid"  value="">
		<div class="cleardiv10"></div>
			<!--<td colspan="2" align="center" style="font-weight:bold;color:#00bb0c;line-height: 30px;background: url('/static/xueye/images/shuomingbg.png') no-repeat center center">特别说明</td>-->
			<input type="button" value="登录" class="tijiao"  onClick="codedologin();"/>
			<div class="cleardiv10"></div>
			<h4 style="font-weight:bold;line-height: 30px;"><span><a href="/login/index" style="color:#666">&nbsp;&nbsp;账号登录&nbsp;&nbsp;</a> | <a href="/login/password" style="color:#666">&nbsp;&nbsp;忘记密码&nbsp;&nbsp;</a> | <a href="/reg/explain" style="color:#666">&nbsp;&nbsp;免费注册&nbsp;&nbsp;</a></span></h4>

		</form>
	 <div class="cleardiv20"></div>
	 <div class="cleardiv20"></div>
	 <div class="cleardiv20"></div>
	 <div class="cleardiv20"></div>
 </div>
	</div>
	</div>
</div>



</body>
<script>
	var count = 60; //间隔函数，1秒执行
	var curCount;//当前剩余秒数
	function sendMessage() {

		curCount = count;
		var partten = /^\d{10,13}$/;

		if (!partten.test(document.getElementById("phone").value)) {
			layer.open({
				content: '请输入正确的手机号码！'
				,shadeClose: false
				,btn: '我知道了'
			});

			return;
		}else{
			//产生验证码

			//设置button效果，开始计时
			$("#btnSendCode").attr("disabled", "true");
			$("#btnSendCode").val("" + curCount + "秒内输入");
			InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
//向后台发送处理数据
			var vcode = $('#vcode').val();
			$.ajax({
				url: "/ajax/code",
				type: "Post",
				data: "sms=3&vcode="+vcode+"&tel=" + $("#phone").val(),
				success: function (data){
					if(data.status == 20000){
						layer.open({
							content: '验证码已发送到你的手机!'
							,shadeClose: false
							,btn: '我知道了'
						});
						//$('#nyz').val(msg);
					} else {
						layer.open({
							content: data.msg
							,shadeClose: false
							,btn: '我知道了'
						});
						window.clearInterval(InterValObj);//停止计时器
						$("#btnSendCode").removeAttr("disabled");//启用按钮
						$("#btnSendCode").val("重新发送");
						myform.yz.focus();
					}
				}
			})
		}
	}
	//timer处理函数
	function SetRemainTime() {
		if (curCount == 0) {
			window.clearInterval(InterValObj);//停止计时器
			$("#btnSendCode").removeAttr("disabled");//启用按钮
			$("#btnSendCode").val("重新发送");
			$.ajax({
				url: "/ajax/vcode",
				type: "Post",
				success: function (data){
					$('#vcode').val(data);
				}
			})
			//code = ""; //清除验证码。如果不清除，过时间后，输入收到的验证码依然有效
		}else {
			curCount--;
			$("#btnSendCode").val("" + curCount + "秒内输入");
		}
	}

	function Verify(){
		var tel = $("#phone").val();
		var yz = $("#yz").val();
		var obj = $('#btn_yes');
		document.submit();

	}

</script>
<script>
    var OPENID = getCookie('openid')  ;

	var userid = localStorage.CURRENT_USER_ID;
	var  tokenid = "";

	var   VipData = "";

	if (userid != undefined && userid !=''){

		$.ajax({ url:WEB_API_URL + "user/userstatus?" , async: false,data:{token: userid}, success: function(json){

			if(json.status == 0) {
				if(json.data.status==1){
					vip = 1;
				}
				tokenid = 1;
				localStorage.CURRENT_USER_VIP = json.data.status;
				localStorage.CURRENT_userinfo = json.data.userinfo;
				location.href='/index/index/';
			}else{
				tokenid = 0;
				localStorage.CURRENT_userinfo = 0;
				localStorage.CURRENT_USER_ID = "";
				localStorage.CURRENT_USER_TypeLX = "";
				localStorage.CURRENT_USER_VIP = "";
			}



		}});
	} else {
		tokenid = 0;
	}


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
       
		$(".zhanghao").focus(function(){
			$(this).next().hide();
		})

		$(".mima").focus(function(){
			$(this).next().hide();
		})

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
			link: 'http://zt.wisecareer.org/login/duanxin',
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
			link: 'http://zt.wisecareer.org/login/duanxin',
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
