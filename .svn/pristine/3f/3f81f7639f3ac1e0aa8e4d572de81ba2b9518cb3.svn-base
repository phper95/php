<?php
// 广告弹幕模型
class PoptxtAdvBkModel extends Model {
	protected $trueTableName = 'user_v_poptxt_adv_bk';
	 
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
	
 	public function getNowTime(){
    	return date("Y-m-d H:i:s", NOW_TIME);
    }
}
?>