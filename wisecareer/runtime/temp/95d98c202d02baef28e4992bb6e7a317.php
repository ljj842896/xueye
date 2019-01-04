<?php /*a:1:{s:79:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\hangye\zy_search.html";i:1545031442;}*/ ?>
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
	<!--[if IE]>
	<script src="//www.wisecareer.org/js/html5shiv.min.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>

	<link rel="stylesheet" type="text/css" href="//www.wisecareer.org/css/zhituo/layui.css" />
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<style>
		.zy table td {
			font-size: 16px;
			line-height: 30px;
			color: #666;
		}
		.zy select#scid{
			padding: 0 0 0 10%;
			width: 100%;
			line-height: 36px;
			height: 36px;
			border: solid 1px #ebebeb;
			-webkit-appearance: none;
			font-size: 14px;
		}

		ul#hangye_list{
			width: 35%;
			position: fixed;
			left: 0;
			top: 0;
			z-index: 1000;
			height: 100%;
			overflow-y: scroll;
		}
		ul#hangye_list>li>a{
			display: block;
			text-align: center;
			border-bottom: solid 1px #ebebeb;
			border-right: solid 1px #ebebeb;
			line-height: 48px;
			font-size: 14px;
			color: #666;
			background: #ebebeb;
		}
		ul#hangye_list>li>a.this{
			font-size: 16px;
			font-weight: bold;
			color: #f58020;
			background: #fff;
		}

		ul#zhiye_list>li>a{
			display: block;
			text-align: center;
			line-height: 48px;
			font-size: 14px;
			color: #666;
		}
		ul#zhiye_list>li>a.this{
			font-size: 16px;
			font-weight: bold;
			color: #f58020;
		}
		ul#zhiye_list>li>a.this:before{
			content: '·  ';
			font-size: 30px;
			line-height: 50px;

		}
		#text2{
			display: block;
			line-height: 36px;
			border: solid 1px rgba(0,0,0,0.3);
			padding: 0 5%;
			color: #666;
			font-size: 12px;
			text-align: left;
		}
	</style>
</head>
<body>
<!-- head -->
	<div class="head" style="position: fixed;top: 0;left: 0;z-index: 300">
		<h1 style="margin: 0">找职业</h1>
		<a href="javascript:history.go(-1)" class="return" style="top: 0"><img src="//www.wisecareer.org/images/zhituo/icon/return-right.png" alt="" width="10"/></a>
	</div>

<!-- home -->

<div id="dBody">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner zy">
			<div class="cleardiv20"></div>
			<form method="GET" action="/hangye/zy_list/" name="myform" id="myform">
				<table width="100%">
					<tr>
						<td>选择你感兴趣的行业或职能岗位</td>
					</tr>
					<tr>
						<td><a href="javascript:a2()" class="click_input" id="text2" tpid="0" zid="0">点击选择</a><input type="hidden" name="tpid" id="job" value="0"/></td>
					</tr>
					<tr>
						<td style="line-height: 10px">&nbsp;</td>
					</tr>
					<tr>
						<td>职业的层级</td>
					</tr>
					<tr>
						<td>
							<ul class="danxuan clear" id="taglx">
								<li><a href="javascript:void(0);"  tag="1" style="display: block;padding:0;text-align: center" >初级岗位</a><img src="//www.wisecareer.org/images/zhituo/icon/yes.png" alt=""/></li>
								<li><a href="javascript:void(0);" tag="2" style="display: block;padding:0;text-align: center"  >中级岗位</a><img src="//www.wisecareer.org/images/zhituo/icon/yes.png" alt=""/></li>
								<li><a href="javascript:void(0);" tag="3" style="display: block;padding:0;text-align: center"  >高级岗位</a><img src="//www.wisecareer.org/images/zhituo/icon/yes.png" alt=""/></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td style="line-height: 30px">&nbsp;</td>
					</tr>
					<tr>
						<td><input  type="submit" value="搜索" class="tijiao"/></td>
					</tr>
				</table>
				<div class="cleardiv20"></div>
				<div class="cleardiv20"></div>
			</form>
			<div class="cleardiv10"></div>
		</div>
	</div>
</div>


