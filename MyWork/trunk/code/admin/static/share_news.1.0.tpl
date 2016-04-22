<!DOCTYPE HTML> 
<html>
<head>
<title>{$title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/> 
<link rel="stylesheet" href="css/style.css">  
<script src="../js/jquery.min.js"></script>
<script src="index.js"></script>
<script src="setting.js"></script>
<script src="srolloading.js"></script>
<script>
	document.write("<s"+"cript type='text/javascript' src='opt.js?"+((new Date()).valueOf())+"'></scr"+"ipt>");
</script>
<script>
$('document').ready(function(){
	readed("{$id}",'share');
	loadComment("{$id}",'share');
});
</script>
</head>
<body class="day">
<div class="content day">
	<div id="head_img">
		<img src="{$bpic}" /> 
	</div>
	<div class="widget">
		<div class="whead">
			<h6>{$news_title}</h6>
			<div class="line clear"></div>
			<div class="grapher">
				<img class="avatar" src="{$grapher_avatar}" />
				<div class="info">{$grapher_name} <span></span></div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="body">
			{$content}
		</div>
		<br />
		<div class="footer">
		</div>
	</div>
</div>
<div>
</div>
<script>
$(".img-scorll-load").scrollLoading();
</script>
</body>
</html>
