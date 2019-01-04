<?php /*a:1:{s:77:"D:\ljj\PHPTutorial\WWW\wisecareer\application\view\zhituo\hangye\zy_list.html";i:1545031442;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>职拓e站</title>
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<meta name="MobileOptimized" content="320"/>
	<link href="//www.wisecareer.org/css/zhituo/style.css" type="text/css" rel="stylesheet" />
	<script src="/static/public/js/jquery-1.8.3.min.js"></script>
	<script src="/static/lib/layer/mobile/layer.js"></script>
	<link rel="stylesheet" href="/static/lib/layer/mobile/need/layer.css"/>
	<script src="/static/zhituo/js/base.min.js"></script>
	<script src="/static/zhituo/js/com.min.js"></script>
	<script src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<style>
		table#tit h2{font-size: 16px;font-weight: bold;color: #666}
		table#tit h3{font-size: 14px;font-weight: bold;color: #666}
		table#content td{font-size: 14px;color: #666;line-height: 30px}
		table#content th{font-size: 16px;color: #666;line-height: 36px}
	</style>
</head>
<body>
<!-- head -->
	<div class="head" style="position: fixed;top: 0;left: 0;z-index: 300">
		<h1 style="margin: 0"><span id="obj"></span>职业列表</h1>
		<a href="javascript:history.go(-1)" class="return"><i class="return-right"></i></a>
	</div>


<!-- home -->
<div id="dBody">
	<div class="cleardiv50"></div>
	<div class="mycontent">
		<div class="inner">
			<div class="cleardiv20"></div>

			<table width="100%" id="content">
				<tr>
					<th width="35%" align="left">职业名称</th>
					<th width="30%">层级</th>
					<th width="35%">代言教练</th>
				</tr>
			</table>
			<div class="cleardiv20"></div>
		</div>
	</div>
</div>
<script>
    var token = localStorage.CURRENT_USER_ID;
    Request = GetRequest();

	var tpid = getUrlParam("tpid");
	$('#hydt').attr('onclick','location.href=\'/hangye/dynamic_list?classid='+tpid+'\'');
    $.getJSON(WEB_API_URL  + "Occupation/occuSearch?"+Request,{token:token},function (json) {
        if(json.status ==0){
            $('#hangye').html('行业：'+json.data.titlename);
            var tr = '';

            $.each(json.data.list,function (index,datas) {
                tr += '<tr onclick="location.href=\'/hangye/zy_content?id='+datas.id+'&num='+datas.num+'\'">';
                tr += '<td width="35%" align="left">'+datas.titlename+'</td>';
                tr += ' <td width="30%" align="center">'+datas.jibiestr+'</td>';
                tr += '<td width="35%" align="center" style="background: url(http://www.wisecareer.org/images/zhituo/icon/arrow-right.png) no-repeat right center;background-size: 6px;"><span style="color: red;font-weight: bold;text-align: center">'+datas.num+'</span> 人 </td></tr>'
            });
            $('#content').append(tr)
        }else{
            //window.location.href='/index/index'
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
			link: 'http://zt.wisecareer.org/hangye/zy_list',
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
			link: 'http://zt.wisecareer.org/hangye/zy_list',
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
