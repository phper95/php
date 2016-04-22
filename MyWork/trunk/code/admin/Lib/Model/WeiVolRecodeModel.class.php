<?php
// 微图解解说记录模型
class WeiVolRecodeModel extends CommonModel {
	
	protected $trueTableName = 'wei_vol_record'; 
	
	public $_validate=array(
		array('intro','require','介绍不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>