<?php
$config = array(
	//'配置项'=>'配置值'
	'URL_MODEL' => 0,
	'APP_STATUS' => 'debug', //应用调试模式状态
	'SHOW_PAGE_TRACE' =>true, // 显示页面Trace信息

	'SESSION_AUTO_START'=>true,
    'USER_AUTH_ON'      =>true,
    'USER_AUTH_TYPE'    =>1,	// 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY'     =>'authId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'    =>'administrator',
    'USER_AUTH_MODEL'   =>'User',	// 默认验证数据表模型
    'AUTH_PWD_ENCODER'  =>'md5',	// 用户认证密码加密方式
    'USER_AUTH_GATEWAY' =>'Public/login',// 默认认证网关
    'NOT_AUTH_MODULE'   =>'Public',	// 默认无需认证模块
    'GUEST_AUTH_ON'     =>false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'     =>0,        // 游客的用户ID
    'APP_AUTOLOAD_PATH'=>'@.TagLib',
//    'TMPL_ACTION_ERROR' => 'Public:error',      //默认错误跳转对应的模板文件  
//    'TMPL_ACTION_SUCCESS' => 'Public:success',  //默认成功跳转对应的模板文件

	
    'DEFAULT_THEME'  => 'default',
    'TMPL_DETECT_THEME' => true, // 自动侦测模板主题

	'LOAD_EXT_CONFIG' => 'db,menu,testUsers,api,fee,ckimg', // 加载扩展配置文件
	"LOAD_EXT_FILE"=>"caesar" , // 加载自定义函数库
	'VAR_PAGE' => 'p', // 分页参数
	'DEFAULT_AJAX_RETURN' => 'JSON', // 默认Ajax返回参数格式

);

return array_merge($config,
	array(
		'PAGE_THEME' => array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'首页','last'=>'末页','theme'=>'共 %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%'),
		'TMPL_PARSE_STRING'  =>array(
		     '__PUBLIC__' => __ROOT__.'/'.APP_NAME.'/Tpl/'.$config['DEFAULT_THEME'].'/Public', // 更改默认的__PUBLIC__ 替换规则
		     '__JS__' => __ROOT__.'/'.APP_NAME.'/Tpl/'.$config['DEFAULT_THEME'].'/Public/js', // 增加新的JS类库路径替换规则
			 '__CSS__' => __ROOT__.'/'.APP_NAME.'/Tpl/'.$config['DEFAULT_THEME'].'/Public/css', // 增加新的JS类库路径替换规则
		     '__UPLOAD__' =>__ROOT__.'/Uploads', // 增加新的上传路径替换规则
		)
	)
);

?>