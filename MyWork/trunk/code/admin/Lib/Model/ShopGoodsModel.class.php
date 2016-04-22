<?php
// 商品模型
class ShopGoodsModel extends CommonModel {
	
	protected $trueTableName = 'shop_goods'; 
	
	public $_validate=array(
		array('name','require','名称不能为空'),
		array('desc','require','描述不能为空'),
	);
	
	public function execGoodsId($xx){
		//逐个解码
		$str = NOW_TIME.'';
		$key = '3LRJS5DHTK8NZGCAE972UMPX6VF14YDW';
		$Code = '';
		for($i = 0,$len=strlen($str); $i < $len; $i ++) {
			$font = substr ( $str, $i, 1 );
			$min = intval($font)*3;
			$max = $min+2;
			if ($font == '9') {$max = strlen($key)-1;}
			$rand = rand($min,$max);
			$font = $key[$rand];
			$Code .= $font;
		}
		return 'GM0X1'.$Code;
	}
	public $_auto=array(
		array('goods_id','execGoodsId',self::MODEL_INSERT,'callback'),
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>