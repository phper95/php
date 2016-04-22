<?php
// 活动奖励模型
class ActiveRewardMemberModel extends CommonModel {
	
	protected $trueTableName = 'activ_reward_user'; 
	
	public $_validate=array(
		array('activ_id','require','活动ID不能为空'),
		array('reward_type','require','奖励类型不能为空'),
		array('user_id','require','用户ID不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>