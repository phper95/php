<?php
/**
 * 各个评级电影的map
 */
class MovieLevelMapModel extends Model {
	protected $trueTableName = 'movie_level_map'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}