<div class="show_hide" style="display: none">
	<div class="class_bg"></div>
	<div class="class_content" style="padding: 0;overflow-y: scroll">
		<div id="box1_tittle">
			<div class="head">
				<h1 style="font-family:Arial,'微软雅黑', Helvetica, sans-serif;">选择职业</h1>
				<a href="javascript:hide()" class="box1_return"><i class="return-right"></i></a>
				<a href="javascript:void(0)" name="submit" style="" id="screen_btn">
					<input type="button" value="提交" style="line-height: 40px;" class="btn1 father" id="btn1"/>
				</a>
			</div>
		</div>

		<div width="100%" id="box1" class="clear">
			<ul id="hangye_list">
				<li><div class="cleardiv50" style="background: #fff"></div></li>
				<li><a href="javascript:void(0);" tpid="0" class="this list_fl" id="first">行业</a></li>
				<li><a href="javascript:void(0);" tpid="1" class="list_fl" id="last">职能</a></li>
			</ul>
			<table width="100%" style="padding-top: 55px;background: #fff">
				<tr>
					<td width="35%" align="center" style="height: 100%">&nbsp;</td>
					<td width="65%" align="center" style="height: 100%">
						<ul id="zhiye_list">

						</ul>
					</td>
				</tr>
			</table>
		</div>
		<div class="clear"></div>
		<div style="width: 100%;text-align: center;display: block"></div>
		<div class="cleardiv5 "></div>
	</div>
</div>

<script>
	$('#taglx>li>a').live('click',function(){
		var attr1=$(this).next().attr('num');
		$(this).next().val(attr1);
		var tag = $(this).attr('tag');
		$("#taglx>li>input[type='hidden']").remove();
		$(this).after('<input name="jibie" type="hidden"  value='+tag+' />' );
		$('#taglx>li>a').removeClass("this");
		$(this).addClass("this");
		$('#taglx>li>img').hide();
		$(this).next().next().show();
	});
    var token = localStorage.CURRENT_USER_ID;
	var tpid='0';
	var id='0';
	var stop=false;
    $.getJSON(WEB_API_URL + "classnamel/industry2",function (json) {

			var str1 = '';
			$.each(json.data.list1,function (index,array) {
				str1+='<li><a href="javascript:void(0);" id="'+array['tpid']+'">'+array['tpname']+'</a></li>';
            });

			$('#zhiye_list').append(str1);

    })

	$('.tijiao').live('click',function(){
		if($('#job').val()==0){
			layer.open({
				content: '请选择你感兴趣的行业或职能岗位'
				,btn: '我知道了'
				,shadeClose: false
			});
			return false;
		}
		document.getElementById("myform").submit();
	})
	function hide(){
		$('.show_hide').hide();
		$('body').css('overflow-y','scroll')
	}
	function a2(){
		$('.father').show();
		$('.show_hide').show();
		$('body').css('overflow-y','hidden')
	}

	$('#zhiye_list>li>a').live('click',function(){
		id=$(this).attr('id');
		$('#job').val(id);

		val=$(this).html();
		$('#zhiye_list>li>a').removeClass('this');
		$(this).addClass('this');
		stop=true;
	});
	$('#btn1').live('click',function fa_sub(){
		if(stop==true){
			$('#text2').attr('tpid',tpid);
			$('#text2').attr('zid',id);
			$('#text2').html(val);
			$('#zhiye_list>li>a').removeClass('this');
			stop=false;
			$('.show_hide').hide();
			$('body').css('overflow-y','scroll')
		}else{
			layer.open({
				content: '请选择职业！'
				,btn: '我知道了'
			});
		}
	})
	var type=0;
	$('.list_fl').live('click',function(){
		type=$(this).attr('tpid');
		if(type==0){
			$('#job').attr('name','tpid');
			$('.list_fl').removeClass('this');
			$(this).addClass('this');
			$.getJSON(WEB_API_URL + "classnamel/industry2",function (json) {

				var str1 = '';
				$.each(json.data.list1,function (index,array) {
					str1+='<li><a href="javascript:void(0);" id="'+array['tpid']+'">'+array['tpname']+'</a></li>';
				});

				$('#zhiye_list').html(str1);

			})
		}else{
			$('#job').attr('name','id');
			$('.list_fl').removeClass('this');
			$(this).addClass('this');
			$.getJSON(WEB_API_URL + "classnamel/industry2",function (json) {

				var str1 = '';
				$.each(json.data.list2,function (index,array) {
					str1+='<li><a href="javascript:void(0);" id="'+array['id']+'">'+array['tpname']+'</a></li>';
				});

				$('#zhiye_list').html(str1);

			})
		}
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
			link: 'http://zt.wisecareer.org/hangye/zy_search',
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
			link: 'http://zt.wisecareer.org/hangye/zy_search',
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

