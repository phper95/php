<?php
/**
 * 各个类型，区域，上线时间，是否精品 上线关系模型
 */
class OnlineMapTagsModel extends Model {
	protected $trueTableName = 'movie_tags_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('update_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
	
}
