<?php /*a:1:{s:77:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\jiaolian\index.html";i:1545031456;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link href="//www.wisecareer.org/css/zhituo/user_1.css" type="text/css" rel="stylesheet" />
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<!--[if IE]>
	<script src="//www.wisecareer.org/js/html5shiv.min.js"></script>
	<![endif]-->
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/> 
	<link rel="stylesheet" type="text/css" href="//www.wisecareer.org/css/zhituo/layui.css" /> 
	<style>
		input.consultation{
			background: #f58020;
			width: 100%;
			height: 40px;
			border: none;
			color: #fff;
			font-size: 16px;
			font-weight: bold;
			-webkit-appearance: none;
		}
		td{
			font-size: 16px;
		}
	</style> 
</head>
<body>

<!-- head -->
	<div class="head" style="position: fixed;top: 0;left: 0;z-index: 300">
		<h1 style="margin: 0">我的教练</h1>
		<a href="/index/index" class="return"><i class="return-right"></i></a>
		<a href="/jiaolian/star" id="screen_btn"><img src="http://www.wisecareer.org/images/zhituo/icon/star_icon.png" alt="" height="16" style="position: relative;top: -2px"/>&nbsp;推荐</a>
	</div>

<!-- home -->
<div id="dBody">
	<div class="cleardiv50"></div>
		<div class="layui-tab">
			<ul class="layui-tab-title" style="height: 32px;width: 100%;text-align: center;padding: 15px 0;background: #fff">
				<li num="1" onclick="Not_open()" style="width: 35%;line-height: 32px">已签约</li>
				<li class="layui-this" num="2" style="width: 35%;line-height: 32px">我关注</li>
			</ul>
			<div class="layui-tab-content">
				<div class="layui-tab-item">
					<div class="mycontent" id="tab1">

					</div>
				</div>
				<div class="layui-tab-item layui-show">
					<div class="mycontent" id="tab2">

					</div>
				</div>
			</div>
		</div>

</div>

<script src="//www.wisecareer.org/js/flickity-docs.min.js"></script>
<script type="text/javascript">
	$(function(){
		isHaveLogin('/jiaolian/index');
	});

	function Not_open(){
		layer.open({
			content: '暂未开通'
			,btn: '我知道了'
			,shadeClose: false
		});
	}

	function GetRequest() {
		var url = location.search; //获取url中"?"符后的字串

		if (url.indexOf("?") != -1) {
			var str = url.substr(1);
			return str;
		}
	}

	var uid = localStorage.CURRENT_USER_ID;
	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/index/index');
	}
	var text1='';
	var text2='';
	var text3='';

	$.ajax({ url:WEB_API_URL + "coachs/mycoach?" , async: false,data:{token: uid}, success: function(json) {
		var data = json.data;
		var str1 ='';
		if(data==''){
			str1+='<table width="100%"><tr><td align="center" style="line-height: 60px;background: #fff;font-size: 16px">暂无关注教练</td></tr></table>';
		}else{
			$.each(json.data, function (index, array) {
				str1 += '<table width="100%" style="border-bottom: 5px solid #ebebeb;padding: 10px 0;background: #fff"><tr><td width="10"></td>';
				str1 += '<td rowspan="5" width="100" valign="middle" align="center" onclick="location.href=\'/jiaolian/space?id='+array['id']+'\'"><img src="'+array['pic']+'" alt="" width="70"/><h3 style="padding: 5px 0;">'+array['user_name']+'</h3></td>';
				str1 += '<td width="10%"></td><td onclick="location.href=\'/jiaolian/space?id='+array['id']+'\'">职业写真 <span class="floatright"><span>'+array['phpto']+'</span>张</span></td>';
				str1 += '<td width="25" valign="middle" align="right" style="background: url(//www.wisecareer.org/images/xueye/icon/arrow-right-xiao.png) no-repeat center right">&nbsp;</td>';
				str1 += '<td width="10"></td></tr><tr><td width="10"></td><td></td><td></td><td></td><td width="10"></td></tr>';
				str1 += '<tr><td width="10"></td><td></td><td onclick="location.href=\'/jiaolian/space?id='+array['id']+'\'">行业动态 <span class="floatright"><span>'+array['hangye']+'</span>个</span></td>';
				str1 += '<td width="25" valign="middle" align="right" style="background: url(//www.wisecareer.org/images/xueye/icon/arrow-right-xiao.png) no-repeat center right">&nbsp;</td>';
				str1 += '<td width="10"></td></tr><tr><td width="10"></td><td></td><td></td><td></td><td width="10"></td></tr>';
				str1 += '<tr><td width="10"></td><td></td><td onclick="location.href=\'/jiaolian/space?id='+array['id']+'\'">新案例 <span class="floatright"><span>'+array['anli']+'</span>个</span></td>';
				str1 += '<td width="25" valign="middle" align="right" style="background: url(//www.wisecareer.org/images/xueye/icon/arrow-right-xiao.png) no-repeat center right">&nbsp;</td>';
				str1 += '<td width="10"></td></tr><tr><td colspan="6">&nbsp;</td></tr><tr><td width="10"></td>';
				str1 += '<td colspan="4"><input type="button" value="发起咨询" class="consultation" onclick="Not_open()"/></td>';
//				onclick="location.href=\'/jiaolian/launch/\'"
				str1 += '<td width="10"></td></tr></table>';
			})
		}
		$('div#tab1').html(str1);
		$('div#tab2').html(str1);
	}
	});


	$('#tanhao1').live('click',function(){
		layer.open({
			content: text1
			,btn: '我知道了'
		});
	});

	$('#tanhao2').live('click',function(){
		layer.open({
			content: text2
			,btn: '我知道了'
		});
	});

	$('#tanhao3').live('click',function(){
		layer.open({
			content: text3
			,btn: '我知道了'
		});
	});

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
			link: 'http://zt.wisecareer.org/jiaolian/index',
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
			link: 'http://zt.wisecareer.org/jialian/index',
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


</body>
</html>
