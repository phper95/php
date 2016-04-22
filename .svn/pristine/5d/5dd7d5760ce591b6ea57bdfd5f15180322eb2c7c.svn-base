<?php
// 电影模型
class AdvhtModel extends CommonModel {
	
	protected $trueTableName = 'adv_ht'; 
	
	public $_validate=array(
		array('name','require','名称必须')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>