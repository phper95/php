<?php
// 商品用户抽中订单模型
class ShopMemberLotterySuccModel extends CommonModel {
	static $STATE_NO_ADDR = 0;
	static $STATE_WAITE_SEND = 1;
	static $STATE_SENDING = 2;
	static $STATE_ENDING = 3;
	static $STATE_TXT = array(
		0 => '未填写收货信息',
		1 => '未发货',
		2 => '已经发货',
		3 => '已处理'		
	);
	
	protected $trueTableName = 'shop_user_lottery_succ'; 
	
	public $_validate=array(
	);
}
?>