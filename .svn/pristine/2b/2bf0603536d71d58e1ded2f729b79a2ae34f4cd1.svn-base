<?php
	$users = include 'base/config/soap_users.php';;
	$user_id = isset ($_GET['userid']) ? $_GET['userid'] : null;
	if (isset($_GET['gm_u'])) { // 统一登录入口登录进入
		$user_id = $_GET['gm_u'];
	}
	$url = '';
	if (isset($user_id)) {
		if (!in_array($user_id, $users)){
			$url = 'javascript:void(0);" onclick="popup(\'嗯哼~~你不在榜上哦~\')';
		} else {
			$url = 'http://ser3.graphmovie.com/appweb/member/info.php?userid='.$user_id;
		}
	} else {
		$ul = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$url = 'http://ser3.graphmovie.com/appweb/login/?ul='.$ul;
	}
?><!DOCTYPE html>
<html class="ui-mobile">
<head>
<title>捡の肥皂榜</title>
<!-- META SECTION-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=false">
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- STYLES SECTION -->

<link type="text/css" rel="stylesheet" href="css/jquery.css">
<link type="text/css" rel="stylesheet" href="css/lightOrange.css">
<link href="css/css.css" rel="stylesheet" type="text/css">

<style type="text/css">
blockquote p {
	font-family: 'Times new Roman', serif;
}

h1 a {
	font-family: 'Lobster', cursive;
}

.ui-body-a,.ui-body-a input,.ui-body-a select,.ui-body-a textarea,.ui-body-a button,.ui-btn-up-a,.ui-btn-hover-a,.ui-btn-down-a,.ui-btn-active,textarea,p,a,h2,h3,h4,h5,h6,span,.ui-bar-a,.ui-bar-a input,.ui-bar-a select,.ui-bar-a textarea,.ui-bar-a button
	{
	font-family: 'Arial', sans-serif;
}

.pick-button.blue {
	background: #00b6ff;
	border: none;
}

.pick-button {
	border-radius: 5px 5px 5px 5px;
	color: #FFFFFF;
	font-weight: bold;
	line-height: 38px;
	margin: 0 20px 20px 0;
	overflow: hidden;
	position: relative;
	text-align: center;
	/*text-shadow: 1px 1px 1px #333333;*/
}


.clear {
	clear: both;
}

.hr {
	clear: both;
	border-top: 1px solid #f8f8f8;
	height: 1px;
}



.button {
	float: left;
	width: 120px;
	line-height: 38px;
	text-align: center;
	font-weight: bold;
	color: #fff;
	text-shadow: 1px 1px 1px #333;
	border-radius: 5px;
	margin: 0 20px 20px 0;
	position: relative;
	overflow: hidden;
}

.button:nth-child (6n){
	margin-right: 0;
}

