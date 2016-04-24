<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" >
<meta name="format-detection" content="telephone=no"/>
<meta name="format-detection" content="telephone=no" /><!--禁用iphone自动将数字串转化为电话-->
<title>电影集市</title>
<link href="__CSS__/style.css?2" rel="stylesheet" type="text/css" media="screen"/>
<link href="__CSS__/swiper.min.css" rel="stylesheet" type="text/css" media="screen"/>
<script src="__JS__/jquery-1.10.1.min.js"></script>
<script src="__JS__/swiper.jquery.min.js"></script>
<script src="http://ser3.graphmovie.com/boo/js/gw2c.js"></script>
<style type="text/css">
*{outline:none;-webkit-touch-callout: none;-webkit-user-select: none;}
*:before{content:'';} *:after{content:'';}
body,html {font-size:0.8em;}
.border {
	border:1px solid red;
}
.head{
	width:100%;
}
.head .item {
	height:100%;background:url('__PUBLIC__/image/head.jpg') no-repeat center;background-size:100% auto;
}
.head .top_head {
	line-height:50px;padding-left:10px;font-size:1.4em;
	background:url('__PUBLIC__/image/line.png') no-repeat center;background-size:auto 40%;
}
.top_head .half {
	float:left;width:50%;text-align:center;
}
.top_head .gold {
	background:url('__PUBLIC__/image/shop_icon_goldcoin.png') no-repeat left center;background-size:auto 100%;
	padding-left:1.5em;
}
.head .prize {
	background:url('__PUBLIC__/image/shop_icont.png.png') no-repeat left center;background-size:auto 100%;
	padding-left:1.5em;
}
.content {overflow:visible;}
.content .swip-img{
	line-height:0;
}
.content .swip-img img {
	width:100%;height:100%;
}
.content .info { 
	color:#999;padding:0 10px;line-height:1.8em;
}
.content .info h4 {
	font-size:1.4em;
	font-weight:300;color:#333;
	margin:0px; line-height:2.1em;
}
.content .bar {
	padding:5px 10px;
}
.bar .timer {
	font-size:0.9em;
	background:url('__PUBLIC__/image/shop_icon_time2.png') no-repeat left center;background-size: auto 90%;
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
.bar .grery {
	background-color:#ccc;color:#fff;
}
.in-line {
display:block;               /*内联对象需加*/
word-break:keep-all;         /* 不换行 */
white-space:nowrap;          /* 不换行 */
overflow:hidden;             /* 内容超出宽度时隐藏超出部分的内容 */
text-overflow:ellipsis;      /* 当对象内文本溢出时显示省略标记(...) ；需与overflow:hidden;一起使用。*/
}
.clear {
	clear:both;
}
.content .line {
	height:30px;
	background-color:#eee;
	position:relative;
	z-index:0;

}


.narror {
	position:relative;
}
.narror:after {
	transform:rotate(-45deg);
	-webkit-transform:rotate(-45deg);
	position:absolute;right:10;top:24px;
	border:2px solid #ccc;border-left:0;border-top:0;width:10px;height:10px;
}

.banner{
	width:100%;
	height: 72px;
	padding:0px;
	background-color: #ebf0f2;
	z-index: 0;
	overflow: visible;
}
.banner-words-left{
	height:24px;
	float: left;
	margin:24px 0px 24px 20px;
	line-height:24px;
	font-size:22px;
	color: #6e6e75;
}
.banner-words-right{
	height:24px;
	float: right;
	margin:24px 20px 24px 0px;
	line-height:24px;
	font-size:22px;
	color: #19191e;
}
#shop_icon_guide{
	width:24px;
	height:24px;
	float: right;
	margin:24px 10px 24px 0px;
}
.content .swip-img .mark{
	width:96px;
	height:90px;
	position: absolute;
	top:-10px;
	z-index: 9999999;
}
</style>
</head>
<body>
<div id="box">
	<div id="main">
		<div class="head swiper-container">
			<div class="top_head">
				<div class="half"><span id="my_gold" class="gold" data-t="我的金币" data-url='http://ser3.graphmovie.com/appweb/uc/goldList-x.php'><?php echo ($gold); ?></span></div>
				<div class="half"><span id="my_order_list" class="prize" data-t="我的奖品" data-url='<?php echo U("Index/orderList","",false,false,true);?>'>我的奖品</span></div>
				<div class="clear"></div>
			</div>
			<div class="swiper-wrapper" id="nav_head">
				<?php if(NOW_TIME <= 1456696800 and $_GET['pub_platform'] == 'android'): ?><!-- <div data-t="给梦想一个机会" data-url="http://caipiao.163.com/nfop/tgwdownload/index.htm?from=tgwweidong4" class="item swiper-slide" data-upvc="0" style="background-image:url(news/4/b.jpg);"> </div> --><?php endif; ?>
				<div data-t="集市规则" data-url="http://ser3.graphmovie.com/appweb/shop/news/1/" class="item swiper-slide" style="background-image:url(news/1/b.jpg);"> </div>
			</div>

			<div class="swiper-wrapper" id="nav_head1">
				<?php if(NOW_TIME <= 1456696800 and $_GET['pub_platform'] == 'android'): ?><!-- <div data-t="给梦想一个机会" data-url="http://caipiao.163.com/nfop/tgwdownload/index.htm?from=tgwweidong4" class="item swiper-slide" data-upvc="0" style="background-image:url(news/4/b.jpg);"> </div> --><?php endif; ?>
				<div data-t="转盘抽奖" data-url='<?php echo U("Index/rotateLottery",array("k"=>$vo["goods_id"]),false,false,true);?>' class="item swiper-slide" style="background-image:url(news/1/b.jpg);"> </div>
			</div>

			<div class="banner">
				<p class="banner-words-left" data-t="全部商品" data-url="http://ser3.graphmovie.com/appweb/shop/news/1/">全部商品</p>

				<p class="banner-words-right" data-t="集市规则" data-url="http://ser3.graphmovie.com/appweb/shop/news/1/">集市规则</p>
				<img src="__PUBLIC__/image/shop_icon_guide.png" id="shop_icon_guide" alt="shop_icon_guide">
			</div>
		</div>
		<div class="content">
			<?php if(empty($list)): ?><h1>还没上架任何商品哦，请稍后再进来吧。</h1><?php endif; ?>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="line"></div>
			<?php if($vo['cat_id'] == 1): ?><div class="item" data-t="<?php echo ($vo["name"]); ?>" data-url='<?php echo U("Index/detail",array("k"=>$vo["goods_id"]),false,false,true);?>'>
			<?php elseif($vo['cat_id'] == 2): ?>
				<div class="item" data-t="<?php echo ($vo["name"]); ?>" data-url='<?php echo U("Index/exchangeDetail",array("k"=>$vo["goods_id"]),false,false,true);?>'><?php endif; ?>


					<div class="swiper-container" style="overflow:visible">
						<div class="swiper-wrapper">
							<?php if(is_array($vo["img_list"])): $i = 0; $__LIST__ = $vo["img_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><div class="swip-img swiper-slide">
								<?php if($vo['cat_id'] == 1): ?><img class="mark" src="__PUBLIC__/image/shop_icon_lottery.png" alt="shop_icon_lottery">
								<?php elseif($vo['cat_id'] == 2): ?>
									<img  class="mark" src="__PUBLIC__/image/shop_img_seckilling.png" alt="shop_img_seckilling"><?php endif; ?>
								<img data-src="<?php echo str_replace('ser3.graphmovie.com','avatar.graphmovie.com',$img['url']);?>" class="swiper-lazy">
								<div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
							</div><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					</div>
					<div class="info">
						<h4 class="in-line"><?php echo ($vo["name"]); ?></h4>
						<div><?php echo ($vo["desc"]); ?></div>
						<div><?php echo formartDate($vo['lottery']['start_time'],'Y-m-d H:i');?> 总共<?php echo ($vo["total_num"]); ?>件，等你来抽</div>
					</div>
					<div class="bar">
							<?php if(strtotime($vo['lottery']['start_time']) > NOW_TIME): ?><div class="timer"><span class="js-timer" data-t="<?php echo strtotime($vo['lottery']['start_time']);?>"></span>  后开始</div>
								<span class="btn blue"><?php echo Intval($vo['price']);?> 金币</span>
							<?php elseif(strtotime($vo['lottery']['end_time']) > NOW_TIME): ?>
								<div class="timer"><span class="js-timer" style="color:#999;" data-t="<?php echo strtotime($vo['lottery']['end_time']);?>"></span>  后结束</div>
								<?php if($vo['ku_cun'] > 0): ?><span class="btn blue"><?php echo Intval($vo['price']);?> 金币</span>
								<?php else: ?>
									<span class="btn grery">已抽完</span><?php endif; ?>
							<?php else: ?>
								<div class="timer">已结束</div>
								<span class="btn grery"><?php echo Intval($vo['price']);?> 金币</span><?php endif; ?>
						<div class="clear"></div>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>
</div>
<script>
$('document').ready(function(){
	var w = 0, h = 0 ;
	var t = <?php echo NOW_TIME;?>;
	function initP(){
		w = $('#main').width();
		h = $('window').height();
		$('#nav_head').height(w*230/640);
		$('#nav_head1').height(w*230/640);
		$('.swip-img').height(w*460/640);
	};
	$(window).resize(initP);
	initP();
	var mySwiper = new Swiper ('.swiper-container', {
	    direction: 'horizontal',  //Slides的滑动方向，可设置水平(horizontal)或垂直(vertical)
	    loop: false,//默认false,loop模式在与free模式同用时会产生抖动
        preloadImages: false,//默认为true，Swiper会强制加载所有图片
        lazyLoading: true   //设为true开启图片延迟加载，使preloadImages无效
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
		//location.href = $(this).attr("data-url");
		var url = $(this).attr("data-url");
		//console.log(url);
		var t = $(this).attr("data-t");
		if (t.length>18) {
			t = t.substr(0,17) + '...';
		}
		var upvc = $(this).attr('data-upvc');
		if (typeof upvc == 'undefined') {upvc=1;}
		GMBase.openUrl ({url:url,upvc:upvc,t:t, s:1});
	});
	
	$('#my_gold').click(function(){
		var url = $(this).attr("data-url");
		var t = $(this).attr("data-t");
		if (t.length>8) {
			t = t.substr(0,7) + '...';
		}
		GMBase.openUrl ({url:url,upvc:1,t:t, s:1});
	});
	$('#my_order_list').click(function(){
		var url = $(this).attr("data-url");
		var t = $(this).attr("data-t");
		if (t.length>8) {
			t = t.substr(0,7) + '...';
		}
		GMBase.openUrl ({url:url,upvc:1,t:t, s:1});
	});

	//特殊样式处理
	$(".content .line:first").hide();

});
</script>
<div style="display: none;">
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?37321f95a4f926d7977d276c8c3280b6";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script></div>

</body>
</html>