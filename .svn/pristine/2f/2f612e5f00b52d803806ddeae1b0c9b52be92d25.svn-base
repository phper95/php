<?php
// 资讯频道模型
class NewsChannelModel extends CommonModel {
	protected $trueTableName = 'paper_news_channel'; 
	
	public $_validate=array(
		array('name','require','名称必须')
	);
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>