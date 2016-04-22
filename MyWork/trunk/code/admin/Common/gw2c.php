<?php
class GW2C {
	static public $ACT_OPEN_NEWS = '1';
	static public $ACT_OPEN_MOVIE = '2';
	static public $ACT_OPEN_ADV = '3';
	static public $ACT_OPEN_TOPIC = '4';
	static public $ACT_OPEN_USERCENTER = '5';
	static public $ACT_OPEN_URL = '11';
	
	/**
	 * 获取gw2c 协议字符串
	 * Enter description here ...
	 * @param $act_open
	 * @param array $param
	 */
	public function getScript($act_open,$param){
		$rst = array('v'=>'1','a'=>$act_open.'','p'=>$param);
		return json_encode($rst);
	}
	
	
}