<?php
// 电影模型
class MovieModel extends CommonModel {
	
	protected $trueTableName = 'movie'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('sub_title','require','副标题必须'),
		array('editor_note','require','编者按必须'),
		array('author','require','影片导演必须'),
		array('actor','require','演员必须'),
		array('intro','require','简介必须'),
		array('showtime','require','影片上映日期必须'),
		array('zone','require','上映地区必须'),
	);
	
	public $_auto=array(
//		array('tags','getTags',self::MODEL_BOTH, 'callback'),
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
	
	protected function getTags(){
		$arr = I('tagsArr');
    	$tags = empty($arr) ? null : implode('|', $arr);
    	return $tags;
	}
	
}
?>