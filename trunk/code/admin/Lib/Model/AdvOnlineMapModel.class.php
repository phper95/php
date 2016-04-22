<?php
/**
 * 各个平台，渠道，广告上线模型
 */
class AdvOnlineMapModel extends Model {
	protected $trueTableName = 'adv_online_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('update_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}
