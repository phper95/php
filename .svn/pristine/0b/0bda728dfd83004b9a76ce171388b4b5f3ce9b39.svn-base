<?php
// 区域广告模型
class Adv2Model extends CommonModel {
	
	protected $trueTableName = 'adv2'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('area','require','广告区域必须')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>