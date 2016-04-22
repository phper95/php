<?php
// 电影模型
class TopicModel extends CommonModel {
	
	protected $trueTableName = 'topic'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('sub_title','require','副标题必须'),
		array('tags','require','类型必须')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('editor_note',''),
		array('author',''),
		array('actor',''),
		array('intro',''),
		array('showtime','2014'),
		array('zone','编基部'),
	);
	
}
?>