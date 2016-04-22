<?php
// 电影弹幕模型
class PoptxtMovieBkModel extends Model {
	protected $trueTableName = 'user_v_poptxt_movie_bk';
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
	
 	public function getNowTime(){
    	return date("Y-m-d H:i:s", NOW_TIME);
    }
}
?>