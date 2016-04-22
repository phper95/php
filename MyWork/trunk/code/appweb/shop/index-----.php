<?php
header("Content-type:text/html;charset=utf-8");
ini_set('date.timezone','Asia/Shanghai'); 
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" >
<title>任务详情</title>
<link href="css/style.css?2" rel="stylesheet" type="text/css" media="screen"/>
<link href="css/swiper.min.css" rel="stylesheet" type="text/css" media="screen"/>
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="js/swiper.jquery.min.js"></script>
<style type="text/css">
body,html {font-size:0.8em;}
.border {
	border:1px solid red;
}
.head{
	width:100%;
}
.head .item {
	height:100%;background:url('image/head.jpg') no-repeat center;background-size:100% auto;
}
.content {padding-top:20px;overflow:hidden;}
.content .swip-img img {
	width:100%;height:100%;
}
.content .info { 
	color:#999;padding:0 10px;line-height:1.8em;
}
.content .info h4 {
	font-size:1.0em;
	font-weight:300;color:#333;
	margin:0px; line-height:2.5em;
}
.content .bar {
	padding:10px;
}
.bar .timer {
	background:url('image/shop_icon_time.png') no-repeat left center;background-size: auto 90%;
	padding:0.6em 0 0.6em 2.4em;float:left;color:#999;
}
.bar .timer span{
	color:#00b9ff;
}
.bar .btn {
	padding:0.6em 1em;float:right;border-radius:50px;
}
.bar .blue {
	background-color:#00b9ff;color:#fff;
}
.in-line {
	display:block;                     /*内联对象需加*/
word-break:keep-all;           /* 不换行 */
white-space:nowrap;          /* 不换行 */
overflow:hidden;               /* 内容超出宽度时隐藏超出部分的内容 */
text-overflow:ellipsis;         /* 当对象内文本溢出时显示省略标记(...) ；需与overflow:hidden;一起使用。*/
}
.clear {
	clear:both;
}
.content .line {
	height:15px; background-color:#eee;
}
</style>
</head>
<body>
<div id="box">
	<div id="main">
		<div id="nav_head" class="head swiper-container">
			<div class="swiper-wrapper">
				<div class="item swiper-slide">asdfasdf</div>
			</div>
		</div>
		<div class="content">
			<div class="item">
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<div class="swip-img swiper-slide">
							<img data-src="1.jpg" class="swiper-lazy">
			                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
						</div>
						<div class="swip-img swiper-slide">
							<img data-src="2.jpg" class="swiper-lazy">
			                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
						</div>
					</div>
				</div>
				<div class="info">
					<h4 class="in-line">华谊兄弟电影周边 阿里快速的减肥啦看似简单发送到</h4>
					<div>拉开手机单老师夸奖对方了水电费</div>
					<div>2015-11-26 10:00 总共12件，等你来抽</div>
				</div>
				<div class="bar">
					<div class="timer"><span class="js-timer" data-t="<?php echo strtotime('2015-11-28 00:00:00');?>"></span>  后开始</div>
					<span class="btn blue">50 金币</span>
					<div class="clear"></div>
				</div>
			</div>
			<div class="line"></div>
			<div class="item">
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<div class="swip-img swiper-slide">
							<img data-src="1.jpg" class="swiper-lazy">
			                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
						</div>
						<div class="swip-img swiper-slide">
							<img data-src="2.jpg" class="swiper-lazy">
			                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
						</div>
					</div>
				</div>
				<div class="info">
					<h4 class="in-line">华谊兄弟电影周边 阿里快速的减肥啦看似简单发送到</h4>
					<div>拉开手机单老师夸奖对方了水电费</div>
					<div>2015-11-26 10:00 总共12件，等你来抽</div>
				</div>
				<div class="bar">
					<div class="timer"><span>17小时25分2秒</span>  后开始</div>
					<span class="btn blue">50 金币</span>
					<div class="clear"></div>
				</div>
			</div>
			<div class="line"></div>
		</div>
	</div>
</div>
<script>
$('document').ready(function(){
	var w = 0, h = 0 ;
	var t = <?php echo time();?>;
	function initP(){
		w = $('#main').width();
		h = $('window').height();
		$('#nav_head').height(w*230/640);
		$('.swip-img').height(w*460/640);
	};
	$(window).resize(initP);
	initP();
	var mySwiper = new Swiper ('.swiper-container', {
	    direction: 'horizontal',
	    loop: false,
        preloadImages: false,
        lazyLoading: true
	});

	function refreshTimer() {
		$('.js-timer').each(function(){
			var timer = $(this).attr('data-t');
			if (timer) {
				var cha_time = parseInt(timer) - t;
				if (cha_time > 0) {
					$(this).html(sec2txt(cha_time));
				}
			}
		});
		t ++;
	};
	var timmer = setInterval(refreshTimer, 1000);
	var sec2txt = function(sec){
		var s = sec % 60;
		var i = Math.floor (sec / 60) % 60;
		var h = Math.floor (sec / 3600) % 24;
		var d = Math.floor (sec / 86400);
		return (d>0?(d+'天'):'') + (d+h>0?(h+'小时'):'') + (d+h+i>0?(i+'分'):'') + (d+h+i+s>0?(s+'秒'):'');
	};

	$('.item').click(function(){
		alert('sb');
	});
});
</script>
</body>
</html>
	
	
	
	
	
	