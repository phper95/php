<?php
// 商品图片模型
class ShopGoodsImageModel extends CommonModel {
	
	protected $trueTableName = 'shop_goods_images'; 
	
	public $_validate=array(
			array('g_id','require','商品ID不能为空'),
			array('url','require','图片地址不能为空'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>