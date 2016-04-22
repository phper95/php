<?php
/**
 * 影片关系上映时间模型
 * Enter description here ...
 * @author Administrator
 *
 */
class MovieUShowtimeModel extends Model {
	protected $trueTableName = 'movie_v_showtime';
	 
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
		array('online_time','getTime',self::MODEL_BOTH,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

?>