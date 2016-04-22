<?php
// 微图解模型
class WeiModel extends CommonModel {
	
	protected $trueTableName = 'wei'; 
	
	public $_validate=array(
		array('title','require','名称不能为空'),
		array('intro','require','介绍不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>