<?php
// 活动模型
class ActiveModel extends CommonModel {
	
	protected $trueTableName = 'activ'; 
	
	public $_validate=array(
		array('name','require','名称不能为空'),
		array('cat_id','require','类型不能为空'),
		array('start_time','require','开始时间不能为空'),
		array('end_time','require','结束时间不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>