<?php
/**
 * 各个平台，渠道，电影上线模型
 */
class OnlineMapPCModel extends Model {
	protected $trueTableName = 'movie_online_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('update_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}
