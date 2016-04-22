<?php
/**
 * 上线渠道模型
 * Enter description here ...
 * @author Administrator
 *
 */
class ChannelModel extends Model {
	protected $trueTableName = 'pub_channel'; 
	
	public $_auto=array(
		array('add_time','getTime',self::MODEL_INSERT,'callback'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
}

?>