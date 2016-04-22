<?php
/**
 * 各个平台，渠道，4.0广告上线模型
 */
class Adv2OnlineMap4Model extends CommonModel {
	protected $trueTableName = 'adv2_online_map_4'; 
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
