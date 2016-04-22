<?php
$config = array(
	//'配置项'=>'配置值'
	'URL_MODEL' => 0,
	'APP_STATUS' => 'debug', //应用调试模式状态
	'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息

	'SESSION_AUTO_START'=>true,
	'SESSION_AUTH_KEY' => 'GM_SHOP_USER_ID',
    'AUTH_PWD_ENCODER'  =>'md5',	// 用户认证密码加密方式
//    'TMPL_ACTION_ERROR' => 'Public:error',      //默认错误跳转对应的模板文件  
//    'TMPL_ACTION_SUCCESS' => 'Public:success',  //默认成功跳转对应的模板文件

	'DEFAULT_ERROR_URL' => 'http://www.graphmovie.com', // 非法访问的默认跳转URL
	
	
	'WISDOM_COUNT' => 5040,
	'WISDOM_DEFAULT_TITLE' => '图解电影',
	'WISDOM_DEFAULT_CONTENT' => '我们就是我们的故事',
    'DEFAULT_THEME'  => 'default',
	'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
	"LOAD_EXT_FILE"=>"caesar" , // 加载自定义函数库

	'LOAD_EXT_CONFIG' => 'db', // 加载扩展配置文件
	'DEFAULT_AJAX_RETURN' => 'JSON', // 默认Ajax返回参数格式
	
	'ORDER_LIST_STATE' => array(
		0 => '无收货地址',
		1 => '待发货',
		2 => '已发货',
		3 => '已处理'	
	),

	//奖品类型
	'GOODS_CATEGORY' => array(
		1 => '直接抽奖',
		2 => '转盘抽奖',
		3 => '奖品兑换'
	),
	//虚拟库存下限值，随机获取的机器人数量
	'VARTUAL_LIMIT'=>5,
);

return array_merge($config,
	array(
		'TMPL_PARSE_STRING'  =>array(
		     '__PUBLIC__' => __ROOT__.'/'.APP_NAME.'/Tpl/'.$config['DEFAULT_THEME'].'/Public', // 更改默认的__PUBLIC__ 替换规则
		     '__JS__' => __ROOT__.'/'.APP_NAME.'/Tpl/'.$config['DEFAULT_THEME'].'/Public/js', // 增加新的JS类库路径替换规则
			 '__CSS__' => __ROOT__.'/'.APP_NAME.'/Tpl/'.$config['DEFAULT_THEME'].'/Public/css', // 增加新的JS类库路径替换规则
		)
	)
);

?>