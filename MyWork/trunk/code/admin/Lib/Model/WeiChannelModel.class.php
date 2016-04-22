<?php
// 微图解频道模型
class WeiChannelModel extends CommonModel {
	
	protected $trueTableName = 'wei_channel'; 
	
	public $_validate=array(
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>