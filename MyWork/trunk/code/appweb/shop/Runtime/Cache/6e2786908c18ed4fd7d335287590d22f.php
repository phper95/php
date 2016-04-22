<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" >
<meta name="format-detection" content="telephone=no"/>
<title>任务详情</title>
<link href="__CSS__/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="__CSS__/style.css?2" rel="stylesheet" type="text/css" media="screen"/>
<link href="__CSS__/swiper.min.css" rel="stylesheet" type="text/css" media="screen"/>
<script src="__JS__/jquery-1.10.1.min.js"></script>
<!--<script src="../js/jquery-1.10.1.min.js"></script>-->
<script src="__JS__/swiper.jquery.min.js"></script>
<script src="__JS__/lib.js?1"></script>
<script src="http://ser3.graphmovie.com/boo/js/gw2c.js"></script>
<style type="text/css">
#main {font-size:0.8em;}
a{text-decoration:none;}
.border {
	border:1px solid red;
}
.content {overflow:hidden;}
.content .item {font-size:0.8em;}
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
	background:url('__PUBLIC__/image/shop_icon_time.png') no-repeat left center;background-size: auto 90%;
	padding:0.6em 0 0.6em 2.4em;float:left;color:#999;
}
.bar .timer span{
	color:#00b9ff;
}
.bar .price{
	float:right;
	padding:0em 1em; color:#00b9ff;font-size:1.2em;
}
.btn {
	padding:0.6em 1em;border-radius:50px;
}
.blue {
	background-color:#00b9ff;color:#fff;
}
.grery {
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
	height:10px; background-color:#eee;
}
.narror {
	position:relative;
}
.narror:after {
	transform:rotate(-45deg);
	position:absolute;right:10;top:10px;
	border:2px solid #ccc;border-left:0;border-top:0;width:12px;height:12px;
}

.detail {
	font-size:1.2em;line-height:1.2em;padding:10px;margin-bottom:50px;
}
.detail p{
	margin:0.2em 0;
}
.foot-bar {
	position:fixed;bottom:0;left:0;width:100%;height:60px;background-color:#fff; text-align:center;
	line-height:60px;font-size:1.2em;
}
.rst-info {
	position:fixed;top:0;left:0;z-index:20;width:100%;min-height:100%;background-color:rgba(0,0,0,0.8);
	display:none;
}
.rst-panel {
	max-width:600px;min-width:300px;margin:0 auto;position:relative;
}
.rst-main {
	width:86%;position:relative;margin:0 auto;
	margin-top:20%;
	background-color:#e04441;border-radius:20px;
	color:#fff;
}
.rst-head {
	position:relative;
	padding-bottom:40px;
	overflow:hidden;
	border-top-left-radius:20px;
	border-top-right-radius:20px;
}
.rst-head h4 {
	font-size:2.0em;text-align:center;margin:0;line-height:1.8em;
}
.rst-thing {
	padding:20px 20px 0 20px;
	min-height:50px;position:relative;
}
.rst-img {
	float:left;width:26%;min-height:120px;
	background:url('__PUBLIC__/image/giftbox.png') no-repeat center;
	background-size:100% auto;
}
.rst-gift {
	float:left;font-size:1.4em;height:120px;width:70%;
	text-align:center;
}
.rst-background {
	position:absolute;background-color:#fa4b49;bottom:15px;left:-50%;height:1000px;width:200%;
	border-radius:1000px;
	z-index:0;
}

.rst-mid-info {
	padding:0 2em;
	background:#f2f2f2;
	text-align:center;color:#999;
}
.have_rst_no_addr {
	line-height:60px;
}
.rst-mid-addr {
	
}
.rst-mid-addr .left {
	line-height:30px;text-align:left;
}
.rst-mid-addr .right {
	line-height:30px;text-align:right;
}
.rst-mid-addr .right .addr {
	background:url('__PUBLIC__/image/shop_icon_locktion.png') no-repeat left center;background-size:auto 100%;padding-left:20px;
}

.rst-mid {
	color:#999;float:left;
	padding:20px 10px;
	line-height:1.6em;
	font-size:1.2em;
	min-height:190px;width:100%;
	text-indent:2em;
}

.rst-close {
	position:absolute;right:-16px;top:-16px;width:50px;height:50px;z-index:5;
	background:url('__PUBLIC__/image/shop_btn_close.png') no-repeat center;
	background-size:100% auto;
}
.rst-foot {
	background:url('__PUBLIC__/image/shop_btn_sure_c.png') no-repeat center 8px;
	height:60px;
	background-size:auto 88%;
}

