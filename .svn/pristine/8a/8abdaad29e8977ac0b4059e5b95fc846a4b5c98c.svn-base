<?php
// 微图解官方图册模型
class WeiMaterialModel extends CommonModel {
	
	protected $trueTableName = 'wei_material'; 
	
	public $_validate=array(
		array('name','require','名称不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>