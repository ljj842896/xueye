<?php /*a:1:{s:74:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\index\index.html";i:1545973808;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link rel="icon" href="//www.wisecareer.org/images/favicon.ico"  type="image/x-icon">
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/css/xueye/yuan.css"/> 
	<script src="//www.wisecareer.org/js/jquery-1.8.3.min.js"></script>
	<script src="//www.wisecareer.org/js/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="//www.wisecareer.org/js/layer/mobile/need/layer.css"/>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

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

		@media all and (min-width: 375px) {
			#dBody{
				bottom: 130px;
			}
			#dFoot{
				height: 125px;
			}
			#dFoot>#jiaxiang{
				position: fixed;
				bottom: 105px;
				left: 50%;
				margin-left: -30px;
				z-index: 00;
			}
			#dFoot>table{
				padding: 20px 0
			}
			#dFoot>table#table_1 td{
				line-height: 80px;
			}
			#dFoot>table#table_2 td{
				line-height: 35px;
			}
		}
		@media (min-width:320px) and (max-width:375px) {
			#dBody{
				bottom: 120px;
			}
			#dFoot{
				height: 115px;
			}
			#dFoot>#jiaxiang{
				position: fixed;
				bottom: 95px;
				left: 50%;
				margin-left: -30px;
				z-index: 900;
			}
			#dFoot>table{
				padding: 20px 0
			}
			#dFoot>table#table_1 td{
				line-height: 70px;
			}
			#dFoot>table#table_2 td{
				line-height: 30px;
			}
		}
		@media all and (max-width: 320px) {
			#dBody{
				bottom: 110px;
			}
			#dFoot{
				height: 105px;
			}
			#dFoot>#jiaxiang{
				position: fixed;
				bottom: 85px;
				left: 50%;
				margin-left: -30px;
				z-index: 00;
			}
			#dFoot>table{
				padding: 20px 0
			}
			#dFoot>table#table_1 td{
				line-height: 60px;
			}
			#dFoot>table#table_2 td{
				line-height: 25px;
			}
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
		#ez{
			width: 25px;
			display: inline-block;
			position: relative;
		}
		#ez>img{
			position: absolute;
			top: 10px;
			left: 3px;
		}
	</style>
</head>
<body>
<!-- head -->
<div id="dHead">
	<div class="head">
		<h1 style="margin: 0">
			职拓
			<span id="ez">&nbsp;
				<img src="//www.wisecareer.org/images/zhituo/E.png" alt="" height="24"/>
			</span>
			站
		</h1>
	</div>
</div>


<div class="show_hide" id="fx" style="position:absolute;top:0;z-index: 2000;height: 100%;overflow: hidden;display: none">

	<table cellpadding="0" cellspacing="0" style="width: 100%;height: 100%;background: rgba(0,0,0,0.8);">
		<tr>
			<td align="right" valign="bottom"><a href="javascript:close()" style="font-weight: bold;color: #fff;line-height: 40px;width: 80px;display: block;text-align: center;font-size: 16px">&nbsp;&nbsp;关闭&nbsp;&nbsp;</a></td>
		</tr>
		<tr>
			<td valign="top" align="center"><img src="/static/zhituo/images/fx_content.jpg" alt="" width="100%"  class="fx_enter"/></td>
		</tr>
	</table>

</div>


<div id="dBody2" style="top:55px;bottom: 110px">
	<div class="mycontent" style="height:100%;background: #fff;margin: 0">
		<div class="cleardiv20"></div>
		<div class="cleardiv5"></div>
		<div class = "wrapper" >
			<ul class = "menu" >
				<li>
					<div class="prize pd" id="jiaolian" onclick="location.href='/jiaolian/index/'" >
						<!--/jiaolian/index/-->
						<h3><img src="//www.wisecareer.org/images/zhituo/icon/home3.png" alt="" class="img"/></h3>
						<p class="p_title">我的教练</p>
						<br/>
					</div>
				</li>
				<li>
					<div class="prize pd" onclick="location.href='/experience/index/'">
						<h3><img src="//www.wisecareer.org/images/zhituo/icon/home1.png" alt="" class="img"/></h3>
						<p class="p_title">案例体验</p>
						<!--<img src="http://zxedu.wisecareer.org/images/tanhao.png" alt="" width="25" class="yujing"/>-->
						<br/>
					</div>
				</li>
				<li>
					<div class="prize pd" onclick="location.href='/scene/input/'">
						<h3><img src="//www.wisecareer.org/images/zhituo/icon/home4.png" alt="" class="img"/></h3>
						<p class="p_title">现场实习</p>
						<br/>
					</div>
				</li>
				<li>
					<div class="prize"  onclick="location.href='/presentation/index/'">
						<!--/presentation/index/-->
						<h3><img src="//www.wisecareer.org/images/zhituo/icon/home2.png" alt="" class="img"/></h3>
						<p class="p_title">我的报告</p>
						<!--<img src="http://zxedu.wisecareer.org/images/tanhao.png" alt="" width="25" class="yujing"/>-->
						<br/>
					</div>
				</li>
				<img src="//www.wisecareer.org/images/zhituo/yuan2.png" alt="" width="100%"/>
				<img src="//www.wisecareer.org/images/zhituo/yuan_orange.png" alt="" width="40%" id="yuan_blue"/>
				<img src="//www.wisecareer.org/images/zhituo/ztez.png" alt="" style="width:35vw;height: 35vw" class="li_num" id="li_num2" onclick="location.href='/presentation/index/'"/>
				<img src="//www.wisecareer.org/images/zhituo/ztez.png" alt="" style="width:35vw;height: 35vw" class="li_num" id="li_num1"/>
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
		</div>
		</div>
</div>
 
<div id="dFoot" style="background: none;border-top: 5px solid #ebebeb">
	<img src="//www.wisecareer.org/images/zhituo/jian_xiang.png" alt="" id="jiaxiang"/>
	<table style="background: #fff;color: #666;display: none" width="100%" id="table_1">
		<tr>
			<td width="33%" align="center" style="border-right: 1px solid #ebebeb;font-size: 16px" class="pd" onclick="location.href='/hangye/zy_search/'">找职业</td>
			<!--onclick="location.href='/jiaolian/star/'"-->
			<td width="34%" align="center" style="font-size: 16px" class="pd" onclick="location.href='/hangye/index/'">行业动态</td>
			<!--/friend/index/-->
			<td width="33%" align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" class="pd" onclick="location.href='/invitation/index/'">实习申请</td>
		</tr>
	</table>
	<table style="background: #fff;color: #666" width="100%"  id="table_2">
		<tr>
			<td width="33%" align="center" style="border-right: 1px solid #ebebeb;font-size: 16px" class="pd"  onclick="location.href='/hangye/zy_search/'">找职业</td>
			<!--onclick="location.href='/jiaolian/star/'"-->
			<td width="34%" align="center" style="font-size: 16px" class="pd" onclick="location.href='/hangye/index/'">行业动态</td>
			<!--/friend/index/-->
			<td width="33%" align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" class="pd" onclick="location.href='/invitation/index/'">实习申请</td>
		</tr>
		<tr>
			<td style="line-height: 4px">&nbsp;</td>
			<td style="line-height: 4px">&nbsp;</td>
			<td style="line-height: 4px">&nbsp;</td>
		</tr>
		<tr>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
			<td style="line-height: 1px;background: #ebebeb">&nbsp;</td>
		</tr>
		<tr>
			<td style="line-height: 4px">&nbsp;</td>
			<td style="line-height: 4px">&nbsp;</td>
			<td style="line-height: 4px">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" style="border-right: 1px solid #ebebeb;font-size: 16px" onclick="location.href='/message/index/'">消息</td>
			<td align="center" style="font-size: 16px" class="pd" id="jiaolian_search" onclick="location.href='/jiaolian/jiaolian_search/'">找教练</td>
			<td align="center" style="border-left: 1px solid #ebebeb;font-size: 16px;position: relative" onclick="location.href='/user/index/'">个人中心</td>
		</tr>
	</table>
</div>

<script>  
<?php if(app('request')->get('tokenid') !=""): ?>

 var  tokenid = "<?php echo htmlentities(app('request')->get('tokenid')); ?>"; 
		
 $.getJSON( WEB_API_URL +  "user/ztuserstatus?", {token:tokenid}, function (json) {
    var data=json.data;
 
 
	 if(data.status == 0) {
            localStorage.CURRENT_USER_ID = "";
            localStorage.CURRENT_USER_TypeLX = "";
            localStorage.CURRENT_USER_VIP = "";
            localStorage.CURRENT_USER_ID = tokenid;
            localStorage.CURRENT_USER_TypeLX = 2;
            localStorage.CURRENT_userinfo = data.userinfo;
		 }else{
			  window.location.href='/login/index';
		  } 
}); 
<?php else: ?>
$(function(){
		isHaveLogin('/index/index');
	});
		 
<?php endif; ?>

	

	function Not_open(){
		layer.open({
			content: '暂未开通'
			,btn: '我知道了'
			,shadeClose: false
		});
	}

	function no_user(){
		layer.open({
			content: '很抱歉，你暂未购买体验套餐, 不能使用此功能。'
			,btn: ['去购买', '下次再说']
			,shadeClose: false
			,yes: function(index){
				window.location.href='/experience/set1';
				layer.close(index);
			}
		});
	}
	function eval123(){
		layer.open({
			content: '抱歉。你需要先购买“教练评价”功能后才能进入。'
			,btn: ['去购买', '下次再说']
			,shadeClose: false
			,yes: function(index){
				window.location.href='/experience/set1';
				layer.close(index);
			}
		});
	}
	var uid = localStorage.CURRENT_USER_ID;
	Request = GetRequest();
	$.getJSON(WEB_API_URL + "user/ztgetuser?", {'token': uid}, function (json) {
		var link="location.href='http://fx.wisecareer.org/index/become?type=2&token="+uid+"'";
		$('img.fx_enter').attr("onclick",link);
		if(json.data.pic==''){
			$('#li_num2').attr('src','//www.wisecareer.org/images/zhituo/ztez.png')
		}else{
			$('#li_num2').attr('src','http://www.wisecareer.org/od/'+json.data.pic)
		}



		if(json.data.isretail==0){
			$('div.show_hide').show();
		}else{
			$('div.show_hide').hide();
		}

		if(json.data.userretailid==1){
			$('div#fx').hide()
		}else{
			$('div#fx').show()
		}

		if(json.data.viptype==0){
			if(json.data.hasmeal_time==0){
				$('.pd').attr("onclick","no_user();");
				if(json.data.has_eval_num<1||json.data.has_eval==0){
					$('#jiaolian,#jiaolian_search').attr("onclick","eval123();");
				}
			}
		}else{
			if(json.data.has_eval_num<1||json.data.has_eval==0){
				$('#jiaolian,#jiaolian_search').attr("onclick","eval123();");
			}
		}
		if(json.data.userinfo==0){
			layer.open({
				content: '欢迎进入职拓e站！加入职拓大本营，享受未来人生。'
				,btn: ['确定', '取消']
				,shadeClose: false
				,yes: function(index){
					window.location.href='/user/userinfo'
				}
			});
		}
	})

	$('#jiaxiang').toggle(function(){
		$(this).attr('src','//www.wisecareer.org/images/zhituo/jia_xiang.png');
		$('#table_2').fadeOut(500,function(){$('#table_1').fadeIn(500)});

	},function(){
		$(this).attr('src','//www.wisecareer.org/images/zhituo/jian_xiang.png');
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


	function close(){
		$('div.show_hide').hide();
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