.midel-liu {
	display: box;
	display: -webkit-box;
	display: -moz-box; 
	-webkit-box-pack:center;
	-moz-box-pack:center;
	-webkit-box-align:center;
	-moz-box-align:center; 
}
.content img {max-width:100%;}
</style>
</head>
<body>
<div id="box">
	<div id="main">
		<div class="rst-info" id="have_rst">
			<div class="rst-panel">
				<div class="rst-main">
					<div class="rst-close" onclick="$(this).parent().parent().parent().hide();"></div>
					<div class="rst-head">
						<div class="rst-background"></div>
						<div class="rst-thing">
							<h4>恭喜你</h4>
							<div class="rst-img">&nbsp;</div>
							<div class="rst-gift midel-liu" id="have_rst_content"></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="rst-mid-info in-line" id="have_rst_addr" data-k="">
						<span class="have_rst_no_addr">配送至： 点击这里去填写收货信息&gt;&gt;</span>
						<div id="have_rst_addr" class="rst-mid-addr"><div class="left"></div><div class="right in-line"><span class="addr"></span></div></div>
					</div>
					<div class="rst-foot" id="have_rst_ok" data-k=""></div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="rst-info" id="not_have_rst">
			<div class="rst-panel">
				<div class="rst-main" style="background-color:#fff;line-height:0;">
					<div class="rst-close"  onclick="$(this).parent().parent().parent().hide();"></div>
					<div class="rst-head" style="padding:0;">
						<img src="__PUBLIC__/image/shop_bg_failed.jpg" width="100%"/>
						<div style="color:#666;font-size:1.4em;position:absolute;width:100%;line-height:50px;top:0;left:0;text-align:center;">很遗憾</div>
					</div>
					<div class="rst-mid midel-liu">
						<span id="not_have_rst_content"></span><br /><br />
						<span id="not_have_rst_movie" style="float:right"></span>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="content">
			<div class="item">
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<?php if(is_array($vo["img_list"])): $i = 0; $__LIST__ = $vo["img_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><div class="swip-img swiper-slide">
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
						<?php elseif(strtotime($vo['lottery']['end_time']) > NOW_TIME): ?>
							<div class="timer"><span class="js-timer" style="color:#999;" data-s="1" data-t="<?php echo strtotime($vo['lottery']['end_time']);?>"></span>  后结束</div>
						<?php else: ?>
							<div class="timer">已结束</div><?php endif; ?>
						<span class="price"><?php echo Intval($vo['price']);?> 金币 / 次</span>
					<div class="clear"></div>
				</div>
			</div>
			<div class="line"></div>
			<div class="detail">
				<?php echo ($vo["intro"]); ?>
			</div>
			<div class="foot-bar">
				<?php if(strtotime($vo['lottery']['start_time']) > NOW_TIME): ?><a id="go_lottery" href="javascript:void(0);" class="btn blue" data-t="<?php echo ($vo["lottery_times"]); ?>">
						抽奖（还未开始）
			 		</a>
				<?php elseif(strtotime($vo['lottery']['end_time']) > NOW_TIME): ?>
					<?php if($vo['ku_cun'] > 0): ?><a id="go_lottery" href="javascript:void(0);" class="btn blue" data-t="<?php echo ($vo["lottery_times"]); ?>">
							抽奖（还有<?php echo ($vo["lottery_times"]); ?>次机会）
					 	</a>
					<?php else: ?>
						<a id="go_no_more" href="javascript:void(0);" class="btn grery" data-t="<?php echo ($vo["lottery_times"]); ?>">
							抽奖（已抽完）
					 	</a><?php endif; ?>
				<?php else: ?>
					<a id="go_lottery_over" href="javascript:void(0);" class="btn grery" data-t="<?php echo ($vo["lottery_times"]); ?>">
						抽奖（已经结束）
				 	</a><?php endif; ?>
				
			</div>
			<div class="line"></div>
		</div>
	</div>
</div>
<script>
$('document').ready(function(){
	var w = 0, h = 0 ;
	var cha_time = <?php echo strtotime($vo['lottery']['start_time']) - NOW_TIME; ?>;
	var t = <?php echo NOW_TIME;?>;
	var userid=GetQueryString('userid');
	var k = GetQueryString('k');
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
				cha_time = parseInt(timer) - t;
				if (cha_time > 0) {
					$(this).html(sec2txt(cha_time));
				} else {
					clearInterval(timmer);
				}
				if (typeof ($(this).attr('data-s')) != 'undefined') {
					cha_time = -1;
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
	
	$('#have_rst_addr').click(function(){
		var id = 'loading_address_panel';
		if ($('#'+id).length == 0){
			$('body').append('<div id="'+id+'" style="top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,0.8);position:fixed;z-index:50;"></div>');
		}
		if ($('#'+id).html() == '') {
			$('#'+id).load("<?php echo U('Index/setAddr',array('userid'=>$_GET['userid']));?>");
		}
	});
	
	$('#have_rst_ok').click(function(){
		var addr_k = $('#have_rst_addr').attr('data-k');
		if (addr_k == '') {
			booAlert('请添加送货地址。'); return;
		}
		var succ_k = $(this).attr('data-k');
		if (succ_k == '') {
			booAlert('error'); return;
		}
		$.ajax({
			url : '<?php echo U("Index/bindAddr",array("userid"=>$_GET["userid"]));?>',
			type :'post',
			data :{userid:userid, k:k, addr_k:addr_k, succ_k:succ_k},
			success : function (data) {
				try {
					if (typeof data === 'string') {
						data = parseJSON(data);
					}
					if (data.rst == 0) {
						booAlert(data.msg);
						$('#have_rst').hide();
					} else {
						booAlert(data.msg);
					}
				} catch (ex) {
					booAlert('网络出错误了哦');
				}
			}, error : function(ex) {
				booAlert('进入方式错误，重新再进来一次吧！');
			}
		});
	});
	
	var loading = false;
	$('#go_lottery').click(function(){
		try {

			if(cha_time>0) {booAlert('抽奖还未开始');return;}
			if (loading) {booAlert('正在卖力加载中，请稍后。。。');return;}
			var yu = parseInt($(this).attr('data-t'));
			if (yu >0) {
				$(this).removeClass('blue').addClass('grery');
				loading = true;
				$.ajax({
					url:'<?php echo U("Index/lottery");?>',
					type:'post',
					data : {userid:userid, k:k},
					success : function(data){
						$('#go_lottery').addClass('blue').removeClass('grery');
						loading = false;
						try {
							if (typeof data === 'string') {
								data = parseJSON(data);
							}
							if (data.rst == 0) {
								var rst = data.data;
								$('#go_lottery').attr('data-t', rst.times);
								$('#go_lottery').html('抽奖（还有'+rst.times+'次机会）');
								if (rst.rst == 0) {
									$('#not_have_rst_content').html("“ "+rst.content+" ”");
									$('#not_have_rst_movie').html("—— 《"+rst.movie+"》");
									$('#not_have_rst').show();
								} else {
									$('#have_rst_content').html('抽中了： '+rst.content);
									$('#have_rst_ok').attr('data-k', rst.succ_k);
									if (rst.addr === '') {
										$('#have_rst_addr').html('<span class="have_rst_no_addr">配送至： 点击这里去填写收货信息&gt;&gt;</span>');
									} else {
										$('#have_rst_addr').attr('data-k', rst.addr.id);
										$('#have_rst_addr').html('<div id="have_rst_addr" class="rst-mid-addr"><div class="left">配送至：'+rst.addr.name+'&nbsp;&nbsp;'+rst.addr.phone+'</div><div class="right in-line"><span class="addr">'+rst.addr.addr+'</span></div></div>');
									}
									$('#have_rst').show();
								}
							} else {
								if (typeof data.data != 'undefined') {
									if (data.data.rst == 0) {
										booAlert(data.msg + '<div class="clear"></div><a id="go_uc_gold" href="javascript:void(0);" style="margin-top:1em;color:#00b9ff;font-size:0.8em;float:right;">查看任务详情</a><div class="clear"></div>');
										$('#go_uc_gold').click(function(){
											GMBase.openUrl ({url:'http://ser3.graphmovie.com/appweb/uc/goldList-x.php',upvc:1,t:'我的金币', s:1});
										});
									}
								} else {
									booAlert(data.msg);
								}
							}
						} catch (ex) {
							booAlert('网络出错误了哦');
						}
					},
					error : function() {
						booAlert('网络出错了哦。');
					}
				});
			} else {
				booAlert('机会已经用光了呢！去试试其他抽奖商品吧。');
			}
		} catch (ex) {
			booAlert('进入方式错误，重新再进来一次吧！');
		}
	});
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