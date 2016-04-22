<?php
// 首页上线配置
class MovieSeasonModel extends CommonModel {
	
	protected $trueTableName = 'movie_season'; 
	
	protected $_validate = array(
		array('name','','剧集已经存在！',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT), // 在新增的时候验证name字段是否唯一
	);
				
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>