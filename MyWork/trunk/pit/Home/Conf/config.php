<?php
return array(
	//'配置项'=>'配置值'
	'EMAIL_KEY' => '123#@!123', // email 加密Key
	'USER_ID_KEY' => '123#@!123', // user_id 加密Key
	'RGST_NAME' => '肉坑の匿名君', // 默认注册用户名
	
	// session 配置
	'SESSION_PREFIX' => 'web_pit', // session 前缀

	// 错误模板
	'TMPL_ACTION_ERROR' => 'Public/error',
	'TMPL_ACTION_SUCCESS' => 'Public/succ',

	// URL模式大小写
	'URL_CASE_INSENSITIVE' => false,

	'URL_MODEL' => 0,
	'DEFAULT_V_LAYER' =>  'View/default',
//	'SHOW_PAGE_TRACE' => true, 
	'LOAD_EXT_CONFIG' => 'db',
//	'URL_ROUTER_ON'   => false, 
	'TMPL_PARSE_STRING'  =>array(     
		'__PUBLIC__' => __ROOT__.'/Home/View/default/Public', // 更改默认的/Public 替换规则     
		'__JS__'     => __ROOT__.'/Home/View/default/Public/js', // 增加新的JS类库路径替换规则     
		'__CSS__' => __ROOT__.'/Home/View/default/Public/css', // 增加新的上传路径替换规则
		'__IMG__' => __ROOT__.'/Home/View/default/Public/img', // 增加新的上传路径替换规则
		'__PITIMG__' => '/boo/Uploads',// 坑图片路径前缀
	),
	
	
	// 起名过滤字段
	'NOT_ALLOW_NAME' => array('图解电影','GraphMovie','graphmovie','Graphmovie','Graph Movie','graph movie','Graph movie','官方','鞭基部','编辑部','研发部','盐发部','创作团','鞭基','盐发'),
	
	// 错误代码
	'BUG_B_CODE' => array(
		'TOO_MANY_USER' => '0x0001', // 数据库存在两个相同的email
		'INSERT_PIT_USER' => '0x1001', // 插入pit_user 出错
		'UPDATE_USER_V_PIT' => '0x2001', // 修改pit_user 出错
	),
	
	// 用户默认数据设置
	'ALLOW_USER_PIT_NUM' => 1, // 默认用户允许占坑数量
	
	'PIT_STATE' => array( // 坑状态
		'APPLY' => 0, // 申请中
		'PITTIN' => 1, // 正在填坑
		'APPLY_UNDO' => 2, // 申请弃坑
	
		'APPLY_FAIL' => 10, // 申请失败
		'APPLY_UNDO_SUCC' => 11, // 弃坑成功
		'APPLY_UNDO_FAIL' => 12, // 弃坑失败
		'DEADLINE' => 13, // 过期未处理
	
		'SUCC' => 20 // 如期交稿成功 
	),
	
	'BANK_LIST' => array(  // 支持的银行列表
		'中国工商银行','中国农业银行','中国银行','中国建设银行','交通银行'
		,'兴业银行','招商银行','广东发展银行','中国民生银行','中信银行'
		,'华夏银行','中国光大银行','广州商行','上海浦东发展银行 ','平安银行','东亚银行'
	)
	
	
);