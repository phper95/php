<?php
/**
 * 用户作品结构模型
 * Enter description here ...
 * @author Administrator
 *
 */
class MemberWorkMapModel extends Model {
	protected $trueTableName = 'user_work_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('update_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

?>