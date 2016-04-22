<?php
// 首页上线表
class HomeOnlineMapModel extends CommonModel {
	
	protected $trueTableName = 'home_online_map'; 
	
	public $_validate=array(
		array('pub_platform','require','平台不能为空'),
		array('pub_channel','require','渠道不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>