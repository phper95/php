<?php
// 电影模型
class AdvModel extends CommonModel {
	
	protected $trueTableName = 'adv'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('sub_title','require','副标题必须'),
		array('editor_note','require','编者按必须'),
		array('company','require','发行公司必须'),
		array('intro','require','简介必须'),
	);
	
	public $_auto=array(
//		array('tags','getTags',self::MODEL_BOTH, 'callback'),
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>