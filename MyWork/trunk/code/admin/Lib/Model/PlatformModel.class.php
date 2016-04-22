<?php
/**
 * 上线平台模型
 * Enter description here ...
 * @author Administrator
 *
 */
class PlatformModel extends Model {
	protected $trueTableName = 'pub_platform';
	 
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

?>