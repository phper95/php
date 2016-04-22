<?php
function formartDate($time=NOW_TIME,$formart = 'Y-m-d H:i:s'){
	if (!is_numeric($time)) {
		$time = strtotime($time);
	}
	return date($formart,$time);
}

/**
 * 生成订单号
 * @param unknown $xx 前缀
 * @return string
 */
function execOrderId($xx = 'D'){
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
	return $xx.$Code;
}