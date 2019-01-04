<?php /*a:1:{s:81:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\presentation\index.html";i:1545031458;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<link rel="stylesheet" type="text/css" href="//www.wisecareer.org/css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="//www.wisecareer.org/css/default.css">
	<!--[if IE]>
	<script src="//www.wisecareer.org/js/html5shiv.min.js"></script>
	<![endif]-->
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

	<style>
		.photo{
			width:100%;
			margin-top: 50px;
			position:relative;
		}
		.photo .images{
			width: 97px;
			background:url(http://xueye.wisecareer.org/images/xueye/user-bg1.png) no-repeat center top;
			background-size:96px;
			height: 59px;
			margin:-6px auto;
			padding-top: 1px;
		}
		.photo .images img{
			width:87px;
			margin: -39px auto;
			display: block;
		}
		.photo h1 {
			text-align: center;
			font-size: 24px;
			line-height: 40px;
			padding-top: 10px;
		}
		.baogao .photo h2{
			text-align: center;
			line-height: 40px;
			padding-top: 10px;
		}
		.baogao .photo h3{
			text-align: center;
			line-height: 30px;
			font-size: 14px;
		}
		.baogao .photo h3>span.num{
			font-size: 18px;
			color: #3eca07;
		}
		.baogao .photo h3>a{
			text-decoration: underline;
		}
		.baogao .photo h2>#name{
			font-size: 20px;
		}
		.baogao .photo h2>#school{
			font-size: 14px;
		}
		h5.xq_h5 {
			padding:5px 30px 5px 15px;
			cursor: pointer;
			line-height: 40px;
			font-size: 14px;
			color: #666666;
			background-image: url(http://xueye.wisecareer.org/images/xueye/icon/arrow-right-xiao.png);
			background-repeat: no-repeat;
			background-position: right center;
			border-bottom: 1px solid #ebebeb;
		}
	</style>
</head>

<body>
<!-- head -->
	<div class="head" style="position: fixed;top: 0;left: 0;z-index: 300">
		<h1 style="margin: 0">我的报告</h1>
		<a href="/index/index/" class="return"><i class="return-right"></i></a>
	</div>

<!-- home -->
<div id="dBody">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner">
			<div class="cleardiv10"></div>
			<table width="100%" style="background: #fff;padding: 15px 0" id="user_xx">
				<tr>
					<td rowspan="4" width="100"><div class="portrait head_pic_man head_pic" onClick="goPermissionUrl('/user/picture?page=presentation&name=index');"><img id = 'pic' src="" alt="" style="border: 1px solid #ebebeb;"/></div></td>
					<td width="10">&nbsp;</td>
					<td valign="middle"></td>
				</tr>
				<tr>
					<td width="10">&nbsp;</td>
					<td id="name" valign="middle"></td>
				</tr>
				<tr>
					<td width="10">&nbsp;</td>
					<td id="school" valign="middle"></td>
				</tr>
				<tr>
					<td width="10">&nbsp;</td>
					<td valign="middle"></td>
				</tr>
				<tr>
					<td colspan="3" align="center" id="qita"></td>
				</tr>
			</table>
			<div class="cleardiv10"></div>
		</div>
		<div class="cleardiv5"></div>
		<div class="baogao" style="background: #fff">
			<!--<h5 class="xq_h5" style="background: none;padding:15px 15px 0 15px;border-bottom: none;line-height: 16px;font-size: 16px">实习项目：</h5>-->
           	<!--<h5 class="xq_h5" style="background: none;padding:5px 15px;">推荐图标32位教练<span class="floatright">关注图标12教练</span></h5>-->
		</div>
	</div>
</div>



<script>
	$(function(){
		isHaveLogin('/presentation/index');
	});
//	Request = GetRequest();
	var eid = localStorage.CURRENT_USER_ID;
	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/index/index');
	}
	$.ajax({ url:WEB_API_URL + "navigation/ztPresentation?" , async: false,data:{token: eid}, success: function(json){
		var data=json.data;
		var str1='';
		var str2='';
		var sexpic='';
		var pic='';
		if(data['sex']==1){
			sexpic='//www.wisecareer.org/images/zhituo/man.png';
			if(data['pic']==null){pic='http://www.wisecareer.org/od/userpic/man_s.png'}else{pic='http://pic.wisecareer.org/od/'+data['pic']}
		}else{
			sexpic='//www.wisecareer.org/images/zhituo/woman.png';
			if(data['pic']==null){pic='http://www.wisecareer.org/od/userpic/woman_s.png'}else{pic='http://pic.wisecareer.org/od/'+data['pic']}
		}

		str2='<h4 style="text-align: center;font-size: 12px;line-height: 40px">总积分 <span style="color: #f58020;font-weight: bold">'+data['jifen']+'</span>  |  胜任力 <span style="color: #f58020;font-weight: bold">'+data['competence']+' %</span>  |  <img src="http://www.wisecareer.org/images/zhituo/icon/heart.png" alt="" height="16" style="position: relative;top: 3px"/> <span style="color: #f58020;font-weight: bold"> '+data['recom']+' </span> 次  |  <img src="http://www.wisecareer.org/images/zhituo/icon/dianzan.png" alt="" height="20" style="position: relative;top: 5px"/> <span style="color: #f58020;font-weight: bold"> '+data['atten']+' </span> 次</h4>';

		str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/tsgw\'">通识岗位能力评价 </h5>';
		str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/hyph\'">行业胜任力评价</h5>';
		str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/zysy\'">基本职业素养评价</h5>';
		str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/xxnl\'">教练评价</h5>';
		str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/demo\'">自我评价</h5>';
		str1+='<h5 class="xq_h5" onclick="location.href=\'/scene/baogao\'">现场实习报告</h5>';
		//str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/demo\'">i-观点和主观题得分<span class="floatright">'+data['defen']+'</span></h5>';
//		str1+='<h5 class="xq_h5" onclick="location.href=\'/presentation/demo\'"> <span class="floatright">11次</span></h5>';
		$('.baogao').html(str1);
		$('#pic').attr('src',pic);
		$('#name').html(data['username'])
		$('#school').html(data['titlename'])
		$('#qita').html(str2);
	}});
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
			link: 'http://zt.wisecareer.org/presentation/index',
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
			link: 'http://zt.wisecareer.org/presentation/index',
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
