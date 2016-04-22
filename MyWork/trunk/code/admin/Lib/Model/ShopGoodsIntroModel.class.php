<?php
// 商品详情模型
class ShopGoodsIntroModel extends CommonModel {
	
	protected $trueTableName = 'shop_goods_intro'; 
	
	public $_validate=array(
		array('intro','require','内容不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>