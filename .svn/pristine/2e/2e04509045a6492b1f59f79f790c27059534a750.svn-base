<?php
// 商品抽奖模型
class ShopLotteryModel extends CommonModel {
	
	protected $trueTableName = 'shop_lottery'; 
	
	public $_validate=array(
		array('g_id','require','商品ID不能为空'),
		array('min_num','number','中奖率格式'),
		array('max_num','number','中奖率格式'),
	);
	
	protected function getLv(){
		return I('min_num').'/'.I('max_num');
	}
	public $_auto=array(
		array('lv','getLv',self::MODEL_BOTH,'callback'),
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>