<?php
// 微图解电影关系模型
class WeiUFilmModel extends CommonModel {
	
	protected $trueTableName = 'wei_v_film'; 
	
	public $_validate=array(
		array('wei_id','require','微图解ID不能为空'),
		array('film_id','require','电影ID不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>