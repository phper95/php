<?php
// 打赏表
class RewardMapModel extends CommonModel {
	
	protected $trueTableName = 'reward_map'; 
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('update_time','getNowTime',self::MODEL_BOTH,'callback'),
	);
}
?>