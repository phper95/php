<?php
// 专题评论举报记录模型
class CommentTopicBkModel extends Model {
	protected $trueTableName = 'user_v_comment_topic_bk';
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
	
 	public function getNowTime(){
    	return date("Y-m-d H:i:s", NOW_TIME);
    }
}
?>