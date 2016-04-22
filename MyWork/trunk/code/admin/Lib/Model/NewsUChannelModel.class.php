<?php
// 资讯频道关系模型
class NewsUChannelModel extends CommonModel {
	protected $trueTableName = 'paper_news_v_channel'; 
	
	public $_validate=array(
		array('paper_id','require','画报ID必须'),
		array('channel_id','require','频道ID必须'),
	);
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>