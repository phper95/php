var gw2c = gw2c || {};
gw2c.config = {
		'1':{'name':'打开资讯','np':{'nid':'资讯ID'}},
		'2':{'name':'打开电影','np':{'mid':'图解ID'}},
		'3':{'name':'打开广告','np':{'aid':'广告ID'}},
		'4':{'name':'打开专题','np':{'tid':'专题ID'}},
		'61':{'name':'打开微图解','np':{'wid':'微图解ID'}},
		'5':{'name':'打开个人中心','np':{'uid':'用户秘号'}},
		'11':{
			'name':'打开URL',
			'np':{
				't':'网页标题',
				'u':'链接地址',
				'upvc':{'str':'是否带参数','d':{'1':'是','0':'否'}},
				's':{'str':'打开方式','d':{'0':'全屏','1':'只要标题','2':'只要控制栏','3':'标题控制栏全要'}}
			}
		},
		'64':{'name':'调用系统浏览器打开连接','np':{'url':'连接地址'}},
		'65':{'name':'打开今日茶点','np':{'t':'标题'}},
};
gw2c.getpanel = function(id){
	id = typeof id=='undefined' ? 'xxsdf' : id;
	var config = this.config;
	var option = [];
	var inputs = [];
	for (var a in config) {
		var one = config[a];
		inputs[a] = [];
		option.push('<option value="'+a+'">'+one.name+'</option>');
		if (one.np) {
			for (var k in one.np) {
				var k_one = one.np[k];
				if (typeof k_one != 'object'){
					inputs[a].push('<div class="formRow"><div class="grid2"><label>'+k_one+':</label></div><div class="grid9"><input type="text" id="'+a+'_'+k+'" /></div><div class="clear"></div></div>'); 
				} else {
					var input = '<div class="formRow"><div class="grid2"><label>'+k_one.str+':</label></div><div class="grid9"><select id="'+a+'_'+k+'">';
					for (var t in k_one.d) {
						input += '<option value="'+t+'">'+k_one.d[t]+'</option>';
					}
					input += '</select></div><div class="clear"></div></div>';
					inputs[a].push(input);
				}
			}
		}
	}
	
	var html = '<div class="formRow">'
			 + '<div class="grid2"><label>选择操作</label></div><div class="grid9"><select id="gw2c_script_a"><option value="">-选择后续操作-</option>'+(option.join(''))+'</select></div><div class="clear"></div></div>'
			 + '<div id="gw2c_script_option"></div>'
			 + '<div id="gw2c_loading_info" style="text-align:center;display:none;padding:10px;"><img alt="" style="margin: 12px 0 0 10px;" src="__PUBLIC__/images/elements/loaders/7s.gif"> 正在加载。。。。。</div>';
	$('#'+id).html(html);
	$('#gw2c_script_a').change(function(){
		if ($(this).val() == '') {
			$('#gw2c_script_option').html('');
		} else {
			$('#gw2c_script_option').html(inputs[$(this).val()].join(''));
		}
	});
};

gw2c.getScript = function(){
	var a = $('#gw2c_script_a').val();
	var needp = this.config[a].np;
	var str = '{"a":"'+a+'","v":"1","p":{';
	var ge = '';
	for (var id in needp) {
		var val = $.trim($('#'+a+'_'+id).val());
		if (val == '') {alert(needp[id]+' 不能为空');return null;}
		if (id == 't') {
			val = gw2c.UrlEncode(val);
		}
		str += ge + '"'+id+'":"'+val+'"';
		ge = ',';
	}
	str += '}}';
	return str;
};

gw2c.UrlEncode = function(str){
	return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}; 