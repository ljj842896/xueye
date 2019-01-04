<?php /*a:1:{s:75:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\hangye\index.html";i:1545031442;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<link href="//www.wisecareer.org/css/zhituo/user_info.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="//www.wisecareer.org/left_and_right_slide/css/idangerous.swiper.css">
	<script src="/static/public/js/jquery-1.8.3.min.js"></script>
	<script src="/static/public/js/jquery.mousewheel.js"></script>
	<script src="/static/lib/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="/static/lib/layer/mobile/need/layer.css"/>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<style>
		ul#xiang>li{
			padding: 0 3%;
			width: 44%;
			float: left;
			text-align: center;
			margin-top: 10px;
		}
		ul.mokuai,table.mokuai{
			width: 100%;
			height: 100px;
			vertical-align: middle;
			background: #fff;border-radius: 10px;position: relative;
			display: inline-block;
		}
		div.mokuai{
			border: none;
			background: none;
		}
		ul.mokuai>li{
			line-height: 20px;
			height: 20px;
		}
		div.renshu{
			width: 30px;height: 30px;line-height:30px;border-radius: 50%;position:absolute;top: -15px;right: -15px;background: #00bb0c;color: #fff;
		}
		.tuijian{
			width: 100%;
		}
		.tuijian td h3{  font-size: 16px;line-height: 20px;color: #fff;padding: 3px 0}
		.tuijian td div.content{  font-size: 14px;height: 48px;overflow: hidden;color: #666;padding-top: 5px}
		.tuijian td{  font-size: 14px;line-height: 16px}
	</style>
</head>
<body>
<!-- head -->


<div class="head" style="position: fixed;top: 0;left: 0;z-index: 3000">
	<h1 style="margin: 0">行业动态</h1>
	<a href="/index/index" class="return"><i class="return-right"></i></a>
</div>
<!--<a href="/dynamic/chart/" id="screen_btn" style="z-index: 3500"><div class="cleardiv10"></div><img src="//www.wisecareer.org/images/chart.png" alt="" width="30"/></a>-->



<!-- home -->
<div id="dBody">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="message clear">
			<div class="swiper-pages swiper-container" style="height: 200px;overflow: hidden">
				<div class="swiper-wrapper">

					<div class="swiper-slide" style="height: 200px;overflow: hidden">
						<div class="page-inner" style="height: 200px;overflow: hidden">
							<div id="banner" class="swiper-container" style="height: 200px;overflow: hidden">
								<div class="swiper-wrapper" id = "bannerid"  style="height: 200px;overflow: hidden">

								</div>
								<div class="pagination"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="cleardiv10" style="background: #ebebeb"></div>
			<ul id="xiang" class="clear">

				<!--<li>-->
					<!--<table width="100%" class="mokuai" onclick="location.href='/dynamic/subject_list'">-->
						<!--<tr>-->
							<!--<td align="center" valign="middle" height="100" width="50%"><img src="//www.wisecareer.org/images/xueye/zengjia.png" alt="" width="90%"/></td>-->
							<!--<td align="left" valign="middle" style="font-size: 16px;">添加动态</td>-->
						<!--</tr>-->
					<!--</table>-->
					<!--&lt;!&ndash;<div class="mokuai" onclick="location.href='/dynamic/subject_list'"><img src="//www.wisecareer.org/images/xueye/tianjia.png" alt="" width="100%" height="100%"/></div>&ndash;&gt;-->
				<!--</li>-->
			</ul>
			<!--<p style="padding: 0 5px;font-size: 14px;color: #666">注：每位学员可免费订阅1个学科动态。目前最多订阅10个动态。</p>-->
			<div class="cleardiv20"></div>
		</div>
	</div>
</div>




<script>
	$(function(){
		isHaveLogin('/hangye/index');
	});
</script>

<script>

	var load_index =layer.open({type: 2, shadeClose: false});
	var uid = localStorage.CURRENT_USER_ID;

	Request = GetRequest();


	var did = getUrlParam("did");
	var titlename = decodeURI(GetQueryString("titlename"));

	var back = getUrlParam("back");
	if(back==1){
		$('a.return').attr('href','/index/index');
	}

	$.getJSON(WEB_API_URL + "dynamic/hangyelist?"+Request, {token: uid}, function (json) {
		var data =json.data;
		var str1 = "";
		if(data !=''){

			$.each(data.sub, function (index, array) {
			    if(array['tpid']){
                    str1 +='<li><table width="100%" class="mokuai" onclick="location.href=\'/hangye/dynamic_list?hangyeid='+array['tpid']+'\'"><tr>';
				}else{
                    str1 +='<li><table width="100%" class="mokuai" onclick="location.href=\'/hangye/dynamic_list?lxid='+array['id']+'\'"><tr>';
				}

				str1 +='<td align="center" rowspan="4" valign="middle" height="100" width="50%"><img src="'+array['pic']+'" alt="" width="80%"/></td><td>&nbsp;</td></tr>';
				str1 +='<tr><td align="left" valign="middle" style="font-size: 16px;">'+array['tpname']+'动态</td></tr>';
				str1 +='<tr><td align="left" valign="middle" style="font-weight: bold">最新动态 <img src="http://www.wisecareer.org/images/zhituo/icon/arrow-right.png" alt="" height="12" style="position: relative;top: 2px;left: 10px"/></td></tr><tr><td>&nbsp;</td></tr></table></li>';

//			str1 +='<li><ul class="mokuai">';
//			str1 +='<li></li><li style="font-size: 16px;height: 30px;line-height: 30px" onclick="location.href=\'/dynamic/dynamic_list?classid='+array['tpid']+'\'">'+array['tpname']+'动态</li><li style="font-size: 14px;height: 30px;line-height: 30px" onclick="location.href=\'/dynamic/dynamic_list?classid='+array['tpid']+'\'">订阅<span> <span style="color: #f00;font-weight: bold">'+array['num']+'</span> </span>人</li>';
//			str1 +='<li></li></ul></li>';
			});
			$('ul#xiang').prepend(str1);
			var str2 = '';
			var lianjie='';
			var id='';
			$.each(data.rec, function (index, array) {
				str2 += '<div class="swiper-slide"><table class="tuijian" width="100%" style="height: 200px;background-size:100% 100%;background-image:url('+array['pic']+')" onclick="location.href=\'/hangye/dynamic_content?id='+array['id']+'\'">';
				str2 += '<tr><td colspan="2" style="height: 36px;background: rgba(0,0,0,0.4)"><h3>'+array['titlename']+'</h3></td></tr>';
				str2 += '<tr><td colspan="2">&nbsp;</td></tr>';
				str2 += '</table></div>';
			});
			$('#bannerid').html(str2);

		}


//		if(json.status==60005){
//			layer.open({
//				content: '查看学科动态需要成为会员呦！'
//				,btn: ['确定', '取消']
//				,yes: function(index){
//					window.location.href='/user/apply';
//				}
//				,no: function(index){
//					window.location.href='/index/index';
//				}
//			});
//		}


		$(function(){

			var nav = $('.swiper-nav').swiper({
				slidesPerView: 'auto',
				freeMode:true,
				noSwiping : true,
				freeModeFluid:true,
				calculateHeight : true,
				visibilityFullFit: true,
				onSlideClick: function(nav){
					pages.swipeTo( nav.clickedSlideIndex )
				}
			})

			//Function to Fix Pages Height
			function fixPagesHeight() {
				$('.swiper-pages').css({
					height: $(window).height()-nav.height-40
				})
			}
			$(window).on('resize',function(){
				fixPagesHeight()
			})
			fixPagesHeight()

			//Init Pages
			var pages = $('.swiper-pages').swiper({
				noSwiping : true,
				onSlideChangeStart: function(){
					$(".swiper-nav .active").removeClass('active')
					$(".swiper-nav .swiper-slide").eq(pages.activeIndex).addClass('active')
				},
			})

			var mySwiper = new Swiper('#banner',{
				loop:true,
				autoplay:5000,
				calculateHeight : true,
				pagination : '.pagination',

				//其他设置
			});
		})

		layer.close(load_index);
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
			link: 'http://zt.wisecareer.org/hangye/index',
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
			link: 'http://zt.wisecareer.org/hangye/index',
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



<script src="//www.wisecareer.org/left_and_right_slide/js/idangerous.swiper-2.0.min.js"></script>

<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1273552666'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/z_stat.php%3Fid%3D1273552666'type='text/javascript'%3E%3C/script%3E"));</script></body>
</html>
