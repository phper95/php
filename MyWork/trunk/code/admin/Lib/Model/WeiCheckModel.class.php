<?php
// 微图解审核模型
class WeiCheckModel extends CommonModel {
	
	protected $trueTableName = 'wei_admin_check'; 
	
	public $_validate=array(
		array('admin_id','require','管理员ID为空'),
		array('wei_id','require','微图解ID为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>