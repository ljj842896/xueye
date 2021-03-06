<?php /*a:1:{s:74:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\scene\input.html";i:1545031460;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/experience.css"/>
	<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/interface.css"/>
	<!--[if IE]>
	<script src="//www.wisecareer.org/js/html5shiv.min.js"></script>
	<![endif]-->
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<style>
		a#ago,a#baogao{
			background: url(//www.wisecareer.org/images/zhituo/icon/arrow-right.png) no-repeat right center;
			background-size: 9px;
			display: block;
		}
	</style>
</head>
<body>
<!-- head -->

<div id="dHead">
	<div class="head">
		<h1 style="margin: 0">现场实习</h1>
		<a href="/index/index" class="return"><i class="return-right"></i></a>
	</div>
</div>

<form id="bomb_box" >
	<ul id="box_content">
		<!--<li style="padding: 5px 0 0 15px;"></li>-->
		<li style="padding: 10px 0 0 15px;"><input type="number" id="bomb_text" style="width: 95%;padding: 5px 0" placeholder="请输入你所报名的实习活动6位入场码"/></li>
		<li style="padding: 5px 0 0 0;text-align: center;"><input type="button" id="bomb_btn" class="btn_yes" value="确定" style="margin: 10px auto"/></li>
		<li style="padding-right: 15px;padding-top: 10px"><a href="/scene/history.html" id="ago">我的实习日志</a></li>
		<li style="padding-right: 15px;padding-top: 10px"><a href="/scene/baogao.html" id="baogao">现场实习报告</a></li>
	</ul>
</form>

<script>
	$(function(){
		isHaveLogin('/scene/input');
	});
	Request = GetRequest();
	var uid = localStorage.CURRENT_USER_ID;
	var num='';
	var no_beginning='';
	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/index/index');
	}
	$.ajax({ url:WEB_API_URL + "activity/inputlistactivity?" + Request, async: false,data:{token: uid}, success: function(json){
		var data=json.data;
		var str1='';
		if(data.data==''){
			str1='<li style="line-height: 24px;padding: 0 15px 15px 15px;border-bottom: 1px solid #ebebeb;font-size: 14px" onclick="location.href=\'/invitation/index/\'"><table width="100%"><tr><td style="background: url(//www.wisecareer.org/images/zhituo/icon/arrow-right.png) no-repeat right center;background-size: 9px;">暂无已报名的活动</td></tr></table></li>';
		}else{
			str1='<li style="line-height: 24px;padding: 0 0 5px 15px;border-bottom: 1px solid #ebebeb;font-size: 14px"><table width="100%"><tr><td width="40" rowspan="2" valign="middle"><img src="http://www.wisecareer.org/images/zhituo/icon/tixing.png" alt="" width="24"/></td><td>'+data.ksdate+'</td><td width="70" rowspan="2" valign="middle" align="center" style="color: #f58020;font-weight: bold">'+data.str+'</td></tr><tr><td style="line-height: 18px;padding: 3px 0">'+data.titlename+'</td></tr></table></li>';
			no_beginning=data.ksdate;
		}
		$('ul#box_content').prepend(str1);
		if(data.num==0){
			$('a#baogao').attr('href','javascript:num0()');
		}
	}
	})

function num0(){
	layer.open({
		content: '暂无现场实习报告，请体验完成后进入！'
		,btn: '我知道了'
		,shadeClose: false
	});
}

	$('#bomb_btn').click(function(){

		num = $('#bomb_text').val();

		if(num==""){
			layer.open({
				content: '入场码不能为空'
				,btn: '我知道了'
				,shadeClose: false
				,time: 5
			});
		}else if(num.length<6||num.length>6){
			layer.open({
				content: '入场码位数错误'
				,btn: '我知道了'
				,shadeClose: false
				,time: 5
			});
		}else{

			$.ajax({
				url: WEB_API_URL+"activity/input_activity",
				type: 'POST',
				dataType: 'json',
				data:{token: uid,code:num},
				async:false,
				success: function (result){
					if(result.status == 130016) {
						layer.open({
							content: result.msg
							,btn: '我知道了'
							,shadeClose: false
							,yes: function(index){
								window.location.href="/scene/internship";
							}
						});
					}else if(result.status == 130003) {
						layer.open({
							content:result.msg
							,btn: '我知道了'
							,shadeClose: false
							,yes: function(index){
								window.location.href="/invitation/index";
							}
						});
					}else if(result.status == 130015){
//						未开始
						layer.open({
							content:'实习活动尚未开始。请在 '+no_beginning+' 之后入场。'
							,btn: '我知道了'
							,shadeClose: false
						});
					}else if(result.status == 130013){
//						未报名
						layer.open({
							content:result.msg
							,btn: ['去报名', '稍后再说']
							,shadeClose: false
							,yes: function(index){
								window.location.href="/invitation/index";
							}
						});
					}else{
						layer.open({
							content:result.msg
							,shadeClose: false
							,btn: '我知道了'
						});
					}
				}
			});

		}

	});

//	$.ajax({ url:WEB_API_URL + "activity/internship?" , async: false,data:{token: uid}, success: function(json){
//
//		var status=json.status;
//		if(status==0){
//			window.location.href='/scene/internship'
//		}
//	}});

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
			link: 'http://zt.wisecareer.org/scene/input',
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
			link: 'http://zt.wisecareer.org/scene/input',
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