.button.gray {
	color: #8c96a0;
	text-shadow: 1px 1px 1px #fff;
	border: 1px solid #dce1e6;
	box-shadow: 0 1px 2px #fff inset, 0 -1px 0 #a8abae inset;
	background: -webkit-linear-gradient(top, #f2f3f7, #e4e8ec);
	background: -moz-linear-gradient(top, #f2f3f7, #e4e8ec);
	background: linear-gradient(top, #f2f3f7, #e4e8ec);
}
.menu_top {
	width:33%;float:left;text-align:center; height:22px;font-size:14px;cursor:pointer;font-weight:bold;
	line-height:22px;
	border-bottom:1px solid #ccc;
	margin-bottom:5px;
	padding-bottom:2px;
}
.cur {
	border-bottom:2px solid #00aaff;
	padding-bottom:1px;
}

.index{font-size:30px; line-height:45px; bont-weight:bold;color:#ff00a0;width:40px;text-align:center;float:left;}
.avatar{ width:40px; height:40px; border-radius:40px; float:left;}
.item {padding:0px 0px;}
.nums {float:right; font-size:30px; line-height:45px; bont-weight:bold;color:#00aaff;position:relative;padding-right:15px;}
.name {font-size:16px; line-height:40px; padding-left:5px;float:left;color:#555;}

.pick_info_item {display:none;}
.pick_info {background-color:#f2f2f2; padding:0px 2px; position:relative;}
.pick_time, .pick_reason {color:#666;font-size:10px;float:left; height:12px; padding:2px 5px;line-height:12px;}
.pick_reason{line-height: 12px;padding:9px 0px; border-left:1px solid #aaa; margin-left:10px; padding-left:8px;}
.pick_time {border:5px solid #fff; border-radius:15px;background-color:#00aaff;color:#fff;font-weight:bold;margin:2px 0px;}
.time_ge {position:absolute;left:77px; top:1px; background:url('images/selectbg.gif'); background-size:100% 100%; width:15px; height:13px;margin-left:5px; margin-top: 6px;}

.month-2,.month-1,.month-3{float:left;width:33%; height:330px; background-size:100% auto;background-repeat:no-repeat;background-position:0 bottom;}
.month-2 {background-image:url('images/second.png');}
.month-1{width:34%; background-image:url('images/first.png');}
.month-3 {background-image:url('images/third.png');}

.targ {background:url('images/flagg.png') no-repeat;width:90%;margin:0px auto;background-size:100% auto;height:100%;}
.month-2 .targ{background-position:0 28%}
.month-1 .targ{background-position:0 10%}
.month-3 .targ{background-position:0 38%;}

.m_user {position:relative;margin:0px auto;height:auto; text-align:center; color:#00aaff;font-weight:bold;}
.month-2 .m_user {padding-top:91%;width:62%;}
.month-1 .m_user {padding-top:51%;width:65%;}
.month-3 .m_user {padding-top:111%;width:60%;}

.month-2 .name,.month-1 .name,.month-3 .name {float:none;padding-top:35%;font-size:10px;text-align:center;color:#cb5432;line-height:12px;font-weight:bold;}
</style>

</head>
<body class="home blog ui-mobile-viewport">
<div style="min-height: 595px;" class="ui-page ui-body-c ui-page-active" tabindex="0" data-url="/themes/breathe_wp/HTML/ORANGE/" data-role="page">
	<!--Header--> 
	<header role="banner" class="ui-header ui-bar-a" data-role="header">
		<h1 aria-level="1" role="heading" tabindex="0" class="ui-title"><a class="ui-link" href="#">捡の肥皂榜</a></h1>
		<hr class="divider">
	</header> <!-- Heder END  --> 
	<!-- Main Content--> 
	<a id="headxxxxx"></a>
	<section role="main" data-role="content" class="wrappedWidth ui-content" id="index">
	<!-- Latest news section-->
		<section id="latest-news">
			<div class="section-image">
				<a class="ui-link">
					<img class="mainIMG" src="http://ser3.graphmovie.com/boo/adv/57/57_20140708222632_s.jpg" alt="graphmovie's timeline">
					<div style="text-align:center;font-weight:bold;font-size:14px;;color:#00aaff;line-height:1.4em;">【捡の肥皂榜，自14年9月5日起，停榜休整】 <br> <br>“肥皂君闭关精修，<br>待我皂丝绾正，<br>少年你捡我可好？”<br> <br> &mdash;&mdash;肥皂君绝笔  <br><br></div> 
				</a>
				<div>
					<span class="menu_top cur" data-class="week">周榜</span>
					<span class="menu_top" data-class="month">月榜</span>
					<span class="menu_top" data-class="year">年榜</span>
				</div>
				<div id="week">
				{$week}
					<button class="button gray" id="go_pre">&lt;&lt; 上一期</button>
					<button style="float: right; margin: 0px;" class="button gray" id="go_next">下一期 &gt;&gt;</button>
				</div>

				<div id="month" style="display:none;">
					{$month}
					<div class="clear" style="height:10px;">&nbsp;</div>
					<button id="go_month_pre" class="button gray">&lt;&lt; 上一期</button>
					<button id="go_month_next" class="button gray" style="float: right; margin: 0px;">下一期 &gt;&gt;</button>
				</div>

				<div id="year" style="display:none;">
					<div style="text-align:center;font-size:20px;font-weight:bold;color:#00aaff;">2015年1月1日 更新</div>
					<img style="margin-left:15%;" src="http://ser3.graphmovie.com/appweb/appwall/soap/img/picking.gif" width="60%"/>
				</div>
			</div>
			<div class="clear"></div>
			<div style="color: #DF1269; font-size: 12px;" id="ErrorInfo"></div>
		</section>
		<div id="set_info">
			<p>
			注意：请上榜的童鞋在 <a href="<?php echo $url;?>" id="go_set_info">“这里”</a> 填写信息以便领取相应福利。么么哒~ 不要错过哦╭(●｀∀´●)╯
			</p> 
		</div>
	</section><!-- Main Content END  --> 
	<!-- Footer --> 
	<footer role="contentinfo" class="ui-footer ui-bar-a" data-role="footer" data-theme="a" data-position="inline">
		<hr class="divider">
		<h4 aria-level="1" role="heading" tabindex="0" class="ui-title">关注我们：</h4>
		<ul class="shareIcons">
			<li><a class="ui-link" rel="external" onClick="javascript:popup(0);"><img src="images/social_qq.png" alt="QQ群"></a></li>
			<li><a class="ui-link" rel="external" onClick="javascript:popup(1);"><img src="images/social_weixin.png" alt="微信"></a></li>
			<li><a class="ui-link" rel="external" onClick="javascript:popup(2);"><img src="images/social_sina.png" alt="Sina微博"></a></li>
			<li><a class="ui-link" rel="external" onClick="javascript:popup(3);"><img src="images/social_ie.png" alt="官方网站"></a></li>
		</ul>
		<div id="text-2" class="widget widget_text">
			<h4 class="widgettitle">联系我们：</h4>
			<div class="textwidget">
				<p>QQ交流群：&nbsp;297443896<br />
				商务QQ：&nbsp;&nbsp;1470937559<br />
				服务热线：&nbsp;&nbsp;0755-83209359<br />
				客服时间：&nbsp;&nbsp;7*24*365 在线<br />
				<br />
				如有问题，欢迎您的来信。<br />
				商务合作：<a class="ui-link" href="mailto:handshake@graphmovie.com">handshake@graphmovie.com</a><br />
				产品建议：<a class="ui-link" href="mailto:blabla@graphmovie.com">blabla@graphmovie.com</a><br />
				BUG反馈：<a class="ui-link" href="mailto:TOT@graphmovie.com">TOT@graphmovie.com</a><br />
	
				如果侵犯了您的权益，请联系：<br />
				<a class="ui-link" href="mailto:or2@graphmovie.com">or2@graphmovie.com</a><br />
				地 址：深圳市福田区深南大道2008号中国凤凰大厦2号楼11B<br />
				邮 编：518006</p>
			</div>
		</div>
	</footer> <!--  Footer END  -->
</div>
<!-- Page initialization END  -->

<div style="top: 297.5px;" class="ui-loader ui-body-a ui-corner-all">
	<span class="ui-icon ui-icon-loading spin"></span>
	<h1>loading</h1>
</div>

<div id="pop_wnd" style="display: none; text-align: center;">
	<div style="display: block;"><img id="pop_img" src="images/feizao.png" style="display: block; margin: auto;" /></div>
	<span style="display: block; margin-top: 10px; text-align: center;" id="pop_msg"></span><br />
	<div style="display: block; text-align: center;">
		<button class="pick-button blue" style="min-width: 20%;" onClick="javascript:removeBlockUIWnd();"><span id="pop-btn-txt"></span></button>
	</div>
</div>

<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript">
			function popup(type) {
				//更改按钮文字
				$('#pop-btn-txt').html();
				$('#pop_msg').html();
				$("#pop_img").attr("src"," ");
				if(type==0){
					//QQ群	
					$("#pop_img").attr("src","images/social_qq.png");
					$('#pop-btn-txt').html('┏(●´Д｀●)┛我来啦!');
					$('#pop_msg').html('QQ粉丝群：297443896');
				}else if(type==1){
					//weixin	
					$("#pop_img").attr("src","images/social_weixin.png");
					$('#pop-btn-txt').html('┏(●´Д｀●)┛我来啦!');
					$('#pop_msg').html('公众号：图解电影GM');
				}else if(type==2){
					//sinawb
					$("#pop_img").attr("src","images/social_sina.png");
					$('#pop-btn-txt').html('┏(●´Д｀●)┛我来啦!');
					$('#pop_msg').html('搜索：图解电影GM');
				}else if(type==3){
					//web
					$("#pop_img").attr("src","images/social_ie.png");
					$('#pop-btn-txt').html('(｀･ω･´)好难记!');
					$('#pop_msg').html('官网：graphmovie.com');
				} else {
					//oter
					$('#pop_img').attr('src','images/warning.png');
					$('#pop-btn-txt').html('（＞д＜） 争取上榜！');
					$('#pop_msg').html(type);
				}
				
				$.blockUI({ 								
					message: $('#pop_wnd'), 
					css: { 
							//top: '20%',
							border: '1px solid #DFDFDF', 
							padding: '15px', 
							backgroundColor: '#FFF', 
							'-webkit-border-radius': '10px', 
							'-moz-border-radius': '10px', 
							opacity: 0.9, 
							color: '#000',
							width: '250',
							height:'200',
							top:(document.body.clientHeight-230)/2,
							left:(document.body.clientWidth-280)/2
						 } 
				});	
			}
			function removeBlockUIWnd(){
				$.unblockUI();
			}
</script>
<script>
			$('document').ready(function(){
				var resize = function(){
					var width = $('.soap_month_flagg').width();
					if (width>50) {
						$('.soap_month_flagg').height(width * (330/108));
					}
				};
				resize();
				$(window).resize(function(){
					resize();
				});
				
				var len = $('.soap_week').length;
				var current_topten = 'soap_week_';
				for (var i =2; i<= len; i++) {
					$('#'+current_topten+i).hide();
				}
				
				var month_len = $('.soap_month').length;
				var current_top_month = 'soap_month_';
				for (var i=2; i<= month_len; i++) {
					$('#'+current_top_month+i).hide();
				}

				var errorInfo = function(msg){
					$('#ErrorInfo').html(msg);
				};

				var now_index = 1;
				$('#go_pre').click(function(){
					errorInfo('');
					if (now_index >= len) {errorInfo('亲啊~~木油上一期了哟！');return;}
					$('#'+current_topten + now_index).hide();
					now_index++;
					$('#'+current_topten + now_index).show();
					$("html,body").stop().animate({scrollTop: $("#headxxxxx").offset().top}, 500);
				});

				$('#go_next').click(function(){
					errorInfo('');
					if (now_index <= 1) {errorInfo('啊亲~~木油下一期了呢！');return;}
					$('#'+current_topten + now_index).hide();
					now_index--;
					$('#'+ current_topten + now_index).show();
					$("html,body").stop().animate({scrollTop: $("#headxxxxx").offset().top}, 500);
				});
				
				var now_month_index = 1;
				$('#go_month_pre').click(function(){
					errorInfo('');
					if (now_month_index >= month_len) {errorInfo('亲啊~~木油上一期了哟！');return;}
					$('#'+current_top_month + now_month_index).hide();
					now_month_index++;
					$('#'+current_top_month + now_month_index).show();
					$("html,body").stop().animate({scrollTop: $("#headxxxxx").offset().top}, 500);
				});

				$('#go_month_next').click(function(){
					errorInfo('');
					if (now_month_index <= 1) {errorInfo('啊亲~~木油下一期了呢！');return;}
					$('#'+current_top_month + now_month_index).hide();
					now_month_index--;
					$('#'+ current_top_month + now_month_index).show();
					$("html,body").stop().animate({scrollTop: $("#headxxxxx").offset().top}, 500);
				});
				
				$('.item').click(function(){
					var index = $(this).attr('data-pindex');
					if (typeof index != 'undefined') {
						var $obj = $('#pick_info_'+index);
						if (index.split('month').length>1){ // movie
							var id_pre = index.substr(0,(index.length-1));
							for (var i=1; i<=3; i++) {
								if (id_pre+i != index) {
									$('#pick_info_'+id_pre+i).hide();
								}
							}
							$("html,body").stop().animate({scrollTop: $("#headxxxxx"+now_month_index).offset().top}, 500);
							$('#pick_info_'+index).slideToggle('fast');
						} else {
							$('#pick_info_'+index).slideToggle('fast');
						}
					}
				});

				$('.menu_top').click(function(){
					errorInfo('');
					$('#week').hide();
					$('#month').hide();
					$('#year').hide();
					var show = $(this).attr('data-class');
					$('#'+show).show();
					$('.menu_top').removeClass('cur');
					$(this).addClass('cur');
					resize();
				});
			});
</script>
</body>
</html>