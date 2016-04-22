<?php
/**
 * 各个平台，渠道，4.8 首页穿插广告上线模型
 */
class Adv2OnlineMapXModel extends CommonModel {
	protected $trueTableName = 'adv2_online_map_x'; 
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
