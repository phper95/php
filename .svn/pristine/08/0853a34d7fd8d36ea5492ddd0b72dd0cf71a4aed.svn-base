<?php
/**
 * 各个平台，渠道，片头片尾广告上线模型
 */
class AdvhtOnlineMapModel extends Model {
	protected $trueTableName = 'adv_ht_online_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('update_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}
