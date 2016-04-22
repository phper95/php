<?php
// 广告评论举报记录模型
class CommentMovieBkModel extends Model {
	protected $trueTableName = 'user_v_comment_movie_bk';
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
	
 	public function getNowTime(){
    	return date("Y-m-d H:i:s", NOW_TIME);
    }
}
?>