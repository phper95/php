<?php
// 电影置顶模型
class MovieExposureModel extends CommonModel {
	
	protected $trueTableName = 'movie_exposure'; 
	
	public $_validate=array(
		array('user_id','require','用户不能为空'),
		array('movie_id','require','电影不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('add_time','getNowTime',self::MODEL_BOTH,'callback'),
	);
}
?>