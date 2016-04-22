<?php
/**
 * 电影关系用户模型
 * Enter description here ...
 * @author Administrator
 *
 */
class BlackRoomModel extends Model {
	protected $trueTableName = 'user_tinyblackroom'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback')
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

