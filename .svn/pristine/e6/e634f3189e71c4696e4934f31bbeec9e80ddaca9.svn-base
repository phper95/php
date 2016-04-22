<?php
// 首页上线4
class HomeOnline4Model extends CommonModel {
	
	protected $trueTableName = 'home_online_4'; 
	
	public $_validate=array(
		array('script','require','脚本不能为空'),
		array('name','require','名称不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>