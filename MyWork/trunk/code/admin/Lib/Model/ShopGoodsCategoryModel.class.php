<?php
// 商品分类模型
class ShopGoodsCategoryModel extends CommonModel {
	
	protected $trueTableName = 'shop_goods_category'; 
	
	public $_validate=array(
		array('name','require','名称不能为空')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>