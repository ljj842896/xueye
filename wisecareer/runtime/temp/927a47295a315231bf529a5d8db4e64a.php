<?php /*a:1:{s:72:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\reg\index.html";i:1545031460;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/yuan.css"/>
	<link href="//www.wisecareer.org/css/zhituo/login.css" type="text/css" rel="stylesheet" />
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="http://xueye.wisecareer.org/js/layer/mobile/need/layer.css"/>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<script>
		$(document).ready(function(){
			$("#phone").focus(function(){
				$(this).next().html("");
				$(this).next().hide();
			});
			$('#phone').bind('blur',function(){
				var tel = $("#phone").val();

				var parent = $(this).parent();
				for(var i=0;i<11;i++){
					var str= tel.substring(i,i+1);
					if(isNaN(str)){
						$(this).next().html("输入必须为数字");
						$(this).next().show();
					}
				}
				if(tel.length==0){
					$(this).next().html("输入号码不能为空");
					$(this).next().show();
				}else if(tel.length>11||tel.length<11){
					$(this).next().html("输入号码为11位");
					$(this).next().show();
				}

			});

			$("#mm").focus(function(){
				$(this).next().html("");
				$(this).next().hide();
			})
			$("#mm").blur(function(){
				var psw = $("#mm").val();
				if(psw==""){
					$(this).next().html("不能为空");
					$(this).next().show();
					return;
				}
				if(psw.length<6||psw.length>16){
					$(this).next().html("密码为6-16个字符");
					$(this).next().show();
					return;
				}
			})
			$("#qrmm").focus(function(){
				$(this).next().html("");
				$(this).next().hide();
			})
			$("#qrmm").blur(function(){
				var psw1 = $("#qrmm").val();
				if(psw1!=$("#mm").val()){
					$(this).next().html("必须与密码相同");
					$(this).next().show();
					return;
				}
				if(psw1==""){
					$(this).next().html("不能为空");
					$(this).next().show();
					return;
				}
			})

		})
	</script>
	<style>
		.login_tishi{
			right: 5px;
		}

		#myform>table td{
			position: relative;
		}
		#gongyue p{font-size: 14px;color: #666;line-height: 20px;text-indent: 28px;}
		#gongyue h3{font-size: 14px;line-height: 30px;font-weight: bold;color: #666;}
	</style>
</head>
<body>

<!-- head -->

<div id="dHead">
	<div class="head">
		<h1 style="font-family:Arial,'微软雅黑', Helvetica, sans-serif;">免费注册</h1>
		<a href="javascript:history.go(-1)" class="return"><i class="return-right"></i></a>
	</div>
</div>

<!-- home -->

<div id="dBody">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner" style="background: none">
			<br/>
			<form action="/reg/reguser" method="post" id='myform' name="myform">
				<!-- <input type="text" placeholder="账号" class="mima shuru" id="zh" name="zh" autocomplete="off" onblur="myFunction(this)" /> -->
				<table width="100%">
					<tr><td colspan="2"><input type="number" placeholder="手机号码"   class="photo shuru" id="phone" name="phone" autocomplete="off"/><span class="login_tishi"></span></td></tr>
					<tr><td colspan="2"><input type="password" placeholder="密码" class="mima shuru" id="mm" name="mm" autocomplete="off"/><span class="login_tishi"></span></td></tr>
					<tr><td colspan="2"><input type="password" placeholder="确认密码" class="mima shuru" id="qrmm" name="qrmm" autocomplete="off"/><span class="login_tishi"></span></td></tr>
					<tr><td width="50%"><input type="number" placeholder="请输入验证码" class="yanzheng" id="yz" name="yz" autocomplete="off"/></td><td width="50%"><input type="button" value="获取验证码" id="btnSendCode" class="huoqu" onclick="sendMessage()" /></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2">&nbsp;&nbsp;<input type="checkbox" id="xieyi" style="height: 16px;width: 16px"/>&nbsp;&nbsp;&nbsp;&nbsp;<span id="yuedu" style="padding-top: 10px;color: #f58020;font-size: 14px;" onclick="gy_show()">请认真阅读‘用户协议’</span></td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><td colspan="2"><input type="button" class="tijiao" id='btn_yes' value="提交" onclick=" return Verify()"/></td></tr>
				</table>
			<!-- <input type="hidden" nane="nyz" value="" id="nyz" /> -->
			</form>
			<div class="cleardiv20"></div>
		</div>
	</div>
</div>

<div class="show_hide" style="display: none">
	<div class="class_content" style="background: none;padding: 0;overflow-y: scroll">
		<div class="head" style="position: fixed;top: 0;left: 0;z-index: 1500">
			<h1 id="gongyue_title" style="font-family:Arial,'微软雅黑', Helvetica, sans-serif;"></h1>
			<a href="javascript:gy_hide()" class="return"><i class="return-right"></i></a>
		</div>
		<div class="cleardiv55"></div>
		<div id="gongyue" style="padding: 0 10px;background: #fff;overflow-y: scroll;min-height: 350px"></div>
		<div class="cleardiv20"></div>
	</div>
</div>

<script>

	var id = getUrlParam("id");
	var obj = getUrlParam("obj");
	var schoolname ;
	var schoolcode;
	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/login/index');
	}
	if(obj==0){
		$('li.jigou').remove();
	}
	function scrollLoad(){
		$.getJSON( WEB_API_URL +  "ajax/person?", {id:22}, function (json) {
			var data=json.data;
			var str1='';
			str1+='<div class="cleardiv20"></div>'+data['res']+'<div class="cleardiv10"></div><input type="button" class="btn_yes" id="xy_yes" value="确认" onclick="gy_yes();"/><div class="cleardiv10"></div>';
			$('#gongyue').html(str1);
			$('#gongyue_title').html(data['headlines']);
		});
	}
	scrollLoad();

	function gy_show(){
		$('.show_hide').show();
	}
	function gy_hide(){
		$('.show_hide').hide();
	}
	function gy_yes(){
		$('.show_hide').hide();
		$('#xieyi').attr('checked','checked');
	}
	var yuedu=true;
	$('input[type="checkbox"]').click(function(){
		if(yuedu==true){
			$(this).attr("checked",'true');
			yuedu=false;
		}else{
			$(this).removeAttr("checked");
			yuedu=true;
		}
	});

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
		}

		if(obj==0){

		}else{
			if(document.getElementById("school").value==""){
				layer.open({
					content: '请输入学校名称！'
					,shadeClose: false
					,btn: '我知道了'
				});
				return;
			}else if(document.getElementById("schoolid").value==""){
				layer.open({
					content: '请输入学校验证码！'
					,shadeClose: false
					,btn: '我知道了'
				});
				return;
			}
		}

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
			data: "sms=1&vcode="+vcode+"&tel=" + $("#phone").val(),

			success: function (data){

				if(data.status==000){
					layer.open({
						content: '验证码已发送到你的手机！'
						,shadeClose: false
						,btn: '我知道了'
					});
					//$('#nyz').val(msg);
				}else{
					layer.open({
						content: data.msg
						,shadeClose: false
						,btn: '我知道了'
					});
					window.clearInterval(InterValObj);//停止计时器
					$("#btnSendCode").removeAttr("disabled");//启用按钮
					$("#btnSendCode").val("重新发送");

				}
			}
		})
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
		var dianhua = $("#phone").val();
		var password = $("#mm").val();
		var passwords = $("#qrmm").val();
		var code = $("#yz").val();
		var schoolid = $("#schoolid").val();
		var schoolname = $("#school").val();

		if (!$('input[type="checkbox"]').attr('checked')) {
			layer.open({
				content: '请确认用户协议！'
				,shadeClose: false
				,btn: '我知道了'
			});
		}else{
			console.log(dianhua);
			console.log(password);
			console.log(passwords);
			console.log(schoolid);
			console.log(schoolname);
			$.ajax({
				type:"POST",
				url:WEB_API_URL+"reg/reguser",
				data:"type=3&phone="+dianhua+"&mm="+password+"&qrmm="+passwords+"&yz="+code,
				cache:false, //不缓存此页面
				xhrFields: {withCredentials: true},
				crossDomain: true,
				success:function(data){
					if(data.status==0){
						localStorage.CURRENT_USER_ID = data.data;
						localStorage.CURRENT_USER_TypeLX = 1;
						localStorage.CURRENT_userinfo = 0;
						layer.open({
							content: "注册成功!"
							,shadeClose: false
							,btn: '知道了',
							yes:function(index){
								location.href='/index/index/';
								layer.close(index);
							}
						});
					}else{
						layer.open({
							content: data.msg
							,shadeClose: false
							,btn: '知道了',
							yes:function(index){
								layer.close(index);
							}
						});
					}
				}
			});
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
			link: 'http://zt.wisecareer.org/reg/index',
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
			link: 'http://zt.wisecareer.org/reg/index',
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
<!-- nav -->
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1273552666'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/z_stat.php%3Fid%3D1273552666'type='text/javascript'%3E%3C/script%3E"));</script></body>
</html>