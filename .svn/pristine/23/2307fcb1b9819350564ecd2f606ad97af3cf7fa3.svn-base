<?php
// 微图解频道关系模型
class WeiUChannelModel extends CommonModel {
	
	protected $trueTableName = 'wei_v_channel'; 
	
	public $_validate=array(
		array('title','require','名称不能为空'),
		array('desc','require','说明不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>