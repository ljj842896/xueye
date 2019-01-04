<?php /*a:1:{s:79:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\experience\index.html";i:1545031440;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/experience.css"/>
	<!--<link rel="stylesheet" href="//www.wisecareer.org/css/zhituo/user_1.css"/>-->
	<link rel="stylesheet" type="text/css" href="//www.wisecareer.org/css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="//www.wisecareer.org/css/default.css">
	<!--[if IE]>
	<script src="//www.wisecareer.org/js/html5shiv.min.js"></script>
	<![endif]-->
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>

	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>
	<script type="text/javascript" src="//www.wisecareer.org/movingboxes/jquery.movingboxes.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<link type="text/css" href="//www.wisecareer.org/movingboxes/lrtk.css" rel="stylesheet">
	<script type="text/javascript">
		$(function(){
			$('#lrtk').movingBoxes({
				startPanel   : 3,       // 从第一个li开始
				reducedSize  : .5,      // 缩小到原图50%的尺寸
				wrap         : true,   // 无缝循环
				buildNav     : false,	// 显示指示器效果
				navFormatter : function(){ return "&#9679;"; } // 指示器格式，为空即会显示123
			});
		});
	</script>
	<style>
		.inner ul#xiang{
			width:90%;
			display:block;
			margin:-10px auto;
			height: 85px;
		}
		.inner ul#xiang>li{
			float:left;
			width:31%;
			padding: 0 1%;
			text-align:center;
			font-size:14px;
			background: url(http://xueye.wisecareer.org/images/xueye/icon/line_03.png) no-repeat right center;
		}
		.inner ul#xiang .c{
			background:none;
		}
		.inner ul#xiang li span.b{
			color:#3f70eb;
		}
		.inner ul#xiang li span{
			display:block;
			color:#f58020;
			font-size:25px;
		}
	</style>
</head>
<body>
<!-- head -->
<div id="dHead">
	<div class="head">
		<h1 style="margin: 0">案例体验</h1>
		<a href="/index/index" class="return"><i class="return-right"></i></a>
	</div>
</div>

<div id="dBody">
	<div class="mycontent experience" style="height:100%;margin: 0">

		<div class="cleardiv50"></div>
		<div class="inner">

		</div>
	</div>
</div>



<script>
	$(function(){
		isHaveLogin('/experience/index');
	});
	var num='';
	function goto(name,type,tid){
		layer.open({
			content: '你要体验'+name+'行业吗？'
			,btn: ['确定', '取消']
			,shadeClose: false
			,yes: function(index){
				window.location.href='/experience/loading/?type='+type+'&tid='+tid+'&lan=1';
				layer.close(index);
			}
		});
	}

	function goto2(name,type,tid){
		layer.open({
			content: '你要体验'+name+'行业（英文）吗？'
			,btn: ['确定', '取消']
			,shadeClose: false
			,yes: function(index){
				window.location.href='/experience/loading/?type='+type+'&tid='+tid+'&lan=2';
				layer.close(index);
			}
		});
	}

	function goto3(name,type,tid){
		layer.open({
			content: '你要体验'+name+'行业吗？'
			,btn: ['确定', '取消']
			,shadeClose: false
			,yes: function(index1){
				layer.close(index1);
				layer.open({
					content: '请选择职业案例的语言版本'
					,btn: ['中文案例', '英文案例']
					,shadeClose: false
					,yes: function(index){
						window.location.href='/experience/loading/?type='+type+'&tid='+tid+'&lan=1';
						layer.close(index);
					}
					,no: function(index){
						window.location.href='/experience/loading/?type='+type+'&tid='+tid+'&lan=2';
						layer.close(index);
					}
				});
			}
		});
	}


	function expire(){
		layer.open({
			content: '亲，你的学习计划已到期，请尽快添加新的计划吧！'
			,btn: ['确定', '取消']
			,shadeClose: false
			,yes: function(index){
				window.location.href='/experience/write_plan/';
				layer.close(index);
			}
		});
	}

	Request = GetRequest();
	var eid = localStorage.CURRENT_USER_ID;

	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/index/index');
	}

	$.ajax({ url:WEB_API_URL + "experience/index?" , async: false,data:{token: eid}, success: function(json){
		var data=json.data.tpid;
		var endtime='';
		var str1='<div class="cleardiv10"></div>';

		if(json.status == 260009){
            layer.open({
                content: json.msg
                ,btn: ['确定', '取消']
                , shadeClose: false
                ,yes: function(index){
                    layer.close(index);
                    window.location.href='/experience/set1/';
                }
            });
		}else if(json.status!=0){
			if(json.data.endtime==''){endtime='<span style="font-size: 14px">暂未开通</span>'}else if(json.data.endtime==undefined){endtime='<span style="font-size: 14px"> 无学习计划</span>'}else{endtime='<span style="font-size: 14px">到期时间: <span style="color:#f58020">'+json.data.endtime+'</span></span>'}
			layer.open({
				content: json.msg
				,btn: ['确定', '取消']
				, shadeClose: false
				,yes: function(index){
					layer.close(index);
					window.location.href='/experience/write_plan/';
				}
			});

			str1 +='<div class="cleardiv20"></div><ul id="xiang">';
			str1 +='<li class="a" onclick="location.href=\'/experience/evaluate\'"><span>'+json.data.xinnum+'</span>最新评价</li>';
			str1 +='<li class="b" style="width:35%;"><span>'+json.data.pro+'%</span>进度</li>';
			str1 +='<li class="c"><span></span>我的经验值</li>';
			str1 +='</ul><div class="cleardiv20"></div><h2 onclick="location.href=\'/experience/plan/\'">当前学习计划&nbsp;&nbsp;&nbsp;'+endtime+'</h2><div class="cleardiv5"></div>';
			str1 +='<h2 onclick="location.href=\'/experience/plan_log/\'">以往学习计划&nbsp;&nbsp;&nbsp;</h2><div class="cleardiv5"></div>';
			str1 +='<h2 onclick="location.href=\'/experience/evaluate\'">教练评价&nbsp;&nbsp;<span style="font-size: 14px">'+json.data.nums+'  条&nbsp;&nbsp;|&nbsp;&nbsp; <img src="//www.wisecareer.org/images/zhituo/icon/ic_thanked.png" alt="" height="20" style="position: relative;top: 4px"/> &nbsp;'+json.data.atten+' 人 &nbsp;&nbsp;|&nbsp;&nbsp; <img src="//www.wisecareer.org/images/zhituo/icon/fabulous_yes.png" alt="" height="20" style="position: relative;top: 4px"/>&nbsp;'+json.data.recom_eval+'  人</span></h2>';
			str1 +='<div class="cleardiv5"></div><div class="cleardiv20"></div><div class="cleardiv20"></div>';
			$('div.inner').html(str1);


		}else if(json.data.expire==1){
			if(json.data.endtime==''){endtime='<span style="font-size: 14px">暂未开通</span>'}else{endtime='<span style="font-size: 14px">到期时间: <span style="color:#f58020">'+json.data.endtime+'</span></span>'}

			layer.open({
				content: "亲，你的学习计划已到期，请尽快添加新的计划吧！"
				,btn: ['确定', '取消']
				, shadeClose: false
				,yes: function(index){
					layer.close(index);
					window.location.href='/experience/write_plan/';
				}
			});


			str1 +='<div class="cleardiv10"></div>';

			if(json.data.pro==100){}else{
				if(json.data['label']!=''){
					str1 +='<div class="report-experience"><ul id="lrtk">';
					$.each(json.data.label, function (index, array) {
						if(array['language']==2){
							str1 += "<li id="+array['tid']+" class="+array['type']+" onclick=\"goto2('"+array['tpname']+"','"+array['type']+"',"+array['tid']+")\"><a href=\"javascript:void(0);\"><img src="+array['pic']+" alt=\"'+array['tpname']+'\"></a></li>";
						}else if(array['language']==3){
							str1 += "<li id="+array['tid']+" class="+array['type']+" onclick=\"goto3('"+array['tpname']+"','"+array['type']+"',"+array['tid']+")\"><a href=\"javascript:void(0);\"><img src="+array['pic']+" alt=\"'+array['tpname']+'\"></a></li>";
						}else{
							str1 += "<li id="+array['tid']+" class="+array['type']+" onclick=\"goto('"+array['tpname']+"','"+array['type']+"',"+array['tid']+")\"><a href=\"javascript:void(0);\"><img src="+array['pic']+" alt=\"'+array['tpname']+'\"></a></li>";
						}
						});
					str1 +='</ul></div>';
				}else{
					str1 +='<div class="cleardiv10"></div>';
				}
			}


			str1 +='<div class="cleardiv20"></div><ul id="xiang">';
			str1 +='<li class="a" onclick="location.href=\'/experience/evaluate\'"><span>'+json.data.xinnum+'</span>最新评价</li>';
			str1 +='<li class="b" style="width:35%;"><span>'+json.data.pro+'%</span>进度</li>';
			str1 +='<li class="c"><span>135</span>我的经验值</li>';
			str1 +='</ul><div class="cleardiv20"></div><h2 onclick="location.href=\'/experience/plan/\'">当前学习计划&nbsp;&nbsp;&nbsp;'+endtime+'</h2><div class="cleardiv5"></div>';
			str1 +='<h2 onclick="location.href=\'/experience/plan_log/\'">以往学习计划&nbsp;&nbsp;&nbsp;</h2><div class="cleardiv5"></div>';
			str1 +='<h2 onclick="location.href=\'/experience/evaluate\'">教练评价&nbsp;&nbsp;<span style="font-size: 14px">'+json.data.nums+'  条&nbsp;&nbsp;|&nbsp;&nbsp; <img src="//www.wisecareer.org/images/zhituo/icon/ic_thanked.png" alt="" height="20" style="position: relative;top: 4px"/> &nbsp;'+json.data.atten+' 人 &nbsp;&nbsp;|&nbsp;&nbsp; <img src="//www.wisecareer.org/images/zhituo/icon/fabulous_yes.png" alt="" height="20" style="position: relative;top: 4px"/>&nbsp;'+json.data.recom_eval+'  人</span></h2>';

			str1 +='<div class="cleardiv5"></div><div class="cleardiv20"></div><div class="cleardiv20"></div>';
			$('div.inner').html(str1);
		}else{
			if(json.data.endtime==''){endtime='<span style="font-size: 14px">暂未开通</span>'}else{endtime='<span style="font-size: 14px">到期时间: <span style="color:#f58020">'+json.data.endtime+'</span></span>'}

			if(json.data==0){
				window.location.href='/experience/write_plan/';
			}else if(json.data.label==0){
				window.location.href='/experience/write_plan/';
			}else{
				str1 +='<div class="cleardiv10"></div>';
				if(json.data.pro==100){}else{
					if(json.data['label']!=''){
						str1 +='<div class="report-experience"><ul id="lrtk">';
						$.each(json.data.label, function (index, array) {
							if(array['language']==2){
								str1 += "<li id="+array['tid']+" class="+array['type']+" onclick=\"goto2('"+array['tpname']+"','"+array['type']+"',"+array['tid']+")\"><a href=\"javascript:void(0);\"><img src="+array['pic']+" alt=\"'+array['tpname']+'\"></a></li>";
							}else if(array['language']==3){
								str1 += "<li id="+array['tid']+" class="+array['type']+" onclick=\"goto3('"+array['tpname']+"','"+array['type']+"',"+array['tid']+")\"><a href=\"javascript:void(0);\"><img src="+array['pic']+" alt=\"'+array['tpname']+'\"></a></li>";
							}else{
								str1 += "<li id="+array['tid']+" class="+array['type']+" onclick=\"goto('"+array['tpname']+"','"+array['type']+"',"+array['tid']+")\"><a href=\"javascript:void(0);\"><img src="+array['pic']+" alt=\"'+array['tpname']+'\"></a></li>";
							}
						});
						str1 +='</ul></div>';
					}else{
						str1 +='<div class="cleardiv10"></div>';
					}
				}
				str1 +='<div class="cleardiv20"></div><ul id="xiang">';
				str1 +='<li class="a" onclick="location.href=\'/experience/evaluate\'"><span>'+json.data.xinnum+'</span>最新评价</li>';
				str1 +='<li class="b" style="width:35%;"><span>'+json.data.pro+'%</span>进度</li>';
				str1 +='<li class="c"><span>135</span>我的经验值</li>';
				str1 +='</ul><div class="cleardiv20"></div><h2 onclick="location.href=\'/experience/plan/\'">当前学习计划&nbsp;&nbsp;&nbsp;'+endtime+'</h2><div class="cleardiv5"></div>';
				str1 +='<h2 onclick="location.href=\'/experience/plan_log/\'">以往学习计划&nbsp;&nbsp;&nbsp;</h2><div class="cleardiv5"></div>';
				str1 +='<h2 onclick="location.href=\'/experience/evaluate\'">教练评价&nbsp;&nbsp;<span style="font-size: 14px">'+json.data.nums+'  条&nbsp;&nbsp;|&nbsp;&nbsp; <img src="//www.wisecareer.org/images/zhituo/icon/ic_thanked.png" alt="" height="20" style="position: relative;top: 4px"/> &nbsp;'+json.data.atten+' 人 &nbsp;&nbsp;|&nbsp;&nbsp; <img src="//www.wisecareer.org/images/zhituo/icon/fabulous_yes.png" alt="" height="20" style="position: relative;top: 4px"/>&nbsp;'+json.data.recom_eval+'  人</span></h2>';

				str1 +='<div class="cleardiv5"></div><div class="cleardiv20"></div><div class="cleardiv20"></div>';
				$('div.inner').html(str1);
			}
		}
	}
	});

</script>

<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1273552666'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/z_stat.php%3Fid%3D1273552666'type='text/javascript'%3E%3C/script%3E"));</script></body>
</html>



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
			title: '跨行业职业体验及现场实习平台',
			desc: '近30个行业、岗位、专题的职业（案例）体验、现场实习、多元随机评价、精准定位“职业教练”...... 积累经验值，换得未来好工作。',
			link: 'http://zt.wisecareer.org/experience/index',
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
			link: 'http://zt.wisecareer.org/experience/index',
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

	function hide(){
		$('.show_hide').hide();
	}

	function show(){
		$('.show_hide').show();
	}
</script>