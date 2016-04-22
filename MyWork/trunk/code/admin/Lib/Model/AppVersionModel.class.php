<?php
// APP版本模型
class AppVersionModel extends CommonModel {
	
	protected $trueTableName = 'gm_app_version'; 
	
	public $_validate=array(
		array('pub_platform','require','平台必须'),
		array('pub_channel','require','渠道必须'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>