<?php /*a:1:{s:73:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\user\index.html";i:1545031462;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/yuan.css"/>
	<link href="//www.wisecareer.org/css/zhituo/user_info.css" type="text/css" rel="stylesheet" />
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>
   <script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body>

<!-- head -->
<div id="dHead">
	<div class="head">
		<h1 style="margin: 0">个人中心</h1>
		<a href="/index/index" class="return"><i class="return-right"></i></a>
	</div>
</div>

<div id="dBody">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner" style="padding:15px 0">
			<table width="100%" id="user_xx">
				<tr>
					<td rowspan="3" align="center" valign="top"><div class="portrait head_pic_man head_pic" onClick="goPermissionUrl('/user/picture?page=user&name=index');"><img id = 'pic' src="" alt="" style="border: 1px solid #ebebeb;"/></div></td>
					<td id = 'name'></td>
				</tr>
				<tr>
					<td id = "school"></td>
				</tr>
				<!--<tr>-->
					<!--<td id="ztb">职拓币：<span id="money">0</span></td>-->
				<!--</tr>-->
				<tr>
					<td id="vip"><span id="status"></span>  |  到期时间 : <span id="xy_e_time"></span> </td>
				</tr>
			</table>
		</div>
		<div class="cleardiv5"></div>
		<div class="inner">
	<div class="my-index-list">
		<ul>
			<li style="border-top:none;"><a href="/user/userinfo"><i class="user-center-1"></i>个人资料</a></li>
			<li><a href="/user/password"><i class="user-center-2"></i>修改密码</a></li>
			<!--<li><a href="javascript:Not_open();"><i class="user-center-4"></i> 积分日志-->
				<!--javascript:goPermissionUrl('/user/jifen.html');-->
				<!--<span style="color: #f00">100</span>-->
			<!--</a></li>-->
			<li><a href="/user/wallet"><i class="user-center-3"></i>我的账户</a></li>
			<!--<li><a href="javascript:goPermissionUrl('/user/label.html');"><i class="user-center-5"></i>教练标签</a></li>-->
			<!--<li><a href="javascript:Not_open();"><i class="user-center-5"></i>推荐关注&nbsp;&nbsp;<span style="font-size: 14px"><img src="//www.wisecareer.org/images/zhituo/icon/fabulous_yes.png" alt="" width="18" style="position: relative;top: 3px"/>&nbsp;&nbsp;3人&nbsp;&nbsp;|&nbsp;&nbsp;<img src="//www.wisecareer.org/images/zhituo/icon/ic_thanked.png" alt="" width="18" style="position: relative;top: 4px"/>&nbsp;&nbsp;2人</span></a></li>-->
			<!--/user/privacy-->
			<!--<li><a href="javascript:goPermissionUrl('/user/convention');"><i class="user-center-11"></i>学员公约</a></li>-->
			<!--<li><a href="/user/auth" onclick = "return guanbi()"><i class="user-center-7"></i>教练权限</a></li>-->
			<li><a href="javascript:void(0);" style="background: none"><i class="user-center-8"></i>软件版本<span>v 1.1.2</span></a></li>
			<li><a href="javascript:void(0);" style="background: none"><i class="user-center-4"></i>联系我们<span>400-074-0520</span></a></li>
		</ul>
	</div>
		</div>
		<div class="inner" style="padding:0;margin-top: 10px">
			<div class="my-index-list">
				<input type="submit" id="tuichu"  value="退出登录"  onclick="doLogout()"/>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">

	$(function(){
		isHaveLogin('/user/index');
	});

	function Not_open(){
		layer.open({
			content: '暂未开通'
			,btn: '我知道了'
			,shadeClose: false
		});
	}
	var token = localStorage.CURRENT_USER_ID;
	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/index/index');
	}

	$.getJSON(WEB_API_URL + "user/ztgetuser?", {'token': token}, function (json) {

		if(json.status ==0){
			var array = json.data;
			var pic  = $('#pic').attr('src','http://www.wisecareer.org/od//'+array.pic);
			if(array.status ==1){
				var statusstr = 'vip会员';
				var xy_e_timestr = array.zt_e_time
			}else if(array.status ==2){
				var statusstr = '机构会员';
				var xy_e_timestr = array.zt_e_time
			}else{
				var statusstr = '普通会员';
				var xy_e_timestr = '未开通';
			}
			$('#status').html(statusstr);
			$('#money').html(array.money);
			$('#xy_e_time').html(xy_e_timestr);
			$('#name').html(array.user_name);
			$('#school').html(array.school_name);
			$('#phone').html(array.advisory);
		}else{
			window.location.href='/index/index'
		}
	})
</script>

<script>
	var EXTERNAL = getCookie('EXTERNAL')
	if(EXTERNAL ==1){
	    $('#tuichu').remove();
	}
function doLogout() {  
	layer.open({
		content: '你确定退出登录吗？'
		,shadeClose: false
				,btn: ['确定', '取消']
				,yes: function(index){
							var uid = localStorage.CURRENT_USER_ID;
							$.post( "/user/logout")
							$.post(WEB_API_URL + "login/logout", {
							token: uid,
							r: Math.random()
							}, function(data) {

					         var result = eval(data);
							 localStorage.CURRENT_USER_ID = ""; 
							 localStorage.CURRENT_USER_TypeLX = ""; 
							 localStorage.CURRENT_USER_VIP = "";
							 localStorage.CURRENT_userinfo = "";


             if (result.status == 0) {
							location.href = "/login"
			}else {
				layer.open({
				content: result.msg
				,btn: '我知道了'
				});
				return;
				}
    })
				}
			}); 
			
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
			link: 'http://zt.wisecareer.org/user/index',
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
			link: 'http://zt.wisecareer.org/user/index',
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