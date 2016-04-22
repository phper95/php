<?php
// 首页弹窗配置上线
class HomeDialogOnlineMapModel extends CommonModel {
	
	protected $trueTableName = 'home_dialog_online_map'; 
	
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback')
	);
}
?>