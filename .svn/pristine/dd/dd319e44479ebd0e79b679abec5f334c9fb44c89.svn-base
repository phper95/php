<?php
/**
 * 用户通知模型
 * Enter description here ...
 * @author Administrator
 *
 */
class MemberNewModel extends Model {
	protected $trueTableName = 'user_v_new'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

?>