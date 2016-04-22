<?php
// 首页上线配置
class HomeOnlineConfModel extends CommonModel {
	
	protected $trueTableName = 'home_online_config'; 
	
	public $_validate=array(
		array('online_id','require','上线电影不能为空'),
		array('online_type','require','类型不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>