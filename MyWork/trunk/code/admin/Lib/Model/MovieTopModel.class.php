<?php
// 电影置顶模型
class MovieTopModel extends CommonModel {
	
	protected $trueTableName = 'movie_top'; 
	
	public $_validate=array(
		array('user_id','require','用户不能为空'),
		array('movie_id','require','电影不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>