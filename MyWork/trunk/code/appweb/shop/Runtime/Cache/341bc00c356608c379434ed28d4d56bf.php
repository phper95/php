<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" >
<meta name="format-detection" content="telephone=no"/>
<title>大转盘抽奖</title>
<!--<link href="__CSS__/style.css?2" rel="stylesheet" type="text/css" media="screen"/>
<link href="__CSS__/swiper.min.css" rel="stylesheet" type="text/css" media="screen"/>-->
<script src="__JS__/jquery-1.10.1.min.js"></script>
<script src="__JS__/awardRotate.js"></script>
<!--<script src="../js/jquery-1.10.1.min.js"></script>-->
<!--<script src="__JS__/swiper.jquery.min.js"></script>-->
<script src="__JS__/lib.js?1"></script>
<script src="http://ser3.graphmovie.com/boo/js/gw2c.js"></script>
<style type="text/css">
	#main {font-size:0.8em;}
	body,ul,ol,li,p,h1,h2,h3,h4,h5,h6,form,fieldset,table,td,img,div{margin:0;padding:0;border:0;}
	body{color:#333; font-size:12px;font-family:"Microsoft YaHei"}
	ul,ol{list-style-type:none;}
	select,input,img,select{vertical-align:middle;}
	input{ font-size:12px;}
	a{ text-decoration:none; color:#000;}
	a:hover{color:#c00; text-decoration:none;}
	.clear{clear:both;}
	#box{
		width:100%;
		margin-top:60px;}
	/* 大转盘样式 */
	.banner{display:block;width:95%;margin-left:auto;margin-right:auto;margin-bottom: 20px;}
	.banner .turnplate{display:block;width:100%;position:relative;}
	.banner .turnplate canvas.item{width:100%;}
	.banner .turnplate img.pointer{position:absolute;width:31.5%;height:42.5%;left:34.6%;top:23%;}
</style>
<script type="text/javascript">
		var turnplate={
			restaraunts:[],				//大转盘奖品名称
			colors:[],					//大转盘奖品区块对应背景颜色
			outsideRadius:192,			//大转盘外圆的半径
			textRadius:155,				//大转盘奖品位置距离圆心的距离
			insideRadius:68,			//大转盘内圆的半径
			startAngle:0,				//开始角度
			imgWidth:64,				//奖品图片的宽度
			imgHeight:64,				//奖品图片的高度

			bRotate:false				//false:停止;ture:旋转
		};

		$(document).ready(function(){
			//动态添加大转盘的奖品与奖品区域背景颜色
			//获取转盘奖品
			var goods = <?php echo $jsonGoods; ?>;
			//console.log(goods[0].cat_id);
			turnplate.restaraunts = new Array;
			turnplate.images = new Array;
			$.each(goods,function(i){
				turnplate.restaraunts[i]=goods[i].name;
				turnplate.images[i]=goods[i].image;
			});
			//最后一个元素为‘谢谢参与’
			turnplate.restaraunts[goods.length]='谢谢参与';
			console.log(turnplate.restaraunts);
			/*$.ajax({
				type:"GET",
				url : '<?php echo U("Index/rotateLotteryGoods",array("userid"=>$_GET["userid"]));?>',
				async:false,
				success:function(res){
					alert(res);
				}

			});*/
			//turnplate.restaraunts = ["50M免费流量包", "10闪币", "谢谢参与", "5闪币", "10M免费流量包", "20M免费流量包", "20闪币 ", "30M免费流量包", "100M免费流量包", "2闪币"];
			turnplate.colors = ["#FFF4D6", "#FFFFFF", "#FFF4D6", "#FFFFFF","#FFF4D6", "#FFFFFF", "#FFF4D6", "#FFFFFF","#FFF4D6", "#FFFFFF"];


			var rotateTimeOut = function (){
				$('#wheelcanvas').rotate({
					angle:0,
					animateTo:2160,
					duration:8000,
					callback:function (){
						alert('网络超时，请检查您的网络设置！');
					}
				});
			};

			//旋转转盘 item:奖品位置; txt：提示语;
			var rotateFn = function (item, txt){
				var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
				if(angles<270){
					angles = 270 - angles;
				}else{
					angles = 360 - angles + 270;
				}
				$('#wheelcanvas').stopRotate();
				$('#wheelcanvas').rotate({
					angle:0,
					animateTo:angles+1800,
					duration:8000,
					callback:function (){
						//alert(txt);
						//转动结束提示
						turnplate.bRotate = !turnplate.bRotate;
					}
				});
			};

			$('.pointer').click(function (){
				if(turnplate.bRotate)return;
				turnplate.bRotate = !turnplate.bRotate;
				//获取随机数(奖品个数范围内)
				//var item = rnd(1,turnplate.restaraunts.length);
				//发起抽奖请求
				$.ajax({
					type:"GET",
					async:false,
					url : '<?php echo U("Index/doRotateLottery",array("userid"=>$_GET["userid"]));?>',
					success:function(res){
					if(res.data.rst == 1){
						item=res.data.lottery_goods_index+1;
					}else{
					    item=goods.length+1;
					}

					}

				});
				alert(item);
				//var item=3;
				//alert(item)
				//奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
				rotateFn(item, turnplate.restaraunts[item-1]);
				/* switch (item) {
				 case 1:
				 rotateFn(252, turnplate.restaraunts[0]);
				 break;
				 case 2:
				 rotateFn(216, turnplate.restaraunts[1]);
				 break;
				 case 3:
				 rotateFn(180, turnplate.restaraunts[2]);
				 break;
				 case 4:
				 rotateFn(144, turnplate.restaraunts[3]);
				 break;
				 case 5:
				 rotateFn(108, turnplate.restaraunts[4]);
				 break;
				 case 6:
				 rotateFn(72, turnplate.restaraunts[5]);
				 break;
				 case 7:
				 rotateFn(36, turnplate.restaraunts[6]);
				 break;
				 case 8:
				 rotateFn(360, turnplate.restaraunts[7]);
				 break;
				 case 9:
				 rotateFn(324, turnplate.restaraunts[8]);
				 break;
				 case 10:
				 rotateFn(288, turnplate.restaraunts[9]);
				 break;
				 } */
				//console.log(item);
			});
		});

		function rnd(n, m){
			var random = Math.floor(Math.random()*(m-n+1)+n);
			return random;

		}


		//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
		window.onload=function(){
			drawRouletteWheel();
		};

		function drawRouletteWheel() {
			var canvas = document.getElementById("wheelcanvas");
			if (canvas.getContext) {
				//根据奖品个数计算圆周角度
				var arc = Math.PI / (turnplate.restaraunts.length/2);
				var ctx = canvas.getContext("2d");
				//在给定矩形内清空一个矩形
				ctx.clearRect(0,0,422,422);
				//strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式
				ctx.strokeStyle = "#FFBE04";
				//font 属性设置或返回画布上文本内容的当前字体属性
				ctx.font = '16px Microsoft YaHei';
				for(var i = 0; i < turnplate.restaraunts.length; i++) {
					var angle = turnplate.startAngle + i * arc;
					ctx.fillStyle = turnplate.colors[i];
					ctx.beginPath();
					//arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）
					ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);
					ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
					ctx.stroke();
					ctx.fill();
					//锁画布(为了保存之前的画布状态)
					ctx.save();

					//----绘制奖品开始----
					ctx.fillStyle = "#E5302F";
					var text = turnplate.restaraunts[i];
					var line_height = 17;
					//translate方法重新映射画布上的 (0,0) 位置
					ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

					//rotate方法旋转当前的绘图
					ctx.rotate(angle + arc / 2 + Math.PI / 2);

					/** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
					if(text.indexOf("M")>0){
						//流量包
						var texts = text.split("M");
						for(var j = 0; j<texts.length; j++){
							ctx.font = j == 0?'bold 20px Microsoft YaHei':'16px Microsoft YaHei';
							if(j == 0){
								ctx.fillText(texts[j]+"M", -ctx.measureText(texts[j]+"M").width / 2, j * line_height);
							}else{
								ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
							}
						}
					}else if(text.length>6){
						//奖品名称长度超过一定范围
						text = text.substring(0,6)+"||"+text.substring(6);
						var texts = text.split("||");
						for(var j = 0; j<texts.length; j++){
							ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
						}
					}else{
						//在画布上绘制填色的文本。文本的默认颜色是黑色
						//measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
						ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
					}

					//添加对应图标
					//alert(text.length)
					if(i<text.length){
						var img= document.getElementById("img_"+i);
						//var img= document.getElementById("shan-img");
						img.onload=function(){
							ctx.drawImage(img,-15,10,turnplate.imgWidth,turnplate.imgHeight);
						};
						ctx.drawImage(img,-15,10,turnplate.imgWidth,turnplate.imgHeight);
					}else if(text.indexOf("谢谢参与")>=0){
						var img= document.getElementById("sorry-img");
						img.onload=function(){
							ctx.drawImage(img,-15,10);
						};
						ctx.drawImage(img,-15,10);
					}
					//把当前画布返回（调整）到上一个save()状态之前
					ctx.restore();
					//----绘制奖品结束----
				}
			}
		}


</script>

</head>
<body>
<div id="box">
	<img src="__PUBLIC__/image/1.png" id="shan-img" style="display:none;" />
	<img src="__PUBLIC__/image/2.png" id="sorry-img" style="display:none;" />
	<?php if(is_array($goods)): $i = 0; $__LIST__ = $goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><img src="<?php echo ($vo["image"]); ?>" data-id="<?php echo ($vo["id"]); ?>" id="img_<?php echo ($i-1); ?>" title="<?php echo ($vo["name"]); ?>" style="width: 64px;height: 64px;display:none;" /><?php endforeach; endif; else: echo "" ;endif; ?>
	<div class="banner">
		<div class="turnplate" style="background-image:url(__PUBLIC__/image/turnplate-bg.png);background-size:100% 100%;">
			<canvas class="item" id="wheelcanvas" width="422px" height="422px"></canvas>
			<img class="pointer" src="__PUBLIC__/image/turnplate-pointer.png"/>
		</div>
	</div>
</div>
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