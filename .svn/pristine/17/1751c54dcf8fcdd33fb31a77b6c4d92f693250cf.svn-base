<?php
/**
 * 推送通知记录表
 * Enter description here ...
 * @author Administrator
 *
 */
class PushNoticeModel extends Model {
	protected $trueTableName = 'push_notice'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

?>