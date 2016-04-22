<!DOCTYPE HTML> 
<html>
<head>
<title>{$title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/> 
<link rel="stylesheet" href="css/style.2.css?93">  
<script src="../js/jquery.min.js"></script>
<script src="index.js"></script>
<script src="setting.2.js?4"></script>
<script src="srolloading.js"></script>
<script src="prefixfree.min.js"></script>
<script>
	document.write("<s"+"cript type='text/javascript' src='opt.2.js?"+((new Date()).valueOf())+"'></scr"+"ipt>");
</script>
<script>
$('document').ready(function(){
	readed("{$id}",'read');
	loadComment("{$id}",'read');
});
</script>
</head>
<body class="day">
<div class="content day" id="box">
	<div id="head_img">
		<img src="{$bpic}" />
		<div id="head-title-box">
			<div id="head-title-info">
				<h4>{$news_title}</h4>
				<div id="head-title-sbtitle">{$news_sub_title}</div>
			</div>
		</div>
	</div>
	<div class="widget">
		<div class="whead">
			<div style="float:right;color:#999;">{$news_online_time}</div>
			<div class="clear"></div>
			<hr />
			<div class="grapher">
				<img class="avatar" src="{$grapher_avatar}" />
				<div class="info"><span>{$grapher_name}</span> </div>
				<a href='gw2c://{"v":"1","a":"5","p":{"uid":"{$grapher_id}"}}' class="title_add">关注</a>
				<div class="clear"></div>
			</div>
			<hr />
			<div class="clear"></div>
		</div>
		<div class="summary" style="">“ {$news_summary} ”</div>
		<div class="clear"></div>
		<div class="body">
			{$content}
			<div id="comments"></div>
			<div id="readmore"></div>
		</div>
	</div>
	<div class="fix-comment-box" id="fix-comment-box">
		<div class="fix-comment">
			<ul class="fix-comment-list" id='fix-comment-list'>
			</ul>
		</div>
	</div>
	<div id="footer-box">
		<div id="footer">
		</div>
	</div>
</div>
<div>
</div>
<script>
$('document').ready(function(){
	$(".img-scorll-load").scrollLoading();
	var scroll_flag = true;
	$(window).scroll(function() {
		var scrollTop = $(this).scrollTop();
		if (scrollTop > 0) {
			if (scroll_flag){
				scroll_flag = false;
				$('#news_setting').addClass('setting-scroll');
			}
		} else {
			scroll_flag = true;
			$('#news_setting').removeClass('setting-scroll');
		}
	});
});
</script>
</body>
</html>
