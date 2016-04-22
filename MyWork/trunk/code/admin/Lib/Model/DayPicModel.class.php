<?php
// 每日一图模型
class DayPicModel extends CommonModel {
	protected $trueTableName = 'paper_daypic';
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('img_url','require','图片必须'),
		array('online_time','require','上线时间必须')
	);
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>