<?php
// 活动类型模型
class ActiveCategoryModel extends CommonModel {
	
	protected $trueTableName = 'activ_category'; 
	
	public $_validate=array(
		array('name','require','名称不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>