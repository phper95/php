<?php
// 微图解解说用户模型
class WeiVolMemberModel extends CommonModel {
	
	protected $trueTableName = 'wei_vol_user'; 
	
	public $_validate=array(
		array('user_id','require','用户ID不能为空'),
		array('vol_id','require','解说ID不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>