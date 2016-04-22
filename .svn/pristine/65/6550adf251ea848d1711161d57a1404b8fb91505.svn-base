<?php
/**
 * 各个平台，渠道，专题上线模型
 */
class OnlineMapTopicModel extends Model {
	protected $trueTableName = 'topic_online_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('update_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}
