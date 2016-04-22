<?php
// 资讯模型
class NewsModel extends CommonModel {
	protected $trueTableName = 'paper_news'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('sub_title','require','副标题必须'),
		array('summary','require','摘要必须'),
		array('html_content','require','资讯内容必须'),
		array('online_time','require','上线时间必须'),
	);
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>