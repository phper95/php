<?php
// 首页上线表
class JianMapModel extends CommonModel {
	//1电影，2新番，3微图解，4画报，5专辑，6每日一图
	static public $TYPE_MOVIE = 1;
	static public $TYPE_FAN = 2;
	static public $TYPE_WEI = 3;
	static public $TYPE_PAPER = 4;
	static public $TYPE_TOPIC = 5;
	static public $TYPE_IMAGE = 6;
	
	protected $trueTableName = 'jian_online_map'; 
	
	public $_validate=array(
		array('online_id','require','上线ID不能为空'),
		array('online_type','require','上线类型不能为空'),
		array('online_time','require','上线时间不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>