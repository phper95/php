<?php
// 电影模型
class TopicUMovieModel extends CommonModel {
	
	protected $trueTableName = 'topic_v_movie'; 
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback')
	);
	
}
